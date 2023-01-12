<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class azerothcore_Authentication implements emulator_Authentication
{
    private $core;
    private $db;

    /**
	 * Array of table names
	 */
	protected $tables = array(
		"account" => "account",
		"account_access" => "account_access",
		"account_banned" => "account_banned"
    );
    
    /**
	 * Array of column names
	 */
	protected $columns = array(
		"account" => array(
			"id" => "id",
			"username" => "username",
			"accsalt" => "salt",
			"hash" => "verifier",
            "email" => "email",
            "reg_mail" => "reg_mail",
            "joindate" => "joindate",
            "last_ip" => "last_ip",
            "expansion" => "expansion",
            "recruiter" => "recruiter"
		),
		"account_access" => array(
			"id" => "id",
            "gmlevel" => "gmlevel",
            "realmid" => "RealmID"
		),
		"account_banned" => array(
			"id" => "id",
			"banreason" => "banreason",
			"active" => "active",
			"bandate" => "bandate",
			"unbandate" => "unbandate",
			"bannedby" => "bannedby"
        )
    );

    public function __construct()
    {
        $this->core =& get_instance();
        $this->db = $this->core->auth_db;
    }

    /**
	 * Gets account record by ID
	 * @param Int $id
	 * @return Array (id, identity, username, email, reg_mail, hash, joindate, expansion, recruiter)
	 */
    public function getAccountById($id)
    {
        $res = null;

        //Get the user account record
        if ($this->core->configItem('bnet', 'authentication'))
        {
            $res = $this->db->prepare("SELECT 
                    `battlenet_accounts`.`id`, 
                    `battlenet_accounts`.`email` AS `identity`, 
                    `account`.`username`, 
                    `battlenet_accounts`.`email`, 
                    `account`.`reg_mail`, 
                    `battlenet_accounts`.`verifier` AS hash, 
                    `battlenet_accounts`.`joindate`, 
                    `account`.`expansion`, 
                    `account`.`recruiter` 
                FROM `battlenet_accounts` 
                LEFT JOIN `account` ON `battlenet_accounts`.`id` = `account`.`battlenet_account` AND `account`.`battlenet_account` = '1' 
                WHERE `battlenet_accounts`.`id` = :id LIMIT 1;");
            $res->bindParam(':id', $id, PDO::PARAM_STR);
            $res->execute();
        }
        else
        {
            $res = $this->db->prepare("SELECT `id`, `username` AS `identity`, `username`, `salt`, `email`, `reg_mail`, `verifier` AS hash, `joindate`, `expansion`, `recruiter` FROM `account` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $id, PDO::PARAM_STR);
            $res->execute();
        }

        if ($res->rowCount() > 0)
        {
            return $res->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
	 * Gets account record by identity
	 * @param String $identity
	 * @return Array (id, identity, username, email, reg_mail, hash, joindate, expansion, recruiter)
	 */
    public function getAccountByIdentity($identity)
    {
        $res = null;

        //Get the user account record
        if ($this->core->configItem('bnet', 'authentication'))
        {
            $res = $this->db->prepare("SELECT 
                    `battlenet_accounts`.`id`, 
                    `battlenet_accounts`.`email` AS `identity`, 
                    `account`.`username`, 
                    `battlenet_accounts`.`email`, 
                    `account`.`reg_mail`, 
                    `battlenet_accounts`.`verifier` AS hash, 
                    `battlenet_accounts`.`joindate`, 
                    `account`.`expansion`, 
                    `account`.`recruiter` 
                FROM `battlenet_accounts` 
                LEFT JOIN `account` ON `battlenet_accounts`.`id` = `account`.`battlenet_account` AND `account`.`battlenet_account` = '1' 
                WHERE `battlenet_accounts`.`email` = :identity LIMIT 1;");
            $res->bindParam(':identity', $identity, PDO::PARAM_STR);
            $res->execute();
        }
        else
        {
            $res = $this->db->prepare("SELECT `id`, `username` AS `identity`, `username`, `salt`, `email`, `reg_mail`, `verifier` AS hash, `joindate`, `expansion`, `recruiter` FROM `account` WHERE `username` = :identity LIMIT 1;");
            $res->bindParam(':identity', $identity, PDO::PARAM_STR);
            $res->execute();
        }

        if ($res->rowCount() > 0)
        {
            return $res->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
	 * Gets account record by email
	 * @param String $email
	 * @return Array (id, identity, username, email, reg_mail, hash, joindate, expansion, recruiter)
	 */
    public function getAccountByEmail($email)
    {
        $res = null;

        //Get the user account record
        if ($this->core->configItem('bnet', 'authentication'))
        {
            $res = $this->db->prepare("SELECT 
                    `battlenet_accounts`.`id`, 
                    `battlenet_accounts`.`email` AS `identity`, 
                    `account`.`username`, 
                    `battlenet_accounts`.`email`, 
                    `account`.`reg_mail`, 
                    `battlenet_accounts`.`verifier` AS hash, 
                    `battlenet_accounts`.`joindate`, 
                    `account`.`expansion`, 
                    `account`.`recruiter` 
                FROM `battlenet_accounts` 
                LEFT JOIN `account` ON `battlenet_accounts`.`id` = `account`.`battlenet_account` AND `account`.`battlenet_account` = '1' 
                WHERE `battlenet_accounts`.`email` = :email LIMIT 1;");
            $res->bindParam(':email', $email, PDO::PARAM_STR);
            $res->execute();
        }
        else
        {
            $res = $this->db->prepare("SELECT `id`, `username` AS `identity`, `username`, `salt`, `email`, `reg_mail`, `verifier` AS hash, `joindate`, `expansion`, `recruiter` FROM `account` WHERE `email` = :email LIMIT 1;");
            $res->bindParam(':email', $email, PDO::PARAM_STR);
            $res->execute();
        }

        if ($res->rowCount() > 0)
        {
            return $res->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
	 * Gets account record by username
	 * @param String $identity
	 * @return Array (id, identity, username, email, reg_mail, hash, joindate, expansion, recruiter)
	 */
    public function getAccountByUsername($username)
    {
        $res = null;

        //Get the user account record
        if ($this->core->configItem('bnet', 'authentication'))
        {
            $res = $this->db->prepare("SELECT 
                    `battlenet_accounts`.`id`, 
                    `battlenet_accounts`.`email` AS `identity`, 
                    `account`.`username`,
                    `battlenet_accounts`.`email`, 
                    `account`.`reg_mail`, 
                    `battlenet_accounts`.`verifier` AS hash, 
                    `battlenet_accounts`.`joindate`, 
                    `account`.`expansion`, 
                    `account`.`recruiter` 
                FROM `account` 
                LEFT JOIN `battlenet_accounts` ON `battlenet_accounts`.`id` = `account`.`battlenet_account` AND `account`.`battlenet_account` = '1' 
                WHERE `account`.`username` = :username LIMIT 1;");
            $res->bindParam(':username', $username, PDO::PARAM_STR);
            $res->execute();
        }
        else
        {
            $res = $this->db->prepare("SELECT `id`, `username` AS `identity`, `username`, `salt`, `email`, `reg_mail`, `verifier` AS hash, `joindate`, `expansion`, `recruiter` FROM `account` WHERE `username` = :username LIMIT 1;");
            $res->bindParam(':username', $username, PDO::PARAM_STR);
            $res->execute();
        }

        if ($res->rowCount() > 0)
        {
            return $res->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
	 * Create a hash used for authentication
	 * @param String $identity
     * @param String $pass
	 * @return String
	 */
	public function makeHash($identity, $password)
	{  
        if ($this->core->configItem('bnet', 'authentication'))
        {
            return $this->makeHashBnet($identity, $password);
        }

        return $this->makeHashRegular($identity, $password);
    }
	
	public function makeVerifier($identity, $password, $salt)
	{  
        return $this->CalculateVerifier($identity, $password, $salt);
    }

    private function makeHashRegular($username, $password)
	{   
		$username = trim($username);
		$password = trim($password);
        
        return strtoupper(sha1(strtoupper($username) . ":" . strtoupper($password)));
    }

    private function makeHashBnet($email, $password)
    {
        $email = trim($email);
		$password = trim($password);
        
        return strtoupper(bin2hex(strrev(hex2bin(strtoupper(hash("sha256", strtoupper(hash("sha256", strtoupper($email)) . ":" . strtoupper($password))))))));
    }
    
    /**
	 * Change account password
     * @param Int $id
	 * @param String $identity
     * @param String $password
	 * @return bool true or false
	 */
    public function changePassword($id, $identity, $password, $salt)
	{
        if ($this->core->configItem('bnet', 'authentication'))
        {
            return $this->changePasswordBnet($id, $identity, $password);
        }

        //make our new pass hash
    //    $shapasshash = $this->makeHashRegular($identity, $password);
		
		//make our new verifier
        $verifier = $this->makeVerifier($identity, $password, $salt);
        
        //Apply the new hash to the account
        $update = $this->db->prepare("UPDATE `account` SET `verifier` = :hash, `session_key` = '' WHERE `id` = :acc LIMIT 1;");
    //  $update->bindParam(':hash', $shapasshash, PDO::PARAM_STR);
		$update->bindParam(':hash', $verifier, PDO::PARAM_LOB);
        $update->bindParam(':acc', $id, PDO::PARAM_INT);
        $update->execute();
            
        //check if the account was affected
        return ($update->rowCount() > 0);
    }

    private function changePasswordBnet($id, $identity, $password)
    {
        //make our new pass hash
        $shapasshash = $this->makeHashBnet($identity, $password);

        //Apply the new hash to the account
        $update = $this->db->prepare("UPDATE `battlenet_accounts` SET `verifier` = :hash WHERE `id` = :acc LIMIT 1;");
        $update->bindParam(':hash', $shapasshash, PDO::PARAM_STR);
        $update->bindParam(':acc', $id, PDO::PARAM_INT);
        $update->execute();

        if (($update->rowCount() > 0))
        {
            $row = $this->getAccountById($id);

            if ($row)
            {
                //make our new pass hash
                $shapasshash = $this->makeHashRegular($row['username'], $password);
                
                //Apply the new hash to the account
                $update = $this->db->prepare("UPDATE `account` SET `verifier` = :hash, `sessionkey` = '', `v` = '', `s` = '' WHERE `battlenet_account` = :acc LIMIT 1;");
                $update->bindParam(':hash', $shapasshash, PDO::PARAM_STR);
                $update->bindParam(':acc', $id, PDO::PARAM_INT);
                $update->execute();
            }

            return true;
        }

        return false;
    }

    /**
	 * Change account email
     * @param Int $id
	 * @param String $email
	 * @return bool true or false
	 */
    public function changeEmail($id, $email)
    {
        if ($this->core->configItem('bnet', 'authentication'))
        {
            return $this->changeEmailBnet($id, $email);
        }

        //Apply the email to the account
        $update = $this->db->prepare("UPDATE `account` SET `email` = :email WHERE `id` = :acc LIMIT 1;");
        $update->bindParam(':email', $email, PDO::PARAM_STR);
        $update->bindParam(':acc', $id, PDO::PARAM_INT);
        $update->execute();
            
        //check if the account was affected
        return ($update->rowCount() > 0);
    }

    private function changeEmailBnet($id, $email)
    {
        //Apply the new hash to the account
        $update = $this->db->prepare("UPDATE `battlenet_accounts` SET `email` = :email WHERE `id` = :acc LIMIT 1;");
        $update->bindParam(':email', strtoupper($email), PDO::PARAM_STR);
        $update->bindParam(':acc', $id, PDO::PARAM_INT);
        $update->execute();

        if (($update->rowCount() > 0))
        {
            $update = $this->db->prepare("UPDATE `account` SET `email` = :email WHERE `battlenet_account` = :acc LIMIT 1;");
            $update->bindParam(':email', $email, PDO::PARAM_STR);
            $update->bindParam(':acc', $id, PDO::PARAM_INT);
            $update->execute();

            return true;
        }

        return false;
    }

    /**
	 * Create a new user account
	 * @param String $identity
     * @param String $password
     * @param String $email
     * @param String $recruiter
	 * @return Int The account record ID or bool false
	 */
    public function register($identity, $password, $email, $recruiter)
	{
        if ($this->core->configItem('bnet', 'authentication'))
        {
            return $this->bnetRegister($password, $email, $recruiter);
        }
		
		//make the user pass hash
	//	$shapasshash = self::makeHashRegular($identity, $password);

		//Generate Random Salt
        $salt = random_bytes(32);
        
		//make our new verifier
        $verifier = $this->makeVerifier($identity, $password, $salt);
		
		// done - this is what you put in the account table!
		$newSalt = $salt;
		$newVerifier = $verifier;
		
		//get the time for the joindate
		$joindate = $this->core->getTime();
        
		//get the visitor IP Address
		$lastip = $this->core->security->getip();
        $expansion = $this->core->configItem('expansion', 'authentication');
        
		$insert = $this->db->prepare("INSERT INTO `account` (`username`, `salt`, `verifier`, `email`, `reg_mail`,  `joindate`, `last_ip`, `expansion`, `recruiter`) VALUES (:username, :salt, :passhash, :email, :email, :joindate, :lastip, :expansion, :recruiter);");
		$insert->bindParam(':username', $identity, PDO::PARAM_STR);
		$insert->bindParam(':salt', $newSalt, PDO::PARAM_LOB);
		$insert->bindParam(':passhash', $newVerifier, PDO::PARAM_LOB);
		$insert->bindParam(':email', $email, PDO::PARAM_STR);
		$insert->bindParam(':joindate', $joindate, PDO::PARAM_STR);
		$insert->bindParam(':lastip', $lastip, PDO::PARAM_STR);
		$insert->bindParam(':expansion', $expansion, PDO::PARAM_INT);
		$insert->bindParam(':recruiter', $recruiter, PDO::PARAM_INT);
				
		//make sure the query was executed without errors
		if ($insert->execute())
		{
			return (int)$this->db->lastInsertId();
		}
        
		return false;
    }

    /**
	 * Create a new user battle net account
     * @param String $password
     * @param String $email
     * @param String $recruiter
	 * @return Int The account record ID or bool false
	 */
    private function bnetRegister($password, $email, $recruiter)
	{
		//make the user pass hash
		$shapasshash = self::makeHashBnet($email, $password);
		
		//get the time for the joindate
		$joindate = $this->core->getTime();
        
		//get the visitor IP Address
		$lastip = $this->core->security->getip();
        $expansion = $this->core->configItem('expansion', 'authentication');
        
        $insert = $this->db->prepare("INSERT INTO `battlenet_accounts` (`email`, `verifier`,  `joindate`, `last_ip`) VALUES (:email, :passhash, :joindate, :lastip);");
        $insert->bindParam(':email', $email, PDO::PARAM_STR);
		$insert->bindParam(':passhash', $shapasshash, PDO::PARAM_STR);
		$insert->bindParam(':joindate', $joindate, PDO::PARAM_STR);
		$insert->bindParam(':lastip', $lastip, PDO::PARAM_STR);
				
		//make sure the query was executed without errors
		if ($insert->execute())
		{
            $accountId = (int)$this->db->lastInsertId();
            $username = $accountId . '#1';
            $shapasshash = $this->makeHashRegular($username, $password);

            $insert = $this->db->prepare("INSERT INTO `account` (`username`, `verifier`, `email`, `reg_mail`,  `joindate`, `last_ip`, `expansion`, `recruiter`, `battlenet_account`, `battlenet_index`) VALUES (:username, :passhash, :email, :email, :joindate, :lastip, :expansion, :recruiter, :accountId, '1');");
            $insert->bindParam(':username', $username, PDO::PARAM_STR);
            $insert->bindParam(':passhash', $shapasshash, PDO::PARAM_STR);
            $insert->bindParam(':email', $email, PDO::PARAM_STR);
            $insert->bindParam(':joindate', $joindate, PDO::PARAM_STR);
            $insert->bindParam(':lastip', $lastip, PDO::PARAM_STR);
            $insert->bindParam(':expansion', $expansion, PDO::PARAM_INT);
            $insert->bindParam(':recruiter', $recruiter, PDO::PARAM_INT);
            $insert->bindParam(':accountId', $accountId, PDO::PARAM_INT);
            $insert->execute();

            return $accountId;
		}
        
		return false;
    }
    
    /**
	 * Gets an array containing User Details for the ACP user view page.
	 * @param Int $id
	 * @return Array
	 */
    public function getACPUserDetails($id)
    {
        $res = $this->db->prepare("SELECT * FROM `account` WHERE `id` = :acc LIMIT 1;");
        $res->bindParam(':acc', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() > 0)
        {
            $row = $res->fetch();

            return array(
                array('name' => 'ID', 'value' => $row['id']),
                array('name' => 'Username', 'value' => $row['username']),
                array('name' => 'E-mail', 'value' => $row['email']),
                array('name' => 'Register E-mail', 'value' => $row['reg_mail']),
                array('name' => 'Register Date', 'value' => $row['joindate']),
                array('name' => 'Last Login', 'value' => $row['last_login']),
                array('name' => 'Last IP', 'value' => $row['last_ip']),
                array('name' => 'Failed Logins', 'value' => $row['failed_logins']),
                array('name' => 'Expansion', 'value' => $row['expansion']),
                array('name' => 'Locale', 'value' => $row['locale']),
                array('name' => 'Locked', 'value' => $row['locked']),
                array('name' => 'Online', 'value' => $row['online']),
                array('name' => 'Mute Time', 'value' => $row['mutetime']),
                array('name' => 'Mute Reason', 'value' => $row['mutereason']),
                array('name' => 'Mute By', 'value' => $row['muteby']),
                array('name' => 'OS', 'value' => $row['os']),
                array('name' => 'Recruiter', 'value' => $row['recruiter']),
            );
        }

        return false;
    }

    /**
	 * Get the name of a table
	 * @param String $name
	 * @return String
	 */
	public function getTable($name)
	{
		if (array_key_exists($name, $this->tables))
		{
			return $this->tables[$name];
		}
	}

	/**
	 * Get the name of a column
	 * @param String $table
	 * @param String $name
	 * @return String
	 */
	public function getColumn($table, $name)
	{
		if (array_key_exists($table, $this->columns) && array_key_exists($name, $this->columns[$table]))
		{
			return $this->columns[$table][$name];
		}
	}

	/**
	 * Get a set of all columns
	 * @param String $name
	 * @return String
	 */
	public function getAllColumns($table)
	{
		if (array_key_exists($table, $this->columns))
		{
			return $this->columns[$table];
		}
	}
	
	/**********************************************
	 * calculate verifier 
	 * @param Variable $verifier
	 * @
	 */
	public function CalculateVerifier($username, $password, $salt)
    {
        // algorithm constants
        $g = gmp_init(7);
        $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
        
        // calculate first hash
        $h1 = sha1(strtoupper($username . ':' . $password), TRUE);
        
        // calculate second hash
        $h2 = sha1($salt.$h1, TRUE);
        
        // convert to integer (little-endian)
        $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
        
        // g^h2 mod N
        $verifier = gmp_powm($g, $h2, $N);
        
        // convert back to a byte array (little-endian)
        $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);
        
        // pad to 32 bytes, remember that zeros go on the end in little-endian!
        $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);
        
        // done!
        return $verifier;
    }
}