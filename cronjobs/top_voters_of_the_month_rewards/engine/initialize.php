<?php

require_once ROOTPATH . '/engine/core.php';

if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Start the core
$CORE = new CORE();
	
	//open database connection
	$DB = $CORE->DatabaseConnection();
		
//setup the security class
$SECURITY = new Security();