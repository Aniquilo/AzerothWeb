<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

/**
 * Recaptcha configuration settings
 * 
 * recaptcha_sitekey: Recaptcha site key to use in the widget
 * recaptcha_secretkey: Recaptcha secret key which is used for communicating between your server to Google's
 * lang: Language code, if blank "en" will be used
 * 
 * recaptcha_sitekey and recaptcha_secretkey can be obtained from https://www.google.com/recaptcha/admin/
 * Language code can be obtained from https://developers.google.com/recaptcha/docs/language
 * 
 * @author Damar Riyadi <damar@tahutek.net>
 */
$config['enabled']             = false;
$config['recaptcha_sitekey']   = '6LfoDaoUAAAAAD-J85RmlJRmXIfruHz5giYyt8aW';
$config['recaptcha_secretkey'] = '6LfoDaoUAAAAAP4W4FK8AlrFSw7lEnIMpZ5an5-P';
$config['lang']                = 'en';