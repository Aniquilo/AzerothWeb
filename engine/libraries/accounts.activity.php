<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class AccountActivity
{
	public static function Insert($description, $userId = false)
	{
		global $DB, $CORE;
		
		//check if we have set account ID
		if ($userId === false)
		{
			//get the CURUSER acc id
			$userId = $CORE->user->get('id');
		}
        
        //get the time
        $ip = $CORE->security->getip();
        $time = $CORE->getTime();
		
		$insert = $DB->prepare("INSERT INTO `account_activity` (`account`, `description`, `ip_address`, `time`) VALUES (:acc, :description, :ip_address, :time);");
		$insert->bindParam(':acc', $userId, PDO::PARAM_INT);
		$insert->bindParam(':description', $description, PDO::PARAM_STR);
		$insert->bindParam(':ip_address', $ip, PDO::PARAM_STR);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
		$insert->execute();
		
		if ($insert->rowCount() > 0)
		{
			//success
			return true;
        }
        
		return false;
	}
}