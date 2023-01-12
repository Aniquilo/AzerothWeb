<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//The cooldown to be applied on each voting site
$config['VOTE']['Cooldown'] = '24 hours';

//Points per Vote
$config['VOTE']['PPV'] = 2;

//Points to be rewareded to the Recruiter if the user has voted 5 times
$config['VOTE']['RAF_PR'] = 2;

//Lets people recieve points per IP only
$config['VOTE']['IP_CHECK'] = true;

//Define the vote sites
$config['VOTE']['Sites'] = array(
    1 => array('name' => 'ArenaTop100', 	'url' => 'https://www.arena-top100.com/index.php?a=in&u=warmashine', 		'img' => 'https://www.arena-top100.com/images/vote/wow-private-servers.png'),
	2 => array('name' => 'Gtop100', 	'url' => 'https://gtop100.com/topsites/World-of-Warcraft/sitedetails/Warmashine-WoW-100410?vote=1', 		'img' => 'https://warmashine.com//template/style/images/gtop100.jpg'),
	3 => array('name' => 'Top100Arena', 	'url' => 'https://www.top100arena.com/category/wow-private-server?vote=98839', 		'img' => 'https://www.top100arena.com/hit/98839/big'),
	4 => array('name' => 'Topg', 	'url' => 'https://topg.org/wow-private-servers/server-637279', 		'img' => 'https://topg.org/topg.gif'),	
);