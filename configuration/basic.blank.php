<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['DEBUG'] = true;

$config['SiteName'] = 'Warcry WoW';
$config['BaseURL'] = 'http://localhost'; 	//(No slash at the end)

//Website language
$config['Language'] = 'english';

//Minifier Settings
$config['UseMinifier'] = true;

//E-mail Address used for sending emails
$config['Email'] = 'info@warcry-wow.com';

//Time settings
$config['TimeZone'] = 'Europe/Berlin';
$config['TimeZoneOffset'] = '+1';

//Google analytics
$config['GA_TrackingID'] = 'UA-XXXXXXXX-X';

//Some general info for the game
$config['realmlist'] = 'warcry-wow.com';
$config['ClientDownload'] = 'magnet:?xt=urn:btih:b296ea8947b36c68f6e022f5a642ecc406ad8968&dn=World%20of%20Warcraft%203.3.5a%20(no%20install)';

//Some SEO meta info
$config['MetaDescription'] = 'Welcome to the best free private server.';
$config['MetaKeywords'] = 'Warcry, Warcry-WoW, Warcry WoW, WoW, World of Warcraft, Warcraft, wotlk, Wrath of the Lich King, wotlk server, Private Server, Private WoW Server, WoW Server, WoW Private Server, win a character, splash, splash page, reward, rewards';
$config['MetaCopyright'] = 'Copyright www.warcry-wow.com';