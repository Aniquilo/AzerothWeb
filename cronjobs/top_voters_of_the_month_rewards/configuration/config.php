<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

define('init_config', true);

include 'basic.php';
include 'database.php';
