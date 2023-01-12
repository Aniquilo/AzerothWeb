<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Grab database connections
// They should be already open
$DB = $CORE->DatabaseConnection();
unset($config['DatabaseName'], $config['DatabaseHost'], $config['DatabaseUser'], $config['DatabasePass'], $config['DatabaseEncoding']);

// Just a reference because of old code
$ERRORS = $CORE->errors;
$NOTIFICATIONS = $CORE->notifications;
$CACHE = $CORE->cache;