<?php
define('init_engine', true);

###################################################################################
## FILE INCLUSION #################################################################
###################################################################################

//constants
include_once ROOTPATH . '/engine/constants.php';

//config
require_once ROOTPATH . '/configuration/config.php';

//Set the error reporting
if (isset($config['DEBUG']) && $config['DEBUG'] === true)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(0);
}

//General Classes
require_once ROOTPATH . '/engine/common.php';
require_once ROOTPATH . '/engine/controller.php';
include_once ROOTPATH . '/engine/classes/cache.php';
include_once ROOTPATH . '/engine/classes/template.php';
include_once ROOTPATH . '/engine/classes/multipleError_handler.php';
include_once ROOTPATH . '/engine/classes/sessions.secure.php';
include_once ROOTPATH . '/engine/classes/rbac.role.php';
include_once ROOTPATH . '/engine/classes/accounts.auth.php';
include_once ROOTPATH . '/engine/classes/accounts.curuser.php';
include_once ROOTPATH . '/engine/classes/security.php';
include_once ROOTPATH . '/engine/classes/chmod.calc.php';
include_once ROOTPATH . '/engine/classes/notifications.php';
include_once ROOTPATH . '/engine/classes/permissions.php';
include_once ROOTPATH . '/engine/classes/realm.php';
include_once ROOTPATH . '/engine/classes/realms.php';
include_once ROOTPATH . '/engine/classes/language.php';
include_once ROOTPATH . '/engine/classes/visitor.tracker.php';
include_once ROOTPATH . '/engine/shutdown.php';
include_once ROOTPATH . '/engine/Item_Classes.php';

//storage variables
include_once ROOTPATH . '/engine/storages/boosts.php';
include_once ROOTPATH . '/engine/storages/rank_strings.php';
include_once ROOTPATH . '/engine/storages/avatars.php';
include_once ROOTPATH . '/engine/storages/countries.php';
include_once ROOTPATH . '/engine/storages/secret_questions.php';
include_once ROOTPATH . '/engine/storages/voteSites.php';
include_once ROOTPATH . '/engine/storages/tp.mapsInfo.php';
include_once ROOTPATH . '/engine/storages/tp.points.php';
include_once ROOTPATH . '/engine/storages/bt.categories.php';
include_once ROOTPATH . '/engine/storages/store_categories.php';

//We'll have some alternatives for the sessions
if (isset($config['SESSION_HANDLER']))
{
	if ($config['SESSION_HANDLER'] == 'MCRYPT')
	{
		include_once ROOTPATH . '/engine/classes/sessions.filesystem.php';
	}
	else
	{
		include_once ROOTPATH . '/engine/classes/sessions.none.php';
	}
}
else
{
	include_once ROOTPATH . '/engine/classes/sessions.none.php';
}

class CORE
{
    private static $instance;
    
    public      $controller;
    public      $method;
    protected   $config;
    protected   $Configs;
	private     $db;
    private     $auth_db;
    private     $data_db;
    private     $authentication;
    private     $session;
    private     $security;
    private     $user;
    private     $tpl;
    private     $lang;
    private     $errors;
    private     $notifications;
    private     $cache;
    private     $realms;

	public function __construct()
	{
		global $config;
        
        self::$instance =& $this;

        $this->config = $config;
        $this->Configs = array();
        $this->db = false;
        $this->auth_db = false;
        $this->data_db = false;

        //starting the session class and defining it
        $this->session = new Session();

        //setting up session handlers from our PHP Class sessions
        $this->session->register();

        //setup the security class
        $this->security = new Security();
        
        //Unregistring globals for security
        $this->security->unregisterGlobals();

        //filter the request methods
        $this->security->RestrictHttpMethods(array('POST', 'GET'));

        //check if the session has expired
        $this->security->CheckSessionLife();

        // Load some configs
        $this->loadConfig('authentication');
        $this->loadConfig('logon');
        $this->loadConfig('realms');

        // open database connections
        $this->DatabaseConnection();
        $this->AuthDatabaseConnection();

        // setup Current User class
        $this->user = new CURUSER();
        
        // Load the authentication class
        $this->authentication = new Authentication();

        // Do authentication related checks
        $this->authentication->RememberMeCheck();
        if (!$this->authentication->UserCheck())
        {
            // If there's no user
            $this->user->InitializeGuest();
        }
        
        // Setup the Template class
        $this->tpl = new Template();

        // Setup the Language class
        $this->lang = new Language();

        //setup the error handler
        $this->errors = new multipleErrors(get_class($this));
        
        //setup the Notifications class
        $this->notifications = new Notifications();
        
        //setup the Cache
        $this->cache = new Cache(array('repo' => ROOTPATH . '/cache'));

        //setup the realms
        $this->realms = new Realms();
    }
    
    public static function &get_instance()
	{
		return self::$instance;
    }
    
    public function __get($name)
    {
        switch ($name)
        {
            case 'db': return $this->db;
            case 'auth_db': return $this->auth_db;
            case 'data_db': return ($this->data_db ? $this->data_db : $this->DataDatabaseConnection());
            case 'session': return $this->session;
            case 'security': return $this->security;
            case 'authentication': return $this->authentication;
            case 'user': return $this->user;
            case 'tpl': return $this->tpl;
            case 'lang': return $this->lang;
            case 'notifications': return $this->notifications;
            case 'errors': return $this->errors;
            case 'cache': return $this->cache;
            case 'realms': return $this->realms;
        }

        if ($this->config['DEBUG'])
        {
            $trace = debug_backtrace();
            trigger_error('Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        }

        return null;
    }

    public function IsACP()
    {
        return false;
    }
	
	public function DatabaseConnection()
	{
		if (!$this->db)
		{
            $this->db = $this->PDOConnect($this->config['Website_DB']);

            if (!$this->db)
            {
                echo '<strong>Database Connection failed:</strong> Unable to connect to the Web Database.<br><br>';
                die;
            }
            
			//unset the config variables
			unset($this->config['Website_DB']);
		}
		
  	 	return $this->db;
	}
	
	public function AuthDatabaseConnection()
	{
		$auth_config = $this->loadConfig('authentication');
		
		if (!$this->auth_db)
		{
			try 
			{
				//Construct PDO
				$obj = new PDO('mysql:dbname='.$auth_config['DatabaseName'].'; host='.$auth_config['DatabaseHost'].'; port='.$auth_config['DatabasePort'].';', $auth_config['DatabaseUser'], $auth_config['DatabasePass'], NULL);
				
				//set error handler exception
				$obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
				
				//set default fetch method
				$obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
				
				//set encoding
				$obj->query("SET NAMES '".$auth_config['DatabaseEncoding']."'");
			}
			catch (PDOException $e)
			{
				echo '<strong>Database Connection failed:</strong> Unable to connect to the Authentication Database.<br><br>';
				
				if ($this->config['DEBUG'])
				{
					echo $e->getMessage();
				}
				die;
			}
			
			//save the database connection because we wont be able to open new one
			$this->auth_db = $obj;
		}
				
  	    return $this->auth_db;
    }

    public function DataDatabaseConnection()
	{
		//check if we have the connection stored
		if (!$this->data_db)
		{
            //store the newly made connection and return it
            $this->data_db = $this->PDOConnect($this->config['WoW_Data_DB']);
        }
        
		return $this->data_db;		
    }
    
    public function PDOConnect($config)
	{
        try 
        {
            //Construct PDO
            $obj = new PDO('mysql:dbname='.$config['name'].'; host='.$config['host'].'; port='.$config['port'].';', $config['user'], $config['pass'], NULL);
            
            //set error handler exception
            $obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            
            //set default fetch method
            $obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            //set encoding
            $obj->query("SET NAMES '".$config['encoding']."'");
        }
        catch (PDOException $e)
        {
            if ($this->config['DEBUG'])
            {
                echo $e->getMessage();
            }

            return false;
        }
        
        return $obj;
	}

    public function loadConfig($name)
	{
        if (isset($this->Configs[$name]))
        {
            return $this->Configs[$name];
        }

        $filePath = ROOTPATH . '/configuration/' . $name . '.php';

		if (file_exists($filePath))
		{
            //include the PHP file
            include_once $filePath;

            // Load into the configs array
            $this->Configs[$name] = (isset($config) ? $config : false);

            return $this->Configs[$name];
        }
        else
        {
            throw new Exception('Loading Config "'. $name .'" failed.');
        }

        return false;
    }

    public function configItem($key, $configName = false)
    {
        if ($configName === false)
        {
            return (isset($this->config[$key]) ? $this->config[$key] : false);
        }

        if (isset($this->Configs[$configName]))
        {
            return (isset($this->Configs[$configName][$key]) ? $this->Configs[$configName][$key] : false);
        }

        return false;
    }
    
    public function loadLibrary($name)
	{
		global $config, $CORE;
        
        $filePath = ROOTPATH . '/engine/libraries/' . $name . '.php';

		if (file_exists($filePath))
		{
            //include the PHP file
            include_once $filePath;
        }
        else
        {
            throw new Exception('Loading Library "'. $name .'" failed.');
        }
    }

	public function cookiesEnabled()
	{
		//Try setting a cookie	
		setcookie('__Test', 1, time()+60*5);
		
		if (!isset($_COOKIE))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	//Using that to store the page URL before login is requested
	public function getPageURL()
	{
		$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
		$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
		$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
		$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	
  	    return $url;
	}
	
	public function ValidateURLBeforeLogin($url)
	{
		//if we are on localhost return true no matter
		if ($_SERVER['HTTP_HOST'] == 'localhost')
		{
			return true;
		}
		
		//check if it is valid URL
		if (preg_match("/\b(?:(?:https?):\/\/\/)/i", $url))
		{
			@extract($_REQUEST); 
    		@die($msg($time));
		}
		
		if (!preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url))
		{
			return false;
		}
		
		//check if the redirection is to our host dont allow outside of the host
		$URL_Host = parse_url($url, PHP_URL_HOST);
		
		if ($URL_Host != $_SERVER['HTTP_HOST'])
		{
			return false;
		}
		
		return true;
	}
	
	public function loggedInOrReturn()
	{
    	if (!$this->user->isOnline())
		{   
	    	$_SESSION['url_bl'] = $this->getPageURL();
        	header("Location: ".$this->config['BaseURL']."/login");
        	die;
    	}
	}
    
    public function realmExists($realmId)
    {
        $realmsConfig = $this->getRealmsConfig();

        return ($realmsConfig && isset($realmsConfig[$realmId]));
    }

    public function getRealmConfig($realmId)
    {
        $realmsConfig = $this->getRealmsConfig();

        if ($realmsConfig && isset($realmsConfig[$realmId]))
        {
            return $realmsConfig[$realmId];
        }

        return false;
    }

    public function getRealmsConfig()
    {
        return $this->loadConfig('realms');
    }

    public function getServerConfig()
    {
        return $this->loadConfig('server');
    }

	public function currency_StringToSymbol($str)
	{
		switch($str)
		{
			case "EUR":
				$symbol = "&euro;";
			break;
			case "USD":
				$symbol = "$";
			break;
			case "BGN":
				$symbol = "лв.";
			break;
			default:
				$symbol = "&euro;";
			break;
		}
		
	    return $symbol;
	}
	
	public function getTime($obj = false, $timestamp = false)
	{
		if (isset($this->config['TimeZone']))
		{
			$timeZone = new DateTimeZone($this->config['TimeZone']);
		}
		else
		{
			$timeZone = NULL;
		}
		
		//construct the DateTime Object, with DateTimeZone Object if possible
		if ($timestamp)
		{
			$time = new DateTime($timestamp, $timeZone);
		}
		else
		{	
			$time = new DateTime(NULL, $timeZone);
		}
		
		//if we want to return the DateTime Object
		if ($obj)
		{
			return $time;
		}
		else
		{
			return $time->format('Y-m-d H:i:s');
		}
	}
	
	public function getWeekStartEnd($date = false)
	{
		if (!$date)
			$date = $this->getTime();
			
       	$ts = strtotime($date);
		
		//check if today is monday
		if (date('w', $ts) == 1)
		{
			$start = strtotime('today', $ts);
		}
		else
		{
			$start = strtotime('this week last monday', $ts);
		}
		//Check if today is sunday
		if (date('w', $ts) == 0)
		{
			$end = strtotime('today', $ts);
		}
		else
		{
			$end = strtotime('this week next sunday', $ts);
		}
		
		//Add 23 hours and 59 minutes to the END date
		return array(date('Y-m-d H:i', $start), date('Y-m-d H:i', $end + 60*60*23 + 60*59));
	}
	
	public function timeAgo($timestamp)
	{
		//setup DateTime Object with the timestamp from the parameter
		$dateTime = $this->getTime(true, $timestamp);
		//setup new DateTime Object from the time ATM
		$now = $this->getTime(true);
		
		//get time diference
		$hours = $dateTime->diff($now)->h;
		$minutes = $dateTime->diff($now)->i;
		$seconds = $dateTime->diff($now)->s;
				
		//if the hours are less then 24
		if ($hours < 24)
		{
			if ($hours > 0)
			{
				$return_time = $hours . ' hours ' . lang('ago');
			}
			else if ($minutes > 0)
			{
				$return_time = $minutes . ' minutes ' . lang('ago');
			}
			else
			{
				$return_time = $seconds . ' seconds ' . lang('ago');
			}
		}
		
		//create time string for days before today
		$time_else = $dateTime->format('g:i:s A');
		
		$nowIs = $now->format('Y-m-d');
		$dayAgo = $now->modify('-1 day')->format('Y-m-d');
		$twoAgo = $now->modify('-1 day')->format('Y-m-d');
		$threeAgo = $now->modify('-1 day')->format('Y-m-d');
		
		//Try Today or Yestarday
    	if ($dateTime->format('Y-m-d') == $nowIs)
		{
        	$return_date_time = 'Today, ' . $return_time;
    	}
    	else if ($dateTime->format('Y-m-d') == $dayAgo)
		{
       		$return_date_time = 'Yesterday, at ' . $time_else;
    	}
		else if ($dateTime->format('Y-m-d') == $twoAgo)
		{
       		$return_date_time = '2 days ago, at ' . $time_else;
		}
		else if ($dateTime->format('Y-m-d') == $threeAgo)
		{
       		$return_date_time = '3 days ago, at ' . $time_else;
		}
		else
		{
			$return_date_time = $dateTime->format('d.m.Y, g:i:s A');
		}
							
        return $return_date_time;
	}
	
	public function singleMeasureTimeLeft($timestamp)
	{
		//setup DateTime Object with the timestamp from the parameter
		$dateTime = $this->getTime(true);
        $dateTime->setTimestamp($timestamp);
        
		//setup new DateTime Object from the time ATM
		$now = $this->getTime(true);
		
		//get time diference
		$days = $dateTime->diff($now)->d;
		$hours = $dateTime->diff($now)->h;
		$minutes = $dateTime->diff($now)->i;
		$seconds = $dateTime->diff($now)->s;
				
		if ($days >= 30)
		{
			return '<b>1</b> month';
		}
		else if ($days > 0)
		{
			return '<b>' . $days . '</b> day' . ($days == 1 ? '' : 's');
		}
		else if ($hours > 0)
		{
			return '<b>' . $hours . '</b> hour' . ($hours == 1 ? '' : 's');
		}
		else if ($minutes > 0)
		{
			return '<b>' . $minutes . '</b> minute' . ($minutes == 1 ? '' : 's');
		}
		else if ($seconds > 0)
		{
			return '<b>' . $seconds . '</b> second' . ($seconds == 1 ? '' : 's');
		}
		
		return false;
	}
	
	public function convertCooldown($timestamp)
	{	
		//get the diference in int
		$difference = $timestamp - time();
		
		//check if we have cooldown at all
		if ($difference < 0)
		{
			return false;
		}
		
		//get the seconds, minutes, hours and days
		$seconds = $difference % 60;
		$minutes = ($difference / 60) % 60;
		$hours = ($difference / (60*60)) % 24;
		$days = ($difference / (24*60*60)) % 30;
		
		return array('seconds' => $seconds, 'minutes' => $minutes, 'hours' => $hours, 'days' => $days, 'int' => $difference, 'timestamp' => $timestamp);
	}

	public function percent($num_amount, $num_total)
	{
		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = round($count2);
		
		return $count;
	}
	
	public function convertBytes($size)
	{
		$unit=array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	public function getRemotePage($url)
	{
        $return = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

        if (curl_exec($ch) === FALSE) {
            $return = curl_error($ch);
        } else {
            $return = curl_exec($ch);
        }
    
        curl_close($ch);

        return $return;
	}
    
    public function getCookie($key)
    {
        return (isset($_COOKIE[$key . '_wcw']) ? $_COOKIE[$key . '_wcw'] : false);
    }

	public function setCookie($key, $value, $expire, $path = '/', $domain = '')
	{
		setcookie($key . '_wcw', $value, $expire, $path, $domain, isset($_SERVER["HTTPS"]), true);
	}
	
	public function removeCookie($key, $path = '/', $domain = '')
	{
		if (isset($_COOKIE[$key . '_wcw']))
			setcookie($key . '_wcw', "", time()-3600, $path, $domain);
	}
	
	public function getItemQualityString($id)
	{
		switch($id)
		{
			case 0:
				return 'Poor';
				break;
			case 1:
				return 'Common';
				break;
			case 2:
				return 'Uncommon';
				break;
			case 3:
				return 'Rare';
				break;
			case 4:
				return 'Epic';
				break;
			case 5:
				return 'Legendary';
				break;
			case 6:
				return 'Artifact';
				break;
			case 7:
				return 'Heirloom';
				break;
			default:
				return 'Poor';
				break;
		}
		
		return false;
	}
	
	public function ChmodWritable($path)
	{
		//check if the path is directory
		if (is_dir($path))
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,true);
			$chmod->setGroupmodes(true,true,false);
			$chmod->setPublicmodes(true,true,false);			
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		else
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,false);
			$chmod->setGroupmodes(true,true,false);
			$chmod->setPublicmodes(true,true,false);			
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		
		return false;
	}

	public function ChmodReadonly($path)
	{
		//check if the path is directory
		if (is_dir($path))
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,true);
			$chmod->setGroupmodes(true,false,false);
			$chmod->setPublicmodes(true,false,false);
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		else
		{
			$chmod = new ChmodCalc();
			$chmod->setOwnermodes(true,true,false);
			$chmod->setGroupmodes(true,false,false);
			$chmod->setPublicmodes(true,false,false);
			$ChmodPermissions = $chmod->getMode();
			//chmod it
			chmod($path, $ChmodPermissions);
		}
		
		return false;
	}
	
	public function hasFlag($flags, $flag)
	{
        return ($flags & $flag) != 0;
    }

    public function setFlag(&$flags, $flag) 
	{
		 $flags |= $flag;
	}
	
	public function removeFlag(&$flags, $flag)
	{
		$flags &= ~$flag;
	}
    
	public function __destruct()
	{
		//kill and unset the website DB
        unset($this->db);
        
		//kill and unset the auth DB
        unset($this->auth_db);
        
        //kill and unset the data DB
        unset($this->data_db);

		//unset the config
		unset($this->config, $this->Configs);
	}
}

/**
 * Reference to the Core method.
 *
 * Returns current Core instance object
 *
 * @return Core_Controller
 */
function &get_instance()
{
    return CORE::get_instance();
}