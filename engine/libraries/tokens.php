<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Tokens
{
    private   $salt     	= 'warcrysalt'; //Secret Word
	private   $key      	= false;
	private   $identifier	= false;
	private	  $application	= false;
	private   $expire 		= false;
	private   $externalData = false;
	private   $savedKeyData = array();
	private   $lastKeyId    = false;
	private   $keyAlgorythm = false;
	
	const EXPIRE_NEVER = 'never';
	
	public function __construct()
	{
	}
	
	public function setIdentifier($id)
	{
		$this->identifier = $id;
	}
	
	public function setApplication($app)
	{
		$this->application = $app;
	}
	
	public function setExpiration($exp)
	{
		$this->expire = $exp;
	}
	
	public function setAlgorythm($str)
	{
		$this->keyAlgorythm = $str;
	}
	
	/**
	**  Converts array into string format and set's it for external data [Accepts Array Only]
	**/	
	public function setExternalData($dataArray)
	{
		$this->externalData = json_encode($dataArray);
	}
	
	/**
	**  Generates random key
	**/	
	public function generateKey()
	{
		//Generate key by defined algorythm
		if ($this->keyAlgorythm)
		{
			return $this->generateKeyByAlgorythm();
		}
		
		//check if we could use a identifier in the key
		if ($this->identifier)
		{
			$this->key = uniqid(mt_rand(), true) . sha1($this->identifier . $this->salt) . uniqid(mt_rand(), true);
		}
		else
		{
			$this->key = uniqid(mt_rand(), true) . uniqid(mt_rand(), true);
		}
		
		//strip dots
		return $this->key = str_replace('.', '', $this->key);			
	}
	
	/**
	**  Generates a key by defined pattern
	**  - Usable markers (s, S, d)
	**/
	
	private function generateKeyByAlgorythm()
	{
		//randomize the pattern
		$pattern = str_shuffle($this->keyAlgorythm);
		
		//split into markers
		$markers = str_split($pattern);
		
		$key = '';
		//let's put up our key
		foreach ($markers as $marker)
		{
			switch ($marker)
			{
				case 'd':
					$key .= substr(str_shuffle(str_repeat("0123456789", 1)), 0, 1);
					break;
				case 's':
					$key .= substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 1)), 0, 1);
					break;
				case 'S':
					$key .= strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 1)), 0, 1));
					break;
			}
		}
		
		//save our key
		$this->key = $key;
		
		unset($pattern, $markers, $marker, $key);
		
		return $this->key;
	}
	
	/**
	**  Registers the key generated by ($this->generateKey()) into the database
	**/		
	public function registerToken()
	{
		global $DB, $CORE;
		
		//check if we have key
		if ($this->key)
		{
			$id = $this->identifier ? $this->identifier : 0;
			$app = $this->application ? $this->application : '';
			$expire = $this->expire ? $this->expire : self::EXPIRE_NEVER;
			$data = $this->externalData ? $this->externalData : '';
			$time = $CORE->getTime();

			//insert new key	
			$insert_res = $DB->prepare("INSERT INTO `tokens` (`account`, `application`, `key`, `time`, `expire`, `externalData`) VALUES (:account, :app, :key, :time, :expire, :data);");
			$insert_res->bindParam(':account', $id, PDO::PARAM_INT);
			$insert_res->bindParam(':app', $app, PDO::PARAM_STR);
			$insert_res->bindParam(':key', $this->key, PDO::PARAM_STR);
			$insert_res->bindParam(':time', $time, PDO::PARAM_STR);
			$insert_res->bindParam(':expire', $expire, PDO::PARAM_STR);
			$insert_res->bindParam(':data', $data, PDO::PARAM_STR);
			$insert_res->execute();
			
			if ($insert_res->rowCount() < 1)
			{
				return 'Unable to insert the key into the database.';
			}
		}
		else
		{
			return 'There is no key to register.';
		}
		unset($insert_res);
		
		return true;
	}
	
	/**
	**  Validate the key which was decoded and set to the class
	**/	
	private function validateToken()
	{
		global $DB, $CORE;
		
		//check if we have key
		if ($this->key)
		{
			//The default query should not include the optional stuff
			$query_where = 'WHERE `key` = :key';
			//we need to check if we have identifier or application to search with
			if ($this->identifier)
				$query_where .= ' AND `account` = :id';
			if ($this->application)
				$query_where .= ' AND `application` = :app';
			
			//prepare the query
			$res = $DB->prepare("SELECT * FROM `tokens` ".$query_where." ORDER BY `time` DESC LIMIT 1;");
			$res->bindParam(':key', $this->key, PDO::PARAM_STR);
			if ($this->identifier)
			{
				$res->bindParam(':id', $this->identifier, PDO::PARAM_INT);
			}
			if ($this->application)
			{
				$res->bindParam(':app', $this->application, PDO::PARAM_STR);
			}
			//run the query
			$res->execute();
			
			//check if the key was found
			if ($res->rowCount() > 0)
			{
				$row = $res->fetch();
				//check if the key has expire period
				if ($row['expire'] != self::EXPIRE_NEVER)
				{
					//check the ticket expiration
					//Convert to Time Object
					$timeObj = $CORE->getTime(true, $row['time']);
					$timeObj->add(date_interval_create_from_date_string($row['expire']));
					$expires = $timeObj->format('Y-m-d H:i:s');
					
					//now check if the time now is greater than the expiration
					if ($CORE->getTime() > $expires)
					{ 
						return 'The key has expired.';
					}
				}
			}
			else
			{
				return 'No record was found with that key.';
			}
		}
		else
		{
			return 'There is no key to validate.';
		}
		
		//save the data about this key
		$this->savedKeyData[$row['id']] = $row;
		$this->lastKeyId = $row['id'];
		
		//if the script ends here that means that the key is valid
		return true;
	}
	
	public function get_tokenData($id = false)
	{
		if (!$id)
		{
			$id = $this->lastKeyId;
		}
		
		//check if the data about this token ID is set
		if (isset($this->savedKeyData[$id]))
		{
			return $this->savedKeyData[$id];
		}
		
		return 'No saved data was found.';
	}
	
	/**
	**  Destroy token - deletes the record from the database
	**/	
	public function destroyToken($id = false)
	{
		global $DB, $CORE;
		
		if (!$id)
		{
			$id = $this->lastKeyId;
		}
		
		//check if we have key
		if ($id)
		{
			//prepare the query
			$res = $DB->prepare("DELETE FROM `tokens` WHERE `id` = :id LIMIT 1;");
			$res->bindParam(':id', $id, PDO::PARAM_INT);
			//run the query
			$res->execute();
			
			if ($res->rowCount() > 0)
			{
				if ($id == $this->lastKeyId)
				{
					$this->lastKeyId = false;
				}
				
				return true;
			}
			else
			{
				return 'Unable to remove the token recrod from the database.';
			}
		}
		else
		{
			return 'Unable to destroy token without id.';
		}
	}

	public function getExternalData($id = false)
	{
		if (!$id)
		{
			$id = $this->lastKeyId;
		}
		
		if ($id)
		{
			//get the token data
			if (isset($this->savedKeyData[$id]))
			{
				return json_decode($this->savedKeyData[$id]['externalData'], true);
			}
		}
		
		return false;
	}
	
	/**
	**  Encodes the key on Base64 and returns it
	**/	
	public function getKey()
	{
		if ($this->key)
		{
			return base64_encode($this->key);
		}
		else
		{
			//no key was generated, so we do it now
            $this->generateKey();
            
			//return the key using the same function
			return base64_encode($this->key);
		}
	}

	/**
	**  Decodes the key on Base64 and stores it in the class then returns token validation
	**/	
	public function setKey($key = false)
	{
		if ($key)
		{
            $this->key = base64_decode($key);
            
			//validate the key
			return $this->validateToken();
        }
        
		return false;
	}
		
	public function __destrruct()
	{
	}
}