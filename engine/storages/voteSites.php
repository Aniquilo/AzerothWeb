<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class VoteSitesData
{
	public $data;

	public function __construct()
	{
        global $config;

        $this->data = $config['VOTE']['Sites'];
	}
	
	public function get($key)
	{
		if (!isset($this->data[$key]))
		{
			return false;
		}
		
		return $this->data[$key];
	}
	
	public function __destruct()
	{
		unset($this->data);
		return true;
	}
}
