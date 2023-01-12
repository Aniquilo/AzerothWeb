<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Used to check the logon server status
$config['LogonServer'] = array(
    'host' => '127.0.0.1', 
    'port'	=> 3724,
);