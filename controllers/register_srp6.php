<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Register extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loadConfig('recaptcha');
    }
    
    public function index()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            exit();
        }

        //get raf hash
        $rafHash = isset($_GET['raf']) ? $_GET['raf'] : false;

        //Before loading any HTML i wanna check if we need to redirect to the Terms page before register
        //check if the Terms of Usage have been accpeted
        if (!isset($_SESSION['TermsAccepted']) or $_SESSION['TermsAccepted'] != true)
        {
            //save the page query
            $_SESSION['TermsReturn'] = base_url() . "/register" . ($rafHash ? '?raf='.$rafHash : '');

            //redirect
            header("Location: ".base_url()."/register/terms");
            die;
        }

        $this->tpl->SetTitle('Register new Account');
        $this->tpl->SetSubtitle('Register');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('register/register');

        $this->tpl->AddFooterJs('template/js/alertbox.js');
	    $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function terms()
    {
        $this->tpl->SetTitle('Terms of Use');
        $this->tpl->SetSubtitle('Register');
        $this->tpl->AddCSS('template/style/page-terms-of-use.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('register/terms');

        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            exit();
        }

        //setup new instance of multiple errors
        $this->errors->NewInstance('register');

        //load libs
        $this->loadLibrary('accounts.register');
        $this->loadLibrary('email.reservation');

        //Define the variables
        $identityColumn = ($this->configItem('bnet', 'authentication') ? 'email' : 'username');
        $identity = (isset($_POST[$identityColumn]) ? $_POST[$identityColumn] : false);
        
        if ($identity && $this->configItem('bnet', 'authentication'))
        {
            $identity = filter_var($identity, FILTER_SANITIZE_EMAIL);
        }
        else if ($identity)
        {
            $identity = filter_var($identity, FILTER_SANITIZE_STRING);
        }

        $displayName = isset($_POST['displayname']) ? $_POST['displayname'] : false;

        if ($displayName)
        {
            $displayName = filter_var($displayName, FILTER_SANITIZE_STRING);
        }

        $password = isset($_POST['password']) ? $_POST['password'] : false;
        $password2 = isset($_POST['password2']) ? $_POST['password2'] : false;

        $email = isset($_POST['email']) ? $_POST['email'] : false;

        if ($email)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        }

        $birthdayMonth = isset($_POST['birthday']['month']) ? $_POST['birthday']['month'] : false;
        $birthdayDay = isset($_POST['birthday']['day']) ? $_POST['birthday']['day'] : false;
        $birthdayYear = isset($_POST['birthday']['year']) ? $_POST['birthday']['year'] : false;

        $country = isset($_POST['country']) ? $_POST['country'] : false;

        $secretQuestion = isset($_POST['secretQuestion']) ? (int)$_POST['secretQuestion'] : false;
        $secretAnswer = isset($_POST['secretAnswer']) ? $_POST['secretAnswer'] : false;

        $rafHash = isset($_POST['raf']) ? $_POST['raf'] : false;

        //missing inputs check
        if (!$this->configItem('bnet', 'authentication'))
        {
            if ($usernameError = AccountsRegister::checkUsername($identity))
            {
                $this->errors->Add($usernameError);
            }
            $identity = trim($identity);
        }

        if ($displaynameError = AccountsRegister::checkDisplayname($displayName))
        {
            $this->errors->Add($displaynameError);
        }
        
        if ($passwordError = AccountsRegister::checkPassword($password, $password2))
        {
            $this->errors->Add($passwordError);
        }
        $password = trim($password);

        if ($emailError = AccountsRegister::checkEmail($email))
        {
            $this->errors->Add($emailError);
        }
        else
        {
            //check for reservation
            if (EmailReservations::IsReserved(array('email' => $email)) === true)
            {
                $this->errors->Add('The e-mail address is reserved.');
            }
        }
        $email = trim($email);

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

        if ($secretQuestionError = AccountsRegister::checkSecretQuestion($secretQuestion))
        {
            $this->errors->Add($secretQuestionError);
        }
        
        if ($secretAnswerError = AccountsRegister::checkSecretAnswer($secretAnswer))
        {
            $this->errors->Add($secretAnswerError);
        }

        $secretAnswer = trim($secretAnswer);

        // If recaptcha is enabled
        if ($this->configItem('enabled', 'recaptcha'))
        {
            $this->loadLibrary('recaptcha');

            $recaptchaInput = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : false;
            $recaptcha = new Recaptcha();
            $response = $recaptcha->verifyResponse($recaptchaInput);

            if (!isset($response['success']) || $response['success'] !== true)
            {
                $this->errors->Add("Please verify that you are not a robot.");
            }
            unset($recaptcha, $recaptchaInput, $response);
        }

        //Check for errors
        $this->errors->Check('/register'.($rafHash ? '?raf='.$rafHash : ''));

        ##################################################
        ######## REGISTER SERVER ACCOUNT #################

        $regData = array(
            'identity' => $identity,
            'password' => $password,
            'email' => $email,
            'displayName' => $displayName,
            'birthday' => $birthday,
            'country' => $country,
            'secretQuestion' => $secretQuestion,
            'secretHash' => sha1($secretQuestion . ':' . strtolower($secretAnswer)),
            'rafHash' => $rafHash
        );

        if ($this->configItem('activation', 'authentication'))
        {
            //Load the Tokens lib
            $this->loadLibrary('tokens');

            //Let's setup our token
            $token = new Tokens();

            //Set the application string so the token is only valid for this app
            $token->setApplication('REGISTER');

            //Set the token expiration time
            $token->setExpiration('24 hours');

            //Save the user ID under the token
            $token->setExternalData($regData);

            //Generate a key for the token
            $token->generateKey();

            //get the encoded key
            $key = $token->getKey();

            //register the token
            $tokenReg = $token->registerToken();

            //continue only if the key was successfully registered
            if ($tokenReg === true)
            {
                $this->loadLibrary('phpmailer');
                
                //setup the PHPMailer class
                $mail = new PHPMailer();
                $mail->IsMail();
                $mail->From = $this->config['Email'];
                $mail->FromName = $this->config['SiteName'];
                
                //get the message html
                $message = file_get_contents(ROOTPATH . '/resources/mails/activation_mail.html');
                        
                //If for some reason we couldnt get the mail HTMl send blank with a key
                if (!$message)
                {
                    $message = base_url() . '/register/activate?key=' . $key;
                }
                else
                {
                    //replace the tags with info
                    $search = array('{DISPLAY_NAME}', '{URL}', '{SITE_NAME}');
                    $replace = array($displayName, base_url() . '/register/activate?key=' . $key, $this->config['SiteName']);
                    $message = str_replace($search, $replace, $message);
                    unset($search, $replace);
                }
                
                //By now we should have the mail message
                $mail->AddAddress($email);

                $mail->WordWrap = 50;
                $mail->IsHTML(true);
                
                $mail->Subject = $this->config['SiteName'] . " Account Activation";
                $mail->Body    = $message;
                
                //check if the message was sent
                if ($mail->Send())
                {
                    //Setup our welcoming notification
                    $this->notifications->SetTitle('Notification');
                    $this->notifications->SetHeadline('Activation required!');
                    $this->notifications->SetText('Your '.$this->config['SiteName'].' account is almost ready for use.<br>Please visit your e-mail address to activate it.');
                    $this->notifications->SetTextAlign('center');
                    //$this->notifications->SetAutoContinue(true);
                    //$this->notifications->SetContinueDelay(5);
                    $this->notifications->Apply();
                    
                    ######################################
                    ########## Redirect ##################
                    header("Location: ".base_url()."/home");
                }
                else
                {
                    $this->errors->Add('Failed to send activation mail. Please contact the administration.');
                }
            }
            else
            {
                $this->errors->Add('Failed to send activation mail. Please contact the administration.');
            }
        }
        else
        {
            // Create the account straight away
            if ($this->process($regData))
            {
                // Redirect
                header("Location: ".base_url()."/home");
                exit;
            }
        }

        $this->errors->Check('/register'.($rafHash ? '?raf='.$rafHash : ''));
        exit;
    }

    public function activate()
    {
        $key = isset($_GET['key']) ? $_GET['key'] : false;

        //check if the key is set
        if (!$key)
        {
            header("Location: ".base_url()."/register");
            die;
        }

        //load the Tokens lib
        $this->loadLibrary('tokens');
                        
        //construct
        $token = new Tokens();
        
        //Set the application string so the token is only valid for this app
        $token->setApplication('REGISTER');
        
        //set and validate the token
        $TokenValidation = $token->setKey($key);

        //make sure the token checks out
        if ($TokenValidation !== true)
        {
            //Setup our notification
            $this->notifications->SetTitle('Notification');
            $this->notifications->SetHeadline('Error!');
            $this->notifications->SetText('Invalid security token.<br>Please open your e-mail and follow the instructions that we sent you.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            header("Location: ".base_url()."/register");
            die;
        }
        
        // Get the reg data
        $regData = $token->getExternalData();

        // Create the account
        if ($this->process($regData))
        {
            // Destroy the token
            $token->destroyToken();

            // Redirect
            header("Location: ".base_url()."/home");
            exit;
        }

        // Check for errors
        $this->errors->Check('/register');
        exit;
    }

    private function process($data)
    {
        $this->loadLibrary('raf');
        $raf = new RAF();

        //some default variables
        $recruiter = 0;

        //resolve the RAF acc ID
        if ($data['rafHash'])
        {
            if ($rafRow = $raf->FindHash($data['rafHash']))
            {
                $recruiter = $rafRow['account'];
            }
            unset($rafRow);
        }
            
        //register
        if ($accountId = $this->authentication->register($data['identity'], $data['password'], $data['email'], $recruiter))
        {
            //unset the terms variable
            unset($_SESSION['TermsAccepted']);
            
            //Get visitor's IP Address
            $ip = $this->security->getip();
            $thetime = $this->getTime();
            $regStatus = 'active';
            
            //Get first realm
            $FirstRealm = $this->realms->getFirstRealm()->getId();
            
            //insert web record
            $insert = $this->db->prepare("REPLACE INTO `account_data` (`id`, `displayName`, `birthday`, `country`, `secretQuestion`, `secretAnswer`, `last_ip`, `reg_ip`, `last_login`, `last_login2`, `status`, `selected_realm`) VALUES (:accid, :displayName, :birthday, :country, :secretQuestion, :secretAnswer, :lastip, :regip, :lastlogin2, :lastlogin2, :status, :realm);");
            $insert->bindParam(':accid', $accountId, PDO::PARAM_INT);
            $insert->bindparam(':displayName', $data['displayName'], PDO::PARAM_STR);
            $insert->bindParam(':birthday', $data['birthday'], PDO::PARAM_STR);
            $insert->bindParam(':country', $data['country'], PDO::PARAM_STR);
            $insert->bindParam(':secretQuestion', $data['secretQuestion'], PDO::PARAM_INT);
            $insert->bindParam(':secretAnswer', $data['secretHash'], PDO::PARAM_STR);
            $insert->bindParam(':lastip', $ip, PDO::PARAM_STR);
            $insert->bindParam(':regip', $ip, PDO::PARAM_STR);
            $insert->bindParam(':lastlogin2', $thetime, PDO::PARAM_STR);
            $insert->bindParam(':status', $regStatus, PDO::PARAM_STR);
            $insert->bindParam(':realm', $FirstRealm, PDO::PARAM_INT);
            $insert->execute();
            
            ######################################
            ############## RAF ###################
            //make a new raf link record because
            //we dont wanna query out auth databse 
            //too much with the website
            if ($data['rafHash'] && $recruiter > 0)
            {
                $raf->CreateLink($accountId, $recruiter);
            }
            
            ######################################
            ############ MAILING #################
            $this->loadLibrary('phpmailer');
            
            //setup the PHPMailer class
            $mail = new PHPMailer();
            $mail->IsMail();
            $mail->From = $this->config['Email'];
            $mail->FromName =  $this->config['SiteName'];
            $mail->AddAddress($data['email']);
            
            //get the message html
            $message = file_get_contents(ROOTPATH . '/resources/mails/register_mail.html');
                    
            //break if the function failed to laod HTML
            if ($message)
            {				
                //replace the tags with info
                $search = array('{USERNAME}', '{DISPLAYNAME}', '{SITE_NAME}');
                $replace = array($data['identity'], $data['displayName'], $this->config['SiteName']);
                $message = str_replace($search, $replace, $message);
                
                $mail->WordWrap = 50;
                $mail->IsHTML(true);
                
                $mail->Subject = $this->config['SiteName'] . " Registration";
                $mail->Body    = $message;
                //$mail->AltBody = "This is the body in plain text for non-HTML mail clients";

                $mail->Send();
            }

            ######################################
            ############# LOGIN ##################
            $getsalt = $this->authentication->getAccountById($accountId);
            $this->user->setLoggedIn($accountId, $this->authentication->makeVerifier($data['identity'], $data['password'], $getsalt['salt']));
            
            //Setup our welcoming notification
            $this->notifications->SetTitle('Notification');
            $this->notifications->SetHeadline('Congratulations!');
            $this->notifications->SetText('Welcome and thank you for joining the '.$this->config['SiteName'].' community.<br>Your '.$this->config['SiteName'].' account is active and ready for use.<br>Please enjoy.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            return true;
        }
        else
        {
            $this->errors->Add('Website Failure, it seems the website is not functioning at the moment. If this problem persists please contact the administration.');
        }

        //unset
        unset($raf);

        // In case of failure
        return false;
    }
}