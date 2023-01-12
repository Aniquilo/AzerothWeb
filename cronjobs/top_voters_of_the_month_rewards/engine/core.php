<?php

define('init_engine', true);

require_once ROOTPATH . '/configuration/config.php';
include_once ROOTPATH . '/engine/classes/security.php';
include_once ROOTPATH . '/engine/classes/coin.activity.php';
include_once ROOTPATH . '/engine/classes/accounts.finances.php';
include_once ROOTPATH . '/engine/constants.php';

class CORE
{
	private $config;
	private $db = false;
	
	public function __construct()
	{
		global $config;
		
		$this->config = $config;
	}
	
	public function DatabaseConnection()
	{
		global $PDO_config;
		
		try 
		{
			//Construct PDO
			$obj = new PDO('mysql:dbname='.$this->config['DatabaseName'].'; host='.$this->config['DatabaseHost'].';', $this->config['DatabaseUser'], $this->config['DatabasePass'], NULL);
			
			//set error handler exception
			$obj->setAttribute(PDO::ATTR_ERRMODE, $PDO_config['errorHandler']);
			
			//set default fetch method
			$obj->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $PDO_config['fetch']);
			
			//set encoding
			$obj->query("SET NAMES '".$this->config['DatabaseEncoding']."'");
		}
		catch (PDOException $e)
		{
			echo '<strong>Database Connection failed:</strong> ' . $e->getMessage();
			die;
		}
		
		$this->db = $obj;
		
  	  return $obj;
	}
	
	public function getTime($obj = false, $timestamp = false)
	{
	  global $config;
	  	
		if (isset($config['TimeZone']))
		{
			$timeZone = new DateTimeZone($config['TimeZone']);
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
	
	public function percent($num_amount, $num_total)
	{
		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = round($count2);
		
		return $count;
	}
	
	public function __destruct()
	{
		//kill and unset the website DB
		$this->db = NULL;
		unset($this->db);

		//unset the config
		unset($this->config);
	}
}