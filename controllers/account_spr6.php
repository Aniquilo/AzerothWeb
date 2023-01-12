<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Account extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadLibrary('accounts.activity');
        $this->loadConfig('premium_services');
        $this->loadConfig('boosts');
    }
    
    public function index()
    {
        $this->tpl->SetTitle(lang('title', 'account'));
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/account');

        $this->tpl->LoadFooter();
    }

    public function avatars()
    {
        $this->tpl->SetTitle('Avatars');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();
        
        //Print the page view
        $this->tpl->LoadView('account/avatars');

        $this->tpl->LoadFooter();
    }

    public function set_avatar()
    {
        $avatarId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($avatarId === false)
        {
            echo 'You must select an avatar first.';
            die;
        }

        $storage = new AvatarGallery();

        //validate the avatar
        $newAvatar = $storage->get($avatarId);

        if (!$newAvatar)
        {
            echo 'The selected avatar is invalid.';
            die;
        }

        unset($storage);

        //Let's validate the ranking requirements
        if ($newAvatar->rank() > $this->user->getRank()->int())
        {
            echo 'The selected avatar requires greater user rank.';
            die;
        }

        $userId = $this->user->get('id');
        $avatar = $newAvatar->int();
        $avatarType = $newAvatar->type();

        $update = $this->db->prepare("UPDATE `account_data` SET `avatar` = :avatar, `avatarType` = :type WHERE `id` = :account LIMIT 1;");
        $update->bindParam(':account', $userId, PDO::PARAM_INT);
        $update->bindParam(':avatar', $avatar, PDO::PARAM_INT);
        $update->bindParam(':type', $avatarType, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            AccountActivity::Insert('Updated avatar');

            echo 'OK';
        }
        else
        {
            echo 'The website failed to update your avatar.';
        }
        exit;
    }

    public function settings()
    {
        $this->tpl->SetTitle('Account Settings');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-account-settings.css');
        $this->tpl->LoadHeader();
        
        //Print the page view
        $this->tpl->LoadView('account/settings');

        $this->tpl->LoadFooter();
    }

    public function setup()
    {
        // Make sure we are allowed to access this page
        if (!isset($_SESSION['ACC_SETUP_PASS']) || !$_SESSION['ACC_SETUP_PASS'])
        {
            $this->tpl->Message('Account Setup', 'An error has occured!', 'You cannot access this page.');
            die;
        }

        $this->tpl->SetTitle('Account Setup');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/setup');

        $this->tpl->AddFooterJs('template/js/alertbox.js');
        $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function setup_submit()
    {
        // Make sure we are allowed to access this page
        if (!isset($_SESSION['ACC_SETUP_PASS']) || !$_SESSION['ACC_SETUP_PASS'])
        {
            $this->tpl->Message('Account Setup', 'An error has occured!', 'You cannot access this page.');
            die;
        }

        //load the register lib
        $this->loadLibrary('accounts.register');

        //setup new instance of multiple errors
        $this->errors->NewInstance('account_setup');

        //Define the variables
        $displayName = isset($_POST['displayname']) ? $_POST['displayname'] : false;
        
        if ($displayName)
        {
            $displayName = filter_var($displayName, FILTER_SANITIZE_STRING);
        }

        $birthdayMonth = isset($_POST['birthday']['month']) ? $_POST['birthday']['month'] : false;
        $birthdayDay = isset($_POST['birthday']['day']) ? $_POST['birthday']['day'] : false;
        $birthdayYear = isset($_POST['birthday']['year']) ? $_POST['birthday']['year'] : false;

        $country = isset($_POST['country']) ? $_POST['country'] : false;

        $secretQuestion = isset($_POST['secretQuestion']) ? (int)$_POST['secretQuestion'] : false;
        $secretAnswer = isset($_POST['secretAnswer']) ? $_POST['secretAnswer'] : false;

        if ($displaynameError = AccountsRegister::checkDisplayname($displayName))
        {
            $this->errors->Add($displaynameError);
        }

        /*
        //validate the Month
        if ($birthdayMonthError = AccountsRegister::checkBirthdayMonth($birthdayMonth))
        {
            $this->errors->Add($birthdayMonthError);
        }
        
        //validate the Day
        if ($birthdayDayError = AccountsRegister::checkBirthdayDay($birthdayDay))
        {
            $this->errors->Add($birthdayDayError);
        }

        //validate the Year
        if ($birthdayYearError = AccountsRegister::checkBirthdayYear($birthdayYear))
        {
            $this->errors->Add($birthdayYearError);
        }

        //add zero "0" to the day if it's not aready entered
        $dayLen = strlen($birthdayDay);
        if (($dayLen >= 1 and $dayLen <= 2) and ($birthdayDay >= 1 and $birthdayDay <= 31))
        {
            if ($dayLen == 1)
            {
                $birthdayDay = '0' . $birthdayDay;
            }
        }

        //merge the birthday
        $birthday = $birthdayMonth . '/' . $birthdayDay . '/' . $birthdayYear;

        if ($countryError = AccountsRegister::checkCountry($country))
        {
            $this->errors->Add($countryError);
        }
        */

        if ($secretQuestionError = AccountsRegister::checkSecretQuestion($secretQuestion))
        {
            $this->errors->Add($secretQuestionError);
        }
        
        if ($secretAnswerError = AccountsRegister::checkSecretAnswer($secretAnswer))
        {
            $this->errors->Add($secretAnswerError);
        }

        $secretAnswer = trim($secretAnswer);

        //Check for errors
        $this->errors->Check('/account/setup');

        ##################################################

        //hash the secret answer
        $aHash = sha1($secretQuestion . ':' . strtolower($secretAnswer));

        //Get first realm
        $FirstRealm = 1;
        $realmsConfig = $this->getRealmsConfig();
        if ($realmsConfig)
        {
            $FirstRealm = $realmsConfig[key($realmsConfig)];
        }
        unset($realmsConfig);

        // Update the record
        $update = $this->db->prepare("UPDATE `account_data` 
                                SET 
                                    `displayName` = :displayName, 
                                    `secretQuestion` = :secretQuestion, 
                                    `secretAnswer` = :secretAnswer, 
                                    `selected_realm` = :realm, 
                                    `event` = 'NONE' 
                                WHERE `id` = :accid 
                                LIMIT 1;");

        $userId = $this->user->get('id');

        $update->bindParam(':accid', $userId, PDO::PARAM_INT);
        $update->bindparam(':displayName', $displayName, PDO::PARAM_STR);
        //$update->bindParam(':birthday', $birthday, PDO::PARAM_STR);
        //$update->bindParam(':country', $country, PDO::PARAM_STR);
        $update->bindParam(':secretQuestion', $secretQuestion, PDO::PARAM_INT);
        $update->bindParam(':secretAnswer', $aHash, PDO::PARAM_STR);
        $update->bindParam(':realm', $FirstRealm, PDO::PARAM_INT);
        $update->execute();

        // Check if the update was successful
        if ($update->rowCount() > 0)
        {
            //Setup our welcoming notification
            $this->notifications->SetTitle('Notification');
            $this->notifications->SetHeadline('Success!');
            $this->notifications->SetText('Welcome back and thank you for updating your account information.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            unset($_SESSION['ACC_SETUP_PASS']);
            
            ######################################
            ########## Redirect ##################
            header("Location: ".base_url()."/home");
        }
        else
        {
            $this->errors->Add("The website failed to update your account record. Please contact the administration.");
        }

        $this->errors->Check('/account/setup');
        exit;
    }

    public function changepass()
    {
        $this->tpl->SetTitle('Change Password');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/changepass');

        $this->tpl->LoadFooter();
    }

    public function submit_changepass()
    {
        //setup new instance of multiple errors
        $this->errors->NewInstance('changepass');

        //bind the onsuccess message
        $this->errors->onSuccess('Your password has been successfully changed.', '/account/changepass');

        //Define the variables
        $password = isset($_POST['password']) ? $_POST['password'] : false;
        $newpassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : false;
        $newpassword2 = isset($_POST['newPassword2']) ? $_POST['newPassword2'] : false;

        //make the hash to check current pass
        $current_verifier = $this->authentication->makeVerifier($this->user->get('identity'), $password, $this->user->get('accsalt'));
        
        if (!$password)
        {
            //The current password is not defined
            $this->errors->Add('Please enter your current password.');
        }
        else if ($current_verifier != $this->user->get('verifier'))
        {
            //check if the password is valid
            $this->errors->Add('You\'ve entered wrong account password.');
        }
        
        if (!$newpassword)
        {
            //no new password
            $this->errors->Add('Please enter new password.');
        }
        else if (!$newpassword2)
        {
            //password not confirmed
            $this->errors->Add('Please confirm your new password.');
        }
        else if ($newpassword == $password)
        {
            //if the new pass and old pass are the same
            $this->errors->Add('Your new password is exactly the same as your old one.');
        }
        else if ($newpassword != $newpassword2)
        {
            //password do not match
            $this->errors->Add('You\'ve failed to confirm your new password.');
        }
        else if (strlen($newpassword) > 64)
        {
            //password too long
            $this->errors->Add('The new password is too long, maximum length 64.');
        }
        else if (strlen($newpassword) < 6)
        {
            //password too short
            $this->errors->Add('The new password is too short, minimum length 6.');
        }
            
        $newpassword = trim($newpassword);

        //Check for errors
        $this->errors->Check('/account/changepass');

        //make our new pass verifier
        $verifier = $this->authentication->makeVerifier($this->user->get('identity'), $newpassword, $this->user->get('accsalt'));
        
        //check if the account was affected
        if ($this->authentication->changePassword($this->user->get('id'), $this->user->get('identity'), $newpassword, $this->user->get('accsalt')))
        {
            AccountActivity::Insert('Changed account password');

            $this->user->setLoggedIn($this->user->get('id'), $verifier);
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to change your account password. Please contact the administration.');
        }

        $this->errors->Check('/account/changepass');
        exit;
    }

    public function changemail()
    {
        $this->tpl->SetTitle('Change E-mail Address');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/changemail');

        $this->tpl->LoadFooter();
    }

    public function submit_changemail()
    {
        $this->loadLibrary('email.reservation');

        //setup new instance of multiple errors
        $this->errors->NewInstance('changemail');

        //bind the onsuccess message
        $this->errors->onSuccess('Your E-mail Address was successfuly changed.', '/account/changemail');

        //Define the variables
        $email = isset($_POST['email']) ? $_POST['email'] : false;
        $secretQuestion = isset($_POST['secretQuestion']) ? (int)$_POST['secretQuestion'] : false;
        $secretAnswer = isset($_POST['secretAnswer']) ? trim($_POST['secretAnswer']) : false;

        if ($email)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        }

        //hash the secret answer
        $aHash = sha1($secretQuestion . ':' . strtolower($secretAnswer));
        
        if (!$secretAnswer)
        {
            //The current password is not defined
            $this->errors->Add('Please enter answer to your Secret Question.');
        }
        else if ($aHash != $this->user->get('secretAnswer'))
        {
            //check if the password is valid
            $this->errors->Add('You\'ve entered wrong Secret Question or Secret Answer.');
        }
        
        if (!$email)
        {
            //no new password
            $this->errors->Add('Please enter your new E-mail Address.');
        }
        else
        {
            //check for reservation
            if (EmailReservations::IsReserved(array('email' => $email)) === true)
            {
                $this->errors->Add('The e-mail address is reserved.');
            }
        }

        //Check for errors
        $this->errors->Check('/account/changemail');

        //check if the account was affected
        if ($this->authentication->changeEmail($this->user->get('id'), $email))
        {
            AccountActivity::Insert('Changed e-mail address to <b>'.$email.'</b>');

            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to change your E-mail Address. Please contact the administration.');
        }

        $this->errors->Check('/account/changemail');
        exit;
    }

    public function changedname()
    {
        $this->tpl->SetTitle('Change Display Name');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/changedname', array(
            'price_silver' => $this->configItem('DNChange_PriceSilver', 'premium_services'),
            'price_gold' => $this->configItem('DNChange_PriceGold', 'premium_services')
        ));

        $this->tpl->LoadFooter();
    }

    public function submit_changedname()
    {
        //load libs
        $this->loadLibrary('accounts.register');
        $this->loadLibrary('accounts.finances');
        $this->loadLibrary('purchaseLog');

        //setup new instance of multiple errors
        $this->errors->NewInstance('changedname');

        //bind the onsuccess message
        $this->errors->onSuccess('Your Display Name was successfuly changed.', '/account/changedname');

        //Define the variables
        $displayName = isset($_POST['displayName']) ? $_POST['displayName'] : false;
        $currency = isset($_POST['currency']) ? (int)$_POST['currency'] : false;

        if ($displayName)
        {
            $displayName = filter_var($displayName, FILTER_SANITIZE_STRING);
        }

        //Define the cost of display name change
        $PurchaseCost_Silver = (int)$this->configItem('DNChange_PriceSilver', 'premium_services');
        $PurchaseCost_Gold = (int)$this->configItem('DNChange_PriceGold', 'premium_services');

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare the log
        $logs = new purchaseLog();

        if (!$displayName)
        {
            //no new password
            $this->errors->Add('Please enter your new Display Name.');
        }
        else if ($displaynameError = AccountsRegister::checkDisplayname($displayName))
        {
            $this->errors->Add($displaynameError);
        }
        if (!$currency)
        {
            //no currency is selected
            $this->errors->Add('Please select a currency for the purchase.');
        }
        else if (!$finance->IsValidCurrency($currency))
        {
            //invalid currency
            $this->errors->Add('Error, invalid currency selected.');
        }
            
        //Check for errors
        $this->errors->Check('/account/changedname');

        ######### CHECK FINANCES #############
        $finance->SetCurrency($currency);
        $finance->SetAmount(($currency == CURRENCY_GOLD ? $PurchaseCost_Gold : $PurchaseCost_Silver));

        //check if the user has enough balance
        if ($BalanceError = $finance->CheckBalance())
        {
            if (is_array($BalanceError))
            {
                //insufficient amount
                foreach ($BalanceError as $currency)
                {
                    $this->errors->Add("You do not have enough " . ucfirst($currency) . " Coins.");
                }
            }
            else
            {
                //technical error
                $this->errors->Add('Error, the website failed to verify your account balance.');
            }
        }
        unset($BalanceError);

        //Check for errors
        $this->errors->Check('/account/changedname');
 
        //start logging
        $logs->add('CHANGE_DNAME', 'Display Name Change service. Using currency: '.$currency.'.', array(
            'selected_currency' => $currency,
            'price' => $currency == CURRENCY_GOLD ? $PurchaseCost_Gold : $PurchaseCost_Silver
        ));
        
        $userId = $this->user->get('id');

        //Apply the new display name to the account
        $update = $this->db->prepare("UPDATE `account_data` SET `displayName` = :name WHERE `id` = :acc LIMIT 1;");
        $update->bindParam(':name', $displayName, PDO::PARAM_STR);
        $update->bindParam(':acc', $userId, PDO::PARAM_INT);
        $update->execute();
            
        //check if the account was affected
        if ($update->rowCount() > 0)
        {
            //update the log
            $logs->update('The user\'s display name has been successfully changed.');
                
            //charge for the purchase
            $Charge = $finance->Charge("Display name change", CA_SOURCE_TYPE_NONE);
            
            if ($Charge === true)
            {
                //update the log
                $logs->update('The user has been charged for his purchase.', 'ok');
            }
            else
            {
                //update the log
                $logs->update('The user was not charged for his purchase, website failed to update.', 'error');
            }
            unset($Charge);
            
            //free up some memory
            unset($finance);
            
            AccountActivity::Insert('Changed display name to <b>'.$displayName.'</b>');

            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to change your Display Name. Please contact the administration.');
            //log
            $logs->update('The website failed to update the user\'s display name.', 'error');
        }

        $this->errors->Check('/account/changedname');
        exit;
    }

    public function activity()
    {
        $p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

        //load the pagination lib
        $this->loadLibrary('paginationType2');

        //Let's setup our pagination
        $pagination = new Pagination();
        $pages = false;
        $perPage = 8;
        $userId = $this->user->get('id');
        $results = false;

        //count the total records
        $res = $this->db->prepare("SELECT COUNT(*) FROM `account_activity` WHERE `account` = :acc;");
        $res->bindParam(':acc', $userId, PDO::PARAM_INT);
        $res->execute();

        $count_row = $res->fetch(PDO::FETCH_NUM);
        $count = $count_row[0];
                    
        unset($count_row);
        unset($res);

        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($count, $perPage, $p);
            
            //get the activity records
            $res = $this->db->prepare("SELECT * FROM `account_activity` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$pages['limit'].";");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            
            $results = $res->fetchAll();

            //loop the records
            foreach ($results as $i => $arr)
            {
                //format the time
                $results[$i]['time'] = get_datetime($arr['time'], 'd F Y, H:i:s');
            }

            if ($count <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle('Account Activity');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-activity-all.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/activity', array(
            'count' => $count,
            'pages' => $pages,
            'results' => $results
        ));

        $this->tpl->LoadFooter();
    }

    public function store_activity()
    {
        $p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

        //load the pagination lib
        $this->loadLibrary('paginationType2');

        //Let's setup our pagination
        $pagination = new Pagination();
        $pages = false;
        $perPage = 8;
        $results = false;
        $userId = $this->user->get('id');

        //count the total records
        $res = $this->db->prepare("SELECT COUNT(*) FROM `store_activity` WHERE `account` = :acc;");
        $res->bindParam(':acc', $userId, PDO::PARAM_INT);
        $res->execute();

        $count_row = $res->fetch(PDO::FETCH_NUM);
        $count = $count_row[0];
                    
        unset($count_row);
        unset($res);

        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($count, $perPage, $p);
            
            //get the activity records
            $res = $this->db->prepare("SELECT * FROM `store_activity` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$pages['limit'].";");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            
            $results = $res->fetchAll();

            //loop the records
            foreach ($results as $i => $arr)
            {
                //get the item record from the store
                $res2 = $this->db->prepare("SELECT `entry`, `name`, `Quality`, `realm` FROM `store_items` WHERE `id` = :id LIMIT 1;");
                $res2->bindParam(':id', $arr['itemId'], PDO::PARAM_INT);
                $res2->execute();
                
                //check if we have found the item
                if ($res2->rowCount() > 0)
                {
                    $item = $res2->fetch();
                }
                else
                {
                    //that's the array for missing item
                    $item = array('entry' => 0, 'name' => 'Unknown', 'Quality' => '0');
                }
                unset($res2);

                //format the time
                $results[$i]['time'] = get_datetime($arr['time'], 'd F Y, H:i:s');
                
                $realmId = 0;
                if (strpos($item['realm'], ',') !== false)
                {
                    $realmId = substr($item['realm'], 0, strpos($item['realm'], ','));
                }
                else
                {
                    $realmId = $item['realm'];
                }

                $results[$i]['realmId'] = $realmId;
                $results[$i]['item'] = $item;
            }

            if ($count <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle('Store Activity');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-activity-all.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/store_activity', array(
            'count' => $count,
            'pages' => $pages,
            'results' => $results
        ));

        $this->tpl->LoadFooter();
    }

    public function coin_activity()
    {

        $p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

        //load the pagination lib
        $this->loadLibrary('paginationType2');

        //Let's setup our pagination
        $pagination = new Pagination();
        $pages = false;
        $perPage = 8;
        $results = false;
        $userId = $this->user->get('id');

        //count the total records
        $res = $this->db->prepare("SELECT COUNT(*) FROM `coin_activity` WHERE `account` = :acc;");
        $res->bindParam(':acc', $userId, PDO::PARAM_INT);
        $res->execute();

        $count_row = $res->fetch(PDO::FETCH_NUM);
        $count = $count_row[0];
                    
        unset($count_row);
        unset($res);

        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($count, $perPage, $p);
            
            //get the activity records
            $res = $this->db->prepare("SELECT * FROM `coin_activity` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$pages['limit'].";");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            
            $results = $res->fetchAll();

            //loop the records
            foreach ($results as $i => $arr)
            {
                //check the source type
                switch ($arr['sourceType'])
                {
                    case CA_SOURCE_TYPE_PURCHASE:
                        $sourceType = '<i>Purchased</i> ';
                        break;
                    case CA_SOURCE_TYPE_REWARD:
                        $sourceType = '<i>Reward</i> ';
                        break;
                    case CA_SOURCE_TYPE_DEDUCTION:
                        $sourceType = '<i>Deducted</i> ';
                        break;
                    case CA_SOURCE_TYPE_NONE:
                    default:
                        $sourceType = '';
                        break;
                }
                
                //check the coins type
                switch ($arr['coinsType'])
                {
                    case CA_COIN_TYPE_SILVER:
                        $coinType = 'Silver coins';
                        break;
                    case CA_COIN_TYPE_GOLD:
                        $coinType = 'Gold coins';
                        break;
                    default:
                        $coinType = 'Unknown coins';
                        break;
                }
                
                //check the exchange type
                switch ($arr['exchangeType'])
                {
                    case CA_EXCHANGE_TYPE_MINUS:
                        $exchangeType = '- ';
                        break;
                    case CA_EXCHANGE_TYPE_PLUS:
                    default:
                        $exchangeType = '';
                        break;
                }
                
                //format the time
                $results[$i]['time'] = get_datetime($arr['time'], 'd F Y, H:i:s');
                $results[$i]['sourceType'] = $sourceType;
                $results[$i]['coinType'] = $coinType;
                $results[$i]['exchangeType'] = $exchangeType;
            }

            if ($count <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle('Coin Activity');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-activity-all.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('account/coin_activity', array(
            'count' => $count,
            'pages' => $pages,
            'results' => $results
        ));

        $this->tpl->LoadFooter();
    }

    public function set_realm()
    {
        //prepare multi errors
        $this->errors->NewInstance('setrealm');

        $RealmId = isset($_POST['realm']) ? (int)$_POST['realm'] : false;

        if (!$RealmId)
        {
            $this->errors->Add("Please select a realm first.");
        }

        //Validate the relam
        if (!$this->realms->realmExists($RealmId))
        {
            $this->errors->Add("The selected realm is invalid.");
        }

        $this->errors->Check('/account');

        ####################################################################
        ## The actual unstuck script begins here
            
        //bind the onsuccess message
        $this->errors->onSuccess('<strong>' . $this->realms->getRealm($RealmId)->getName() . '</strong> was successfully set as operating realm.', '/account');
        
        $userId = $this->user->get('id');

        //Set the realm
        $update = $this->db->prepare("UPDATE `account_data` SET `selected_realm` = :realm WHERE `id` = :acc LIMIT 1;");
        $update->bindParam(':realm', $RealmId, PDO::PARAM_INT);
        $update->bindParam(':acc', $userId, PDO::PARAM_INT);
        $update->execute();
        
        //update the cooldown if the character was unstucked
        if ($update->rowCount() > 0)
        {
            //redirect
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to set your operating realm. Please try again later or contact the administration.');
        }
            
        ####################################################################

        $this->errors->Check('/account');
    }
}