<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Records Display Limits
$config['FORUM']['Topics_Limit'] 	= 20;
$config['FORUM']['Posts_Limit'] 	= 10;