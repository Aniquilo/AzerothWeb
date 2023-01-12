<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Login extends Core_Controller
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
            die();
        }

        //check if we've got return
        if (isset($_GET['return']))
        {
            $return = rawurldecode($_GET['return']);
            $_SESSION['url_bl'] = $return;
        }

        $this->tpl->SetTitle('Sign In');
        $this->tpl->SetSubtitle('Sign In');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('login/login');

        //print the footer
        $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            die();
        }

        //prepare multi errors
        $this->errors->NewInstance('login');
        
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

        $password = (isset($_POST['password']) ? $_POST['password'] : false);
        $rememberme = (isset($_POST['rememberme']) ? true : false);

        if (isset($_POST['url_bl']))
        {
            //check if it is valid URL
            if ($this->ValidateURLBeforeLogin($_POST['url_bl']))
            {
                $_SESSION['url_bl'] = $_POST['url_bl'];
            }
            unset($_POST['url_bl']);
        }

        if (!$identity)
        {
            $this->errors->Add("Please fill in your identity.");
        }
        if (!$password)
        {
            $this->errors->Add("Please enter your password.");
        }

        $this->errors->Check('/login');

        // Get the account record
        $row = $this->authentication->getAccountByIdentity($identity);

        //check if we have found the record
        if ($row !== false)
        {
            $accid = $row['id'];
            $accpasshash = $row['hash'];
            $accemail = $row['email'];
            $webRow = false;

            // Get some extra user info
            $res = $this->db->prepare("SELECT `status`, `last_ip`, `twofactor_email`, `salt`, `login_attempts` FROM `account_data` WHERE `id` = :account LIMIT 1;");
            $res->bindParam(':account', $accid, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $webRow = $res->fetch();
            }
            else
            {
                //try creating new record for this acc
                if (!$this->user->handle_MissingRecord($accid))
                {
                    $this->errors->Add("The account you are trying to access is broken. Please contact the administration.");
                }

                // Temporary
                $webRow = array(
                    'status' => 'active',
                    'last_ip' => $this->security->getip(),
                    'twofactor_email' => 0,
                    'salt' => null,
                    'login_attempts' => 0
                );
            }

            // If recaptcha is enabled
            if ($this->configItem('enabled', 'recaptcha') && (int)$webRow['login_attempts'] >= 3)
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

                $this->errors->Check('/login');
            }

            //make new pass hash
            $passcheck = $this->authentication->makehash($identity, $password);

            //compare the new pass hash with the one in the record
            if (strtoupper($accpasshash) == strtoupper($passcheck))
            { 
                // Check if the account has been disabled
                if ($webRow['status'] == 'disabled')
                {
                    $this->errors->Add("The account you are trying to access has been disabled.");
                }

                // Check if the account is not activated
                if ($webRow['status'] == 'pending')
                {
                    $this->errors->Add("The account you are trying to access is not activated. Please activate your account by checking your email.");
                }

                // Check for two-factor auth
                if ((int)$webRow['twofactor_email'] == 1)
                {
                    // Check if the last ip is different from this ip
                    if ($webRow['last_ip'] != $this->security->getip())
                    {
                        // Send code by email
                        //Load the two-factor auth lib
                        $this->loadLibrary('accounts.2fa');

                        //Let's setup our token
                        $TwoFactorAuth = new TwoFactorAuthentication();

                        //register the token
                        $loginCode = $TwoFactorAuth->registerKey($accid);

                        //continue only if the key was successfully registered
                        if ($loginCode !== false)
                        {
                            $TwoFactorAuth->emailKey($accemail, $loginCode);
                            
                            //Save user login details to session
                            $_SESSION['2fa_accid']    = $accid;
                            $_SESSION['2fa_identity'] = $identity;
                            $_SESSION['2fa_passhash'] = $passcheck;
                            $_SESSION['2fa_remember'] = $rememberme;

                            //redirect
                            header("Location: " . base_url() . "/login/code");
                            exit;
                        }
                        else
                        {
                            $this->errors->Add("The website failed to register two-factor authentication key. Please contact the administration.");
                        }
                    }
                }

                $this->errors->Check('/login');

                //make some logging
                $this->user->logInfoAtLogin($accid);
                
                //if the account is good to be logged in
                //Login the user
                $this->user->setLoggedIn($accid, $passcheck);
                
                //needed for the loginb page
                $_SESSION['JustLoggedIn'] = true;
                
                //Remember me
                if ($rememberme)
                {
                    $salt = null;

                    if ($webRow === false || $webRow['salt'] == null || $webRow['salt'] == '')
                    {
                        //Generate random salt
                        $salt = uniqid(mt_rand(), true);
                        
                        //store the salt
                        $update = $this->db->prepare("UPDATE `account_data` SET `salt` = :salt WHERE `id` = :acc LIMIT 1;");
                        $update->bindParam(':acc', $accid, PDO::PARAM_INT);
                        $update->bindParam(':salt', $salt, PDO::PARAM_STR);
                        $update->execute();
                    }
                    else
                    {
                        $salt = $webRow['salt'];
                    }
                    
                    //make the hash for the cookie
                    $newHash = sha1($accpasshash . $salt);
                    
                    //Remember the user for a month
                    $expire = strtotime('+1 month', time());
                    
                    //set the cookie
                    $this->setCookie('rmm_identity', $identity, $expire);
                    $this->setCookie('rmm_secret', $newHash, $expire);

                    //mem
                    unset($newHash, $expire, $salt);
                }
                        
                //redirect
                header("Location: " . base_url() . "/login/proceed");
                exit;
            }
            else
            {
                // Increase login attempts count
                $update = $this->db->prepare("UPDATE `account_data` SET `login_attempts` = `login_attempts` + 1 WHERE `id` = :account LIMIT 1;");
                $update->bindParam(':account', $accid, PDO::PARAM_INT);
                $update->execute();
                
                // On the session
                $_SESSION['login_attempts'] = (int)$webRow['login_attempts'] + 1;

                $this->errors->Add("The identity or password you have entered are incorrect.");
            }
        }
        else
        {
            $this->errors->Add("The identity or password you have entered are incorrect.");
        }

        $this->errors->Check('/login');
        exit;
    }

    public function proceed()
    {
        //check if we just had the login
        if (!isset($_SESSION['JustLoggedIn']))
        {
            header("Refresh: 0; url=".base_url()."/");
            exit();
        }

        $url = false;

        //check if we have URL the user wanted to access before we ask to login
        if (isset($_SESSION['url_bl']))
        {
            //check if it is valid URL
            if ($this->ValidateURLBeforeLogin($_SESSION['url_bl']))
            {
                $url = trim($_SESSION['url_bl']);
            }
            unset($_SESSION['url_bl']);
        }

        //default url
        if (!$url)
        {
            $url = base_url() . '/';
        }

        //Set the title
        $this->tpl->SetTitle('Sign In');
        $this->tpl->SetSubtitle('Sign In');
        $this->tpl->LoadHeader();

        //Print the view
        $this->tpl->LoadView('login/login_proceed');
	
        //unset the page pass
        unset($_SESSION['JustLoggedIn']);
        
        //Load the footer
        $this->tpl->LoadFooter();
        
        //Flush the page to the buffer
        $this->tpl->BufferFlush();
            
        //check for referral activations
        $this->loadLibrary('raf');
        
        //setup the raf class
        $raf = new RAF();
        
        /* The new way of activating RAF Links */
        
        //check if we have recruiter
        if ($this->user->get('recruiter') > 0)
        {
            $userId = $this->user->get('id');
            $recruiter = $userId = $this->user->get('recruiter');

            //find the record
            $res = $this->db->prepare("SELECT * FROM `raf_links` WHERE `account` = :acc AND `recruiter` = :rec LIMIT 1;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->bindParam(':rec', $recruiter, PDO::PARAM_INT);
            $res->execute();
            
            //check if we have the link
            if ($res->rowCount() > 0)
            {
                //fetch
                $row = $res->fetch();
                
                //check if the link status is pending
                if ($row['status'] == RAF_LINK_PENDING)
                {
                    //check for activation
                    //cooldowns
                    $cooldown = $this->user->getCooldown('RAF_REF_UP');
                    $cooldownTime = '15 minutes';
            
                    //check the cooldown, we dont want users to spamm our databases
                    if (!$cooldown || time() > $cooldown)
                    {
                        //define that we have not found a character yet
                        $found = false;

                        //define that we have not met the requirements for the status change
                        $requirementsMet = false;

                        //save the highest found character info
                        $highestLevel = 0;
                        $highestText = '';

                        //find the hightest level character in all the realms
                        //loop the realms
                        foreach ($this->getRealmsConfig() as $RealmId => $RealmData)
                        {
                            $realm = $this->realms->getRealm($RealmId);

                            //check if the characters database is reachable
                            if ($realm->checkCharactersConnection())
                            {
                                //now find it
                                if ($charRow = $realm->getCharacters()->FindHightestLevelCharacter($row['account']))
                                {
                                    $found = true;
                                    
                                    //check if the character meets the requirements
                                    if ($charRow['class'] == 6)
                                    {
                                        //if the character is DK
                                        if ($charRow['level'] >= 80)
                                        {
                                            //the character meets the requirements
                                            $requirementsMet = true;
                                        }
                                    }
                                    else
                                    {
                                        //any other class than DK
                                        if ($charRow['level'] >= 60)
                                        {
                                            //the character meets the requirements
                                            $requirementsMet = true;
                                        }
                                    }

                                    //if the character meet's the requirements
                                    if ($requirementsMet)
                                    {
                                        //update the status and statusText
                                        $statusText = '<b>'.$charRow['name'].'</b> '.$this->realms->getClassString($charRow['class']).' Level '.$charRow['level'];
                                        $status = RAF_LINK_ACTIVE;

                                        $cDate = $this->getTime();
                                        
                                        //query
                                        $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text, `status` = :status, `cDate` = :time WHERE `id` = :id LIMIT 1;");
                                        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
                                        $update->bindParam(':text', $statusText, PDO::PARAM_STR);
                                        $update->bindParam(':status', $status, PDO::PARAM_INT);
                                        $update->bindParam(':time', $cDate, PDO::PARAM_STR);
                                        $update->execute();
                                        unset($update);

                                        //the link is active save that info to the CURUSER class
                                        $this->user->setRecruiterLinkState(RAF_LINK_ACTIVE);

                                        //break the realm loop for this referral
                                        break 1;
                                    }

                                    if ($highestLevel < (int)$charRow['level'])
                                    {
                                        $highestLevel = (int)$charRow['level'];
                                        $highestText = '<b>'.$charRow['name'].'</b> '.$this->realms->getClassString($charRow['class']).' Level '.$charRow['level'];
                                    }
                                }
                                unset($charRow);
                            }
                        } //end of the realms loop

                        //if we found a character but not high enough level
                        if ($found && !$requirementsMet)
                        {
                            $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text WHERE `id` = :id LIMIT 1;");
                            $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
                            $update->bindParam(':text', $highestText, PDO::PARAM_STR);
                            $update->execute();
                            unset($update);
                        }

                        //if we had no characters for this referral update the status text
                        if (!$found)
                        {
                            $statusText = 'No character was found';
                            $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text WHERE `id` = :id LIMIT 1;");
                            $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
                            $update->bindParam(':text', $statusText, PDO::PARAM_STR);
                            $update->execute();
                            unset($update);
                        }
                        unset($found);
                        unset($requirementsMet);

                        //set a cooldown on this update
                        $this->user->setCooldown('RAF_REF_UP', strtotime('+'.$cooldownTime));
                    }
                    //here ends the IF Cooldown
                    unset($cooldown, $cooldownTime);
                }
                else if ($row['status'] == RAF_LINK_ACTIVE)
                {
                    //the link is active save that info to the CURUSER class
                    $this->user->setRecruiterLinkState(RAF_LINK_ACTIVE);
                }
                unset($row);
            }
            //IF the record is found ends here
            unset($res);
        }
        
        unset($raf);
        
        // Check for special account events
        if ($this->user->get('event') == 'EVENT_COPIED_ACCOUNT')
        {
            //Set the pass
            $_SESSION['ACC_SETUP_PASS'] = true;

            //redirect to the first time login page
            $url = base_url() . '/account/setup';
        }
        
        //redirect to the correct page
        echo '<meta http-equiv="refresh" content="1;URL=\'', $url, '\'">';
        exit;
    }

    public function code()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            die();
        }

        //Set the title
        $this->tpl->SetTitle('Sign In');
        $this->tpl->SetSubtitle('Sign In');
        
        //Print the header
        $this->tpl->LoadHeader();

        //Print the view
        $this->tpl->LoadView('login/login_code');
	
        //Load the footer
        $this->tpl->LoadFooter();
    }

    public function submit_code()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            exit();
        }

        //prepare multi errors
        $this->errors->NewInstance('login');
            
        $code = (isset($_POST['code']) ? (int)$_POST['code'] : false);

        if (!$code)
        {
            $this->errors->Add("Please enter the two-factor authentication code.");
        }

        $this->errors->Check('/login/code');

        if (!isset($_SESSION['2fa_accid']) || !isset($_SESSION['2fa_identity']) || !isset($_SESSION['2fa_passhash']) || !isset($_SESSION['2fa_remember']))
        {
            $this->errors->Add("Something went wrong, please try again.");
        }

        $this->errors->Check('/login');

        $accid = $_SESSION['2fa_accid'];
        $identity = $_SESSION['2fa_identity'];
        $passcheck = $_SESSION['2fa_passhash'];
        $rememberme = $_SESSION['2fa_remember'];

        //Load the two-factor auth lib
        $this->loadLibrary('accounts.2fa');

        //Let's setup our token
        $TwoFactorAuth = new TwoFactorAuthentication();

        $validateKey = $TwoFactorAuth->validateKey($code, $accid, '+5 minutes');

        if ($validateKey === false)
        {
            $this->errors->Add("Invalide two-factor authentication code, the code could be incorrect or expired.");
        }

        $this->errors->Check('/login/code');

        // Delete all two-factor auth keys
        $TwoFactorAuth->destroyKeys($accid);
        
        //make some logging
        if (!$this->user->logInfoAtLogin($accid))
        {
            //try creating new record for this acc
            if (!$this->user->handle_MissingRecord($accid))
            {
                $this->errors->Add("The account you are trying to access is broken. Please contact the administration.");
            }
        }

        $this->errors->Check('/login');

        //if the account is good to be logged in
        //Login the user
        $this->user->setLoggedIn($accid, $passcheck);
        
        //needed for the loginb page
        $_SESSION['JustLoggedIn'] = true;

        //Remember me
        if ($rememberme)
        {
            //Generate random salt
            $salt = uniqid(mt_rand(), true);
            
            //store the salt
            $update = $this->db->prepare("UPDATE `account_data` SET `salt` = :salt WHERE `id` = :acc LIMIT 1;");
            $update->bindParam(':acc', $accid, PDO::PARAM_INT);
            $update->bindParam(':salt', $salt, PDO::PARAM_STR);
            $update->execute();
            
            //make the hash for the cookie
            $newHash = sha1($passcheck . $salt);
            
            //Remember the user for a month
            $expire = strtotime('+1 month', time());
            
            //set the cookie
            $this->setCookie('rmm_identity', $identity, $expire);
            $this->setCookie('rmm_secret', $newHash, $expire);

            //mem
            unset($newHash, $expire, $value, $salt);
        }

        unset($_SESSION['2fa_accid'], $_SESSION['2fa_identity'], $_SESSION['2fa_passhash'], $_SESSION['2fa_remember']);

        //redirect
        header("Location: " . base_url() . "/login/proceed");
        exit;
    }
}