<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Enable or disable the service
$config['Enabled'] = false;

//Boosts Price per Duration
$config['Pricing'] = array(
	BOOST_DURATION_10 => array(CURRENCY_SILVER => 100, CURRENCY_GOLD => 10),
	BOOST_DURATION_15 => array(CURRENCY_SILVER => 140, CURRENCY_GOLD => 14),
	BOOST_DURATION_30 => array(CURRENCY_SILVER => 270, CURRENCY_GOLD => 27)
);