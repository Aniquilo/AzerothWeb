<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['IMPORTANT_NOTICE_ENABLE'] = true;
$config['IMPORTANT_NOTICE_MESSAGE'] = '<p><strong>Please note!</strong> This is an example website notice message.</p>';
