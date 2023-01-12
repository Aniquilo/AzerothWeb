<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//Realms configuration
$config[1] = array(
	'name' 			=> 'Fury', 
    'descr' 		=> 'Blizzlike High-Rate',
	'online_players' 		=> 'Connected players',
    
    'emulator'      => 'trinity', // Options: trinity, azerothcore
    
	'CharsDatabase' => array(
        'host' 		=> 'localhost', 
        'port'      => 3306,
		'name' 		=> 'characters', 		
		'user' 		=> 'root', 
		'pass' 		=> '', 
		'encoding' 	=> 'utf8'
    ),
    
	'WorldDatabase' => array(
        'host' 		=> 'localhost', 
        'port'      => 3306,
		'name' 		=> 'world', 		
		'user' 		=> 'root', 
		'pass' 		=> '', 
		'encoding' 	=> 'utf8'
    ), 
    
	'address' 		=> '127.0.0.1',
	'port' 			=> '8089',
	'soap_protocol' => 'http',
	'soap_address'  => '127.0.0.1',
    'soap_port'     => '7878',
	'soap_user'     => 'ADMIN',
    'soap_pass'     => 'ADMIN',
    
    'UPDATE_TIME' 	=> '10 minutes',
    
    // This is the wow database url, used for item links and such
    'wowdb_url'     => 'https://wotlkdb.com',	//(No slash at the end)
    'wowdb_lang'    => 'en', // You can use www, en, de, es, fr, ru

    // This is the background image used in the realm status
	// Use "wotlk", "pandaria" or "cataclysm"
    'background'	=> 'wotlk',
    
    //Realms information for the details page
    'info'          => array(
        'expansion' => 'Wrath of the Lich King',
        'short_description' => 'Blizzlike Content, High Rate Realm',
        'description' => "Experience Warth of the Lich King in a much faster pased environment with our High Rate Realm, Rage! Do you rather level a bit slower? No problem! We've developed our own <a href=\"".base_url()."/featured-addons\">AddOn</a> to help you change your Experience Rates. You can choose from 1x the regular XP rate all the way up to 15 times.<br/><br/>Rage is in constant development and issues are being resolved and working content is added all the time. Visit our Working Content page for a full overview of all the content available.",
    )
);