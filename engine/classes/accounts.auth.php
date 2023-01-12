<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Authentication
{
    private $core;
    private $emulatorAuth;
    
 	//Constructor
	public function __construct()
	{
        $this->core =& get_instance();
        $this->emulatorAuth = null;
        $this->loadEmulatorAuthentication();
    }

    public function UserCheck()
	{
		//If we are not logged in	
    	if (!isset($_SESSION['uid']) || !isset($_SESSION['pass']))
		{
    		return false;
		}
	
		//get the user id if set
    	$id = 0 + (int)$_SESSION['uid'];
	
		//if there is no id
    	if (!$id)
		{
    		return false;
		}
		
        //get the account record
        $row = $this->emulatorAuth->getAccountById($id);

		//If user with that ID actually exists else empty session
    	if (!$row)
		{
			$_SESSION = array();
    		return false;
		}
	
		//check user pass 
    	if (strtolower($_SESSION['pass']) !== strtolower($row['hash']))
		{
			$_SESSION = array();
    		return false;
		}

		//let's add some security to the session
    	$ss = new Secure(true, 2);

		//if the session is stolen we empty it
    	if (!$ss->check())
		{
			$_SESSION = array();
			return false;
		}
    	unset($ss);
		
		//find the webiste record
		$res = $this->core->db->prepare("SELECT * FROM `account_data` WHERE `id` = :id LIMIT 1");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		$webRow = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
		
		//create new translated row
		$newRow['id'] = $row['id'];
        $newRow['identity'] = $row['identity'];
        $newRow['email'] = $row['email'];
        $newRow['reg_mail'] = $row['reg_mail'];
        $newRow['shapasshash'] = $row['hash'];
        $newRow['joindate'] = $row['joindate'];
		$newRow['expansion'] = $row['expansion'];
		$newRow['recruiter'] = $row['recruiter'];
		
		//merge the website row with the newly made auth row
		if ($webRow)
		{
			$newRow = array_merge($newRow, $webRow);
		}
				
		//set the CMS database accounts_more record of this user		
		$this->core->user->InitializeUser($newRow);
	
		//free the result and unset the row	 
		unset($row);
        unset($newRow); 
        unset($webRow);

        return true;
    }
    
    public function RememberMeCheck()
	{
		$rememberMeCookieIdentity = $this->core->getCookie('rmm_identity');
        $rememberMeCookieSecret = $this->core->getCookie('rmm_secret');
        
		if ($rememberMeCookieIdentity && $rememberMeCookieSecret && !isset($_SESSION['logged']))
		{
			//-do cookie login values
			$cookieUser = strtoupper($rememberMeCookieIdentity);
			$cookieHash = $rememberMeCookieSecret;
			
			unset($rememberMeCookieIdentity, $rememberMeCookieSecret);
			
            // Get account record
			$acc = $this->emulatorAuth->getAccountByIdentity($cookieUser);
            
            if ($acc !== false)
            {
				//Get the user account salt
				$saltRes = $this->core->db->prepare("SELECT `salt` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
				$saltRes->bindParam(':acc', $acc['id'], PDO::PARAM_INT);
				$saltRes->execute();
				
				if ($saltRes->rowCount() > 0)
				{
					$web = $saltRes->fetch(PDO::FETCH_ASSOC);
					
					if ($web['salt'] != '')
					{
						//match the cookie hash
						$hashCheck = sha1($acc['hash'] . $web['salt']);
						
						if ($hashCheck === $cookieHash)
						{
							//Login the user
							$this->core->user->setLoggedIn($acc['id'], $acc['hash']);
							
							// Also update the recruit link state
							// check if we have recruiter
							if ($acc['recruiter'] > 0)
							{
								//find the record
								$res2 = $this->core->db->prepare("SELECT * FROM `raf_links` WHERE `account` = :acc AND `recruiter` = :rec LIMIT 1;");
								$res2->bindParam(':acc', $acc['id'], PDO::PARAM_INT);
								$res2->bindParam(':rec', $acc['recruiter'], PDO::PARAM_INT);
								$res2->execute();
								
								//check if we have the link
								if ($res2->rowCount() > 0)
								{
									//fetch
									$raf_row = $res2->fetch();
									
									//check if the link status is pending
									if ($raf_row['status'] == RAF_LINK_ACTIVE)
									{
										//the link is active save that info to the CURUSER class
										$this->core->user->setRecruiterLinkState(RAF_LINK_ACTIVE);
									}
									
									unset($raf_row);
								}
								unset($res2);
							}
						}
					}
					unset($web, $acc, $hashCheck);
				}
			}
			unset($cookieUser, $cookieHash);
		}
		unset($rememberMeCookieIdentity, $rememberMeCookieSecret);
    }
    
    /**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __call($method, $arguments)
	{
		if (!method_exists($this->emulatorAuth, $method))
		{
			throw new Exception('Undefined method Authentication::' . $method . '() called');
		}

		return call_user_func_array(array($this->emulatorAuth, $method), $arguments);
    }
    
    /**
	 * Create a new user accpimt
	 * @param String $username
     * @param String $password
     * @param String $email
     * @param String $recruiter
	 * @return Int The account record ID or bool false
	 */
    public function register($username, $password, $email, $recruiter = 0)
	{
        return $this->emulatorAuth->register($username, $password, $email, $recruiter);
    }
    
    /**
	 * Get the emulator authentication class object
	 * @return Object
	 */
    private function loadEmulatorAuthentication()
    {
        if ($this->emulatorAuth != null)
        {
            return $this->authentication;
        }

        // The emulator authentication interface
        require_once ROOTPATH . '/engine/emulators/interfaces/authentication.php';

        // Resolve the authentication emulator
        $emulator = 'trinity'; // by default

        if ($this->core->configItem('emulator', 'authentication') !== false)
        {
            $emulator = $this->core->configItem('emulator', 'authentication');
        }

        $filePath = ROOTPATH . '/engine/emulators/' . $emulator . '/authentication.php';

        if (file_exists($filePath))
        {
            require_once $filePath;

            $className = $emulator . '_Authentication';

            if (class_exists($className))
            {
                $this->emulatorAuth = new $className();

                return $this->emulatorAuth;
            }
            else
            {
                die ('Failed to load the authentication class [' . $className . '] for emulator ' . $emulator . '.');
            }
        }
        else
        {
            die ('Failed to load the authentication class for emulator ' . $emulator . '.');
        }
    }
}