<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

define('init_config', true);

include_once 'basic.php';
include_once 'database.php';
include_once 'sessions.php';
include_once 'vote.php';
include_once 'important_notice.php';
include_once 'forum.php';
include_once 'social.php';
include_once 'tooltip_constants.php';
include_once 'wow_classes.php';