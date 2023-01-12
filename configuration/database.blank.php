<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Website Database Connection Info
$config['Website_DB'] = array(
    'host' 		=> 'localhost', 
    'port'      => 3306,
    'name' 		=> 'website', 		
    'user' 		=> 'root', 
    'pass' 		=> '', 
    'encoding' 	=> 'utf8'
);

//WoW Data Database Connection Info
$config['WoW_Data_DB'] = array(
    'host' 		=> 'localhost', 
    'port'      => 3306,
    'name' 		=> 'wow_data', 		
    'user' 		=> 'root', 
    'pass' 		=> '', 
    'encoding' 	=> 'utf8'
);