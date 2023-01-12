<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Recovery extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loadConfig('recaptcha');
    }
    
    public function password()
    {
        $this->tpl->SetTitle('Password Recovery');
        $this->tpl->SetSubtitle('Password Recovery');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('recovery/password');

        $this->tpl->LoadFooter();
    }

    public function submit_password()
    {
        //if the user is already logged in return him to index
        if ($this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            exit;
        }

        //Load the Tokens lib
        $this->loadLibrary('tokens');

        //define the email reward token lifetime
        $emailTokenLifetime = '24 hours';

        //the event we should apply to the account
        $event = 'PASSWORD_RECOVERY_PENDING';

        //Get variables
        $email = isset($_POST['email']) ? trim($_POST['email']) : false;

        if ($email)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        }

        //setup new instance of multiple errors
        $this->errors->NewInstance('password_recovery');

        //missing inputs check
        if (!$email)
        {
            $this->errors->Add('Please enter your E-mail Address.');
        }

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
        $this->errors->Check('/recovery/password');

        // Get the account record
        $row = $this->authentication->getAccountByEmail($email);

        if ($row === false && $email !== false)
        {
            $this->errors->Add('Incorrent E-Mail address. Please make sure you enter the correct E-Mail address of your account.');
        }

        //Check for errors
        $this->errors->Check('/recovery/password');

        //assume failure
        $success = false;

        //Let's setup our token
        $token = new Tokens();

        //Set the account ID to be included as salt
        $token->setIdentifier($row['id']);

        //Set the application string so the token is only valid for this app
        $token->setApplication('PRECOVER');

        //Set the token expiration time
        $token->setExpiration($emailTokenLifetime);

        //Save the user ID under the token
        $token->setExternalData($row);

        //Generate a key for the token
        $token->generateKey();

        //get the encoded key
        $key = $token->getKey();

        //register the token
        $tokenReg = $token->registerToken();

        //continue only if the key was successfully registered
        if ($tokenReg === true)
        {
            //update the account event
            $update = $this->db->prepare("UPDATE `account_data` SET `event` = UPPER(:event) WHERE `id` = :id LIMIT 1;");
            $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
            $update->bindParam(':event', $event, PDO::PARAM_STR);
            $update->execute();
            unset($update);
            
            ############################################################################
            ## Not it's time to send the revocery mail
            $this->loadLibrary('phpmailer');
            
            //setup the PHPMailer class
            $mail = new PHPMailer();
            $mail->IsMail();
            $mail->From = $this->config['Email'];
            $mail->FromName = $this->config['SiteName'];
            
            //get the message html
            $message = file_get_contents(ROOTPATH . '/resources/mails/recovery_mail.html');
                    
            //If for some reason we couldnt get the mail HTMl send blank with a key
            if (!$message)
            {
                $message = base_url() . '/recovery/password_verify?key=' . $key;
            }
            else
            {
                //Get the user display name
                $res = $this->db->prepare("SELECT `displayname` FROM `account_data` WHERE `id` = :id LIMIT 1;");
                $res->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $res->execute();
                
                if ($res->rowCount() > 0)
                {
                    $drow = $res->fetch();
                    $displayname = $drow['displayname'];
                    unset($drow);
                }
                else
                {
                    $displayname = 'Unknown';
                }
                unset($res);
                
                //replace the tags with info
                $search = array('{DISPLAY_NAME}', '{URL}', '{SITE_NAME}');
                $replace = array($displayname, base_url() . '/recovery/password_verify?key=' . $key, $this->config['SiteName']);
                $message = str_replace($search, $replace, $message);
                unset($search, $replace);
            }
            
            //By now we should have the mail message
            $mail->AddAddress($email);

            $mail->WordWrap = 50;
            $mail->IsHTML(true);
            
            $mail->Subject = $this->config['SiteName'] . " Password Recovery";
            $mail->Body    = $message;
            
            //check if the message was sent
            if ($mail->Send())
            {
                $success = true;
            }
            else
            {
                $this->errors->Add('Failed to send password recovery mail. Please contact the administration.');
            }
            
            //unset them variables
            unset($displayname, $message, $mail);
        }
        else
        {
            $this->errors->Add('Failed to send password recovery mail. Please contact the administration.');
        }

        //Unset some variables
        unset($tokenReg, $key, $token, $event, $emailTokenLifetime, $row, $email);

        //Check for errors
        $this->errors->Check('/recovery/password');

        //handle the success
        if ($success)
        {
            //Setup our welcoming notification
            $this->notifications->SetTitle('Password Recovery');
            $this->notifications->SetHeadline('One step away!');
            $this->notifications->SetText('We have sent you a mail containing the instructions to complete the recovery process.<br>This process may only be completed in the next 24 hours.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            header("Location: ".base_url()."/home");
        }
        exit;
    }

    public function password_verify()
    {
        $key = isset($_GET['key']) ? $_GET['key'] : false;

        //check if the key is set
        if (!$key)
        {
            header("Location: ".base_url()."/recovery/password");
            die;
        }

        //load the Tokens lib
        $this->loadLibrary('tokens');
                        
        //construct
        $token = new Tokens();
        
        //Set the application string so the token is only valid for this app
        $token->setApplication('PRECOVER');
        
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
            
            header("Location: ".base_url()."/recovery/password");
            die;
        }
        unset($token, $TokenValidation);

        //the token is valid, save the token for the execute
        $_SESSION['P_Recovery_Token'] = $key;

        $this->tpl->SetTitle('Password Recovery');
        $this->tpl->SetSubtitle('Password Recovery');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('recovery/password_verify');

        $this->tpl->LoadFooter();
        exit;
    }

    public function password_finish()
    {
        //Load the Tokens lib
        $this->loadLibrary('tokens');
        $this->loadLibrary('accounts.register');
        
        //setup new instance of multiple errors
        $this->errors->NewInstance('password_recovery');

        //Define the variables
        $password = isset($_POST['password']) ? $_POST['password'] : false;
        $password2 = isset($_POST['password2']) ? $_POST['password2'] : false;
        $key = isset($_SESSION['P_Recovery_Token']) ? $_SESSION['P_Recovery_Token'] : false;

        //construct
        $token = new Tokens();

        //Set the application string so the token is only valid for this app
        $token->setApplication('PRECOVER');
        
        if ($passwordError = AccountsRegister::checkPassword($password, $password2))
        {
            $this->errors->Add($passwordError);
        }
        $password = trim($password);

        //Check if the key is set and valid
        if (!$key || $token->setKey($key) !== true)
        {
            //Setup our notification
            $this->notifications->SetTitle('Notification');
            $this->notifications->SetHeadline('Error!');
            $this->notifications->SetText('Invalid security token.<br>Please open your your e-mail and follow the instruction we have sent you.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            header("Location: ".base_url()."/recovery/password");
            die;
        }
        
        //Check for errors
        $this->errors->Check('/recovery/password_verify?key='.$key);

        //Get the external data for the token
        $row = $token->getExternalData();
        
        //make our new pass verifier
        $verifier = $this->authentication->makeVerifier($row['identity'], $password, $row['salt']);
        
        //check if the account was affected
        if ($this->authentication->changePassword($row['id'], $row['identity'], $password, $row['salt']))
        {
            //Destroy this token
            $token->destroyToken();
            
            //unset the class
            unset($token);

            //update the account event
            $update = $this->db->prepare("UPDATE `account_data` SET `event` = '' WHERE `id` = :id LIMIT 1;");
            $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
            $update->execute();
            unset($update);
            
            //Setup our notification
            $this->notifications->SetTitle('Password Recovery');
            $this->notifications->SetHeadline('Congratulations!');
            $this->notifications->SetText('Your account password has been updated.<br>Please enjoy your stay.');
            $this->notifications->SetTextAlign('center');
            //$this->notifications->SetAutoContinue(true);
            //$this->notifications->SetContinueDelay(5);
            $this->notifications->Apply();
            
            // LOGIN
            $this->user->setLoggedIn($row['id'], $verifier);

            AccountActivity::Insert('Recovered account password', $row['id']);

            header("Location: ".base_url()."/home");
            exit;
        }
        else
        {
            $this->errors->Add('The website failed to change your account password. Please contact the administration.');
        }

        $this->errors->Check('/recovery/password_verify?key='.$key);
        exit;
    }
}