<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//The support email, used as Sender for the WAC mails
$config['Email'] = 'info@localhost';

//Time zone
$config['TimeZone'] = 'Europe/Berlin';
