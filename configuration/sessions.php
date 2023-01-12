<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

#define the session handler
# - NONE : Normal sessions
# - MCRYPT : Filesystem stored session using mcrypt to encrypt/decrypt the data
$config['SESSION_HANDLER'] = 'NONE'; 

#Define the session life time [How long can the session stay alive if not refreshed]
$config['SESSION_LIFETIME'] = "1 hour"; //time in string example: "1 minute"

#Define the interval of time to regenerate the session id
$config['SESSION_REGEN_TIME'] = '1 minute'; //time in string example: "1 minute"

#Define the session cookie name
$config['SESSION_COOKIE'] = 'warcry_wow_sess';

#Define the session cookie path
$config['SESSION_COOKIE_PATH'] = '/';

#Define the session cookie domain
$config['SESSION_COOKIE_DOMAIN'] = '';

#Define the session cookie secure true or false
$config['SESSION_COOKIE_SECURE'] = false; // only enable if you have ssl

#Define the session cookie httponly true or false
$config['SESSION_COOKIE_HTTPONLY'] = true; // if enabled the cookie wont be accessible by javascript