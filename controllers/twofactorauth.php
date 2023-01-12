<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Twofactorauth extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadLibrary('accounts.activity');
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Two-factor authentication');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('twofactorauth/twofactorauth');

        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        //Load the two-factor auth lib
        $this->loadLibrary('accounts.2fa');

        //setup new instance of multiple errors
        $this->errors->NewInstance('twofactorauth');

        //Get variables
        $email_2fa = isset($_POST['email']) ? (int)$_POST['email'] : 0;

        if ($email_2fa == 1 && (int)$this->user->get('twofactor_email') == 1)
        {
            $this->errors->Add('Two-factor authentication via e-mail is already active.');
        }

        //Check for errors
        $this->errors->Check('/twofactorauth');

        //Let's setup our token
        $TwoFactorAuth = new TwoFactorAuthentication();

        // Check if we are activating two-factor auth for email
        if ($email_2fa == 1)
        {
            //register the token
            $activateKey = $TwoFactorAuth->registerKey($this->user->get('id'));

            //continue only if the key was successfully registered
            if ($activateKey !== false)
            {
                // Now it's time to send the revocery mail
                $this->loadLibrary('phpmailer');
                
                //setup the PHPMailer class
                $mail = new PHPMailer();
                $mail->IsMail();
                $mail->From = $this->config['Email'];
                $mail->FromName = $this->config['SiteName'];
                
                //get the message html
                $message = file_get_contents(ROOTPATH . '/resources/mails/twofactor_activate_mail.html');
                        
                //If for some reason we couldnt get the mail HTMl send blank with a key
                if (!$message)
                {
                    $message = base_url() . '/twofactorauth/activate?verify=1&key=' . $activateKey;
                }
                else
                {
                    //replace the tags with info
                    $search = array('{DISPLAY_NAME}', '{URL}', '{SITE_NAME}');
                    $replace = array($this->user->get('displayName'), base_url() . '/twofactorauth/activate?verify=1&key=' . $activateKey, $this->config['SiteName']);
                    $message = str_replace($search, $replace, $message);
                    unset($search, $replace);
                }
                
                //By now we should have the mail message
                $mail->AddAddress($this->user->get('email'));

                $mail->WordWrap = 50;
                $mail->IsHTML(true);
                
                $mail->Subject = $this->config['SiteName'] . " Two-factor Authentication";
                $mail->Body    = $message;
                
                //check if the message was sent
                if ($mail->Send())
                {
                    //Setup notification
                    $this->notifications->SetTitle('Two-factor Authentication');
                    $this->notifications->SetHeadline('One step away!');
                    $this->notifications->SetText('We have sent you an email containing the instructions to activate two-factor authentication.<br>This process may only be completed in the next 24 hours.');
                    $this->notifications->SetTextAlign('center');
                    //$this->notifications->SetAutoContinue(true);
                    //$this->notifications->SetContinueDelay(5);
                    $this->notifications->Apply();
                    
                    header("Location: ".base_url()."/home");
                    die;
                }
                
                //unset them variables
                unset($message, $mail);
            }
            else
            {
                $this->errors->Add('Failed to send two-factor authentication activation email. Please contact the administration.');
            }

            unset($activateKey);
        }
        else
        {
            AccountActivity::Insert('Disabled two-factor authentication via e-mail');

            // Deactivate two-factor authentication via email
            $this->user->Update(array('twofactor_email' => 0));

            //redirect
            $this->errors->onSuccess('Two-factor authentication via e-mail has been disabled.', '/twofactorauth');
            $this->errors->triggerSuccess();
        }

        //Unset some variables
        unset($TwoFactorAuth);

        //Check for errors
        $this->errors->Check('/twofactorauth');
        exit;
    }

    public function activate()
    {
        //Load the two-factor auth lib
        $this->loadLibrary('accounts.2fa');

        //define the email lifetime
        $emailLifetime = '+1 day';

        //Get variables
        $key = isset($_GET['key']) ? (int)$_GET['key'] : false;

        //Let's setup our token
        $TwoFactorAuth = new TwoFactorAuthentication();

        //Prepare some variables
        $headline = '';
        $message = '';

        if ($key !== false)
        {
            $validate = $TwoFactorAuth->validateKey($key, $this->user->get('id'), $emailLifetime);

            if ($validate !== false)
            {
                AccountActivity::Insert('Enabled two-factor authentication via e-mail');

                // Activate the two-factor via email auth
                $this->user->Update(array('twofactor_email' => 1));

                // Destroy all keys for the current user
                $TwoFactorAuth->destroyKeys($this->user->get('id'));

                $headline = 'Confirmed';
                $message = 'Two-factor authentication via e-mail has been enabled.';
            }
            else
            {
                $headline = 'Error';
                $message = 'Failed to activate two-factor authentication. Please try again.';
            }
        }
        else
        {
            $headline = 'Error';
            $message = 'Please use a valid activation link.';
        }

        unset($TwoFactorAuth);
        
        //Set the title
        $this->tpl->SetTitle('Two-factor Authentication');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('twofactorauth/activate', array('headline' => $headline, 'message' => $message));

        $this->tpl->LoadFooter();
    }
}