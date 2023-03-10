<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Ranking System
define('RANK_ROOKIE', 0);
define('RANK_PARTICIPANT', 1);
define('RANK_MEMBER', 2);
define('RANK_VETERAN', 3);
define('RANK_SENIOR_MEMBER', 4);
define('RANK_ADDICT', 5);
//Staff Ranks
define('RANK_STAFF_MEMBER', 6);

//Avatar System
define('AVATAR_TYPE_GALLERY', 0);
define('AVATAR_TYPE_UPLOAD', 1);

//characters
define('FACTION_ALLIANCE', 1);
define('FACTION_HORDE', 2);

//media types
define('TYPE_SCREENSHOT', 1);
define('TYPE_WALLPAPER', 2);

//screenshop status types
define('SCREENSHOT_STATUS_PENDING', 0);
define('SCREENSHOT_STATUS_APPROVED', 1);
define('SCREENSHOT_STATUS_DENIED', 2);

//Currencies
define("CURRENCY_SILVER", 1);
define("CURRENCY_GOLD", 2);

//coin activity
define('CA_SOURCE_TYPE_NONE', 0);
define('CA_SOURCE_TYPE_PURCHASE', 1);
define('CA_SOURCE_TYPE_REWARD', 2);
define('CA_SOURCE_TYPE_DEDUCTION', 3);
define('CA_COIN_TYPE_SILVER', 1);
define('CA_COIN_TYPE_GOLD', 2);
define('CA_EXCHANGE_TYPE_PLUS', 1);
define('CA_EXCHANGE_TYPE_MINUS', 2);

//WAC System
define('WAC_SIGN_STATUS_PENDING', 0);
define('WAC_SIGN_STATUS_WON', 1);
define('WAC_GEAR_TYPE_PVE', 1);
define('WAC_GEAR_TYPE_PVP', 2);
define('WAC_FACTION_ALLIANCE', 1);
define('WAC_FACTION_HORDE', 2);
define('WAC_FACTION_BOTH', 3);

//RAF System
define('RAF_LINK_PENDING', 0);
define('RAF_LINK_ACTIVE', 1);

//Social Networks
define('APP_FACEBOOK', 1);
define('APP_TWITTER', 2);
define('STATUS_POSITIVE', 1);
define('STATUS_NEGATIVE', 0);

//Changelogs
define('CHANGELOG_WEB', 1);
define('CHANGELOG_CORE', 2);
define('CHANGELOG_PERPAGE', 30);

//Transaction Logs
define('TRANSACTION_LOG_TYPE_NONE', 0);
define('TRANSACTION_LOG_TYPE_NORMAL', 1);
define('TRANSACTION_LOG_TYPE_URGENT', 2);

//Bug tracker
//Main Categories
define('BT_CAT_WEBSITE', 1);
define('BT_CAT_WOTLK_CORE', 2);
//issue approval statuses
define('BT_APP_STATUS_PENDING', 0);
define('BT_APP_STATUS_APPROVED', 1);
define('BT_APP_STATUS_DECLINED', 2);
//bug priorities
define('BT_PRIORITY_NONE', 0);
define('BT_PRIORITY_LOW', 1);
define('BT_PRIORITY_NORMAL', 2);
define('BT_PRIORITY_HIGH', 3);
//bug statuses
define('BT_STATUS_NEW', 0);
define('BT_STATUS_OPEN', 1);
define('BT_STATUS_ONHOLD', 2);
define('BT_STATUS_DUPLICATE', 3);
define('BT_STATUS_INVALID', 5);
define('BT_STATUS_WONTFIX', 6);
define('BT_STATUS_RESOLVED', 7);

//Item Refund System
define('IRS_STATUS_NONE', 0);
define('IRS_STATUS_REFUNDED', 1);
define('IRS_STATUS_ERROR', 2);

########################################
### FORUM FLAGS SECTION ################

//Forum flags
define('WCF_FLAGS_CLASSES_LAYOUT', 1);

//Topic flags

//Post flags
define('WCF_FLAGS_STAFF_POST', 1);


