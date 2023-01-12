<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class UserRank
{
	private $rank;
	
	//Constructor
	public function __construct($rank)
	{
		$this->rank = $rank;
		
		return true;	
	}
	
	public function int()
	{
		return (int)$this->rank;
	}
	
	public function string()
	{
		$data = new RankStringData();
		
		return $data->get($this->int());
	}
}

class Avatar
{
	private $id;
	private $str;
	private $rank;
	private $type;
	
	public function __construct($id = 0, $str = '', $rank = 0, $type = 0)
	{
		$this->id = $id;
		$this->str = $str;
		$this->rank = $rank;
		$this->type = $type;
		
		return true;
	}
	
	public function int()
	{
		return (int)$this->id;
	}
	
	public function string()
	{
		return $this->str;
	}
	
	public function rank()
	{
		return (int)$this->rank;
	}
	
	public function type()
	{
		return (int)$this->type;
	}
}

class CURUSER
{
    private $core;
    private $db;
    private $row;
    private $roles;
    
 	//Constructor
	public function __construct()
	{
        $this->core =& get_instance();
        $this->db = $this->core->db;
        $this->roles = array();
    }
	
	//function to check if the current user is logged in
	public function isOnline()
	{
		//If session logged is not set return false
		if (!isset($_SESSION['logged']) || !$_SESSION['logged'] || $_SESSION['logged'] != '1')
		{
		    return false;
		}
		else if (!isset($this->row)) //if the curuser record is not set
		{
			return false;
		}
		
	    return true;
	}
    
	public function InitializeGuest()
	{
        $this->row = array();
        
        // Only initilize RBAC after the user record has been set
        $this->InitializeRBAC();
	}

	//We set the user record on startup check
	public function InitializeUser($array)
	{
        $this->row = $array;

        // Only initilize RBAC after the user record has been set
        $this->InitializeRBAC();
	}
	
	//If the index dosent exits returns false
	public function get($key)
	{
		if (!isset($this->row[$key]))
		    return false;
		  
	    return $this->row[$key];
	}
	
	//Function to set variable into the curuser row
	public function set($key, $value)
	{
		return $this->row[$key] = $value;
	}

	public function setLoggedIn($id, $passhash)
	{
		$ss = new Secure(true, 2);
    	$ss->open();
		unset($ss);
		
    	$_SESSION['uid'] = $id;
		$_SESSION['pass'] = $passhash;
        $_SESSION['logged'] = '1';
        
	    return true;
	}
	
	public function getRank()
	{
		return new UserRank($this->get('rank'));
	}
	
	public function getAvatar()
	{
		if ((int)$this->get('avatarType') == AVATAR_TYPE_GALLERY)
		{
			$gallery = new AvatarGallery();

			return $gallery->get($this->get('avatar'));
		}
		else if ((int)$this->get('avatarType') == AVATAR_TYPE_UPLOAD)
		{
			return new Avatar(0, $this->get('avatar'), 0, AVATAR_TYPE_UPLOAD);
		}
		
		return false;
	}
	
	public function GetRealmId()
	{
		//If for some reason this is called with no user
		if (!$this->isOnline())
		{
			return $this->core->realms->getFirstRealm()->getId();
		}
		
		//Check if the user has selected realm
		if (isset($this->row['selected_realm']) && $this->row['selected_realm'] != '')
		{
			//is valid realm
			if ($this->core->realms->realmExists($this->row['selected_realm']))
			{
				return (int)$this->row['selected_realm'];
			}
		}
		
		//not set
		return $this->core->realms->getFirstRealm()->getId();
    }
    
    public function getRealm()
    {
        return $this->core->realms->getRealm($this->GetRealmId());
    }
	
	public function Update($array)
	{
		if (!is_array($array))
		{
			return false;
		}
		else if (count($array) == 0)
		{
			return false;
		}
		
		//prepare the query
		foreach ($array as $key => $value)
		{
			$updateset[] = "`".$key."` = :".strtolower($key);
		}

		$userId = $this->get('id');

		$update = $this->db->prepare("UPDATE `account_data` SET ".implode(', ', $updateset)." WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $userId, PDO::PARAM_INT);
		//prepare the values
		foreach ($array as $key => $value)
		{
			$update->bindParam(':'.strtolower($key), $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
		}		
		$update->execute();

		if ($update->rowCount() > 0)
		{
			return true;
        }
        
		return false;
	}
	
	public function handle_MissingRecord($acc)
	{
		$ip = $this->core->security->getip();
		$thislogin = $this->core->getTime();
		
		$insert = $this->db->prepare("INSERT INTO `account_data` (`id`, `last_ip`, `reg_ip`, `last_login2`, `status`, `event`) VALUES (:account, :lastip, :regip, :lastlogin2, :status, :event);");
		$insert->bindParam(':account', $acc, PDO::PARAM_INT);
		$insert->bindParam(':lastip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':regip', $ip, PDO::PARAM_STR);
		$insert->bindParam(':lastlogin2', $thislogin, PDO::PARAM_STR);
        $insert->bindValue(':status', 'active', PDO::PARAM_STR);
        $insert->bindValue(':event', 'EVENT_COPIED_ACCOUNT', PDO::PARAM_STR);
		$insert->execute();
		
		$return = $insert->rowCount();
		
		return $return;
	}
    
    public function getLastLogin($userId = false)
    {
        if (!$userId)
            $userId = $this->get('id');

        if ($userId !== false)
        {
            //get the last login time
            $res = $this->db->prepare("SELECT last_login2 FROM `account_data` WHERE `id` = :account LIMIT 1;");
            $res->bindParam(':account', $userId, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();

                return $row['last_login2'];
            }
        }

        return false;
    }

    public function getLastIp($userId = false)
    {
        if (!$userId)
            $userId = $this->get('id');

        if ($userId !== false)
        {
            //get the last login time
            $res = $this->db->prepare("SELECT `last_ip` FROM `account_data` WHERE `id` = :account LIMIT 1;");
            $res->bindParam(':account', $userId, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $row = $res->fetch();

                return $row['last_ip'];
            }
        }

        return false;
    }

	public function logInfoAtLogin($acc)
	{
		$ip = $this->core->security->getip();
		$thislogin = $this->core->getTime();
		
		//get the last login time
		$lastLogin = $this->getLastLogin($acc);
		
		if (!$lastLogin)
			return false;
		
		$update = $this->db->prepare("UPDATE `account_data` SET `last_ip` = :ip, `last_login` = :lastlogin, `last_login2` = :lastlogin2, `login_attempts` = '0' WHERE `id` = :account LIMIT 1;");
		$update->bindParam(':account', $acc, PDO::PARAM_INT);
		$update->bindParam(':ip', $ip, PDO::PARAM_STR);
		$update->bindParam(':lastlogin', $lastLogin, PDO::PARAM_STR);
		$update->bindParam(':lastlogin2', $thislogin, PDO::PARAM_STR);
		$update->execute();
		
		return ($update->rowCount() > 0);
	}

	public function logout()
	{
    	$_SESSION = array();	
		session_unset();
        session_destroy();
        session_regenerate_id();

        $this->core->removeCookie('rmm_identity');
        $this->core->removeCookie('rmm_secret');
	}

	public function getCooldown($key, $userId = false)
	{
        if (!$userId)
        {
            //check if the current user is online
            if (!$this->isOnline())
            {
                return false;
            }

            $userId = $this->get('id');
        }

		$res = $this->db->prepare("SELECT `cooldown` FROM `account_cooldowns` WHERE `account` = :acc AND `keyName` = :key ORDER BY `id` DESC LIMIT 1;");
		$res->bindParam(':acc', $userId, PDO::PARAM_INT);
		$res->bindParam(':key', $key, PDO::PARAM_STR);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
			unset($res);
			return false;
        }

        $row = $res->fetch();
	    return (int)$row['cooldown'];
    }
    
    public function setCooldown($key, $value, $userId = false)
	{
        if (!$userId)
        {
            //check if the current user is online
            if (!$this->isOnline())
            {
                return false;
            }

            $userId = $this->get('id');
        }

		if (!is_numeric($value))
			return false;
		
		$rep = $this->db->prepare("REPLACE INTO `account_cooldowns` (`account`, `keyName`, `cooldown`) VALUES (:acc, :key, :cd);");
		$rep->bindParam(':acc', $userId, PDO::PARAM_INT);
        $rep->bindParam(':key', $key, PDO::PARAM_STR);
        $rep->bindParam(':cd', $value, PDO::PARAM_INT);
		$rep->execute();
		
	    return $rep->rowCount() > 0;
	}
	
	public function unsetCooldown($key, $userId = false)
	{
        if (!$userId)
        {
            //check if the current user is online
            if (!$this->isOnline())
            {
                return false;
            }

            $userId = $this->get('id');
        }

		$del = $this->db->prepare("DELETE FROM `account_cooldowns` WHERE `account` = :acc AND `keyName` = :key LIMIT 1;");
		$del->bindParam(':acc', $userId, PDO::PARAM_INT);
		$del->bindParam(':key', $key, PDO::PARAM_STR);
        $del->execute();
        
        return true;
	}
	
	public function getVoteIPCooldown($siteid)
	{
		$res = $this->db->prepare("SELECT `account` FROM `vote_data` WHERE `ip` = :ip AND `siteid` = :siteid ORDER BY `id` DESC LIMIT 1");
		$res->bindParam(':ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
		$res->bindParam(':siteid', $siteid, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
			unset($res);
			return false;
        }
        
		//fetch the data
		$row = $res->fetch();
		unset($res);
		
		return $this->getCooldown('votingsite'.$siteid, $row['account']);
	}
	
    // populate roles with their associated permissions
    protected function InitializeRBAC()
    {
        $this->roles = array();
        $sth = null;

        //check for guest
        if (!$this->isOnline())
        {
            $sql = "SELECT `role_id`, `role_name` FROM `rbac_roles` WHERE `role_id` = '1';";
            $sth = $this->db->prepare($sql);
            $sth->execute();
        }
        else
        {
            $userId = $this->get('id');

            $sql = "SELECT t1.role_id, t2.role_name FROM `rbac_user_role` as t1
                    JOIN `rbac_roles` as t2 ON t1.role_id = t2.role_id
                    WHERE t1.user_id = :user_id";
            $sth = $this->db->prepare($sql);
            $sth->execute(array(":user_id" => $userId));

            // Check if we have no role then give player
            if ($sth->rowCount() == 0)
            {
                $sql = "SELECT `role_id`, `role_name` FROM `rbac_roles` WHERE `role_id` = '2';";
                $sth = $this->db->prepare($sql);
                $sth->execute();
            }
        }

        while ($row = $sth->fetch(PDO::FETCH_ASSOC))
        {
            $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        }
    }

    // check if user has a specific permission
    public function hasPermission($perm)
    {
        foreach ($this->roles as $role)
        {
            if ($role->hasPerm($perm))
            {
                return true;
            }
        }
        
        return false;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function hasRole($roleId)
    {
        foreach ($this->roles as $role)
        {
            if ((int)$role->getId() == (int)$roleId)
                return true;
        }
        
        return false;
    }

    public function hasAnyOfRoles($roles)
    {
        foreach ($roles as $roleId)
        {
            if ($this->hasRole($roleId))
                return true;
        }
        
        return false;
    }

    public function setRecruiterLinkState($status)
	{
		$_SESSION['CU_RAF_LINK_STATE'] = $status;
	}
	
	public function getRecruiterLinkState()
	{
		//check if the sessions was set
		if (isset($_SESSION['CU_RAF_LINK_STATE']))
		{
			return $_SESSION['CU_RAF_LINK_STATE'];
		}
		
		return RAF_LINK_PENDING;
    }
    
	public function __destruct()
	{
		unset($this->row);
		unset($this->db);
	}
}