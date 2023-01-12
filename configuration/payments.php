<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['coins_name'] = 'Warcry Gold Coins';

/*
 * STRIPE CONFIG
 */
$config['stripe']['enabled'] = false;
$config['stripe']['api_key'] = 'sk_test_n8zOVLXVvlXEwbp9MPFFWWbW002PeV2ERB';
$config['stripe']['pub_key'] = 'pk_test_5GniGRGFGyePaJKgKgEr4bfS00Q16LU2p2';

/*
 * PAYMENTWALL CONFIG 
 */
$config['paymentwall']['enabled'] = false;
$config['paymentwall']['secret_key'] = '';
// IPN URL is "(base_url)/ipn/paymentwall"

/*
 * PAYPAL CONFIG
 */
$config['paypal']['enabled'] = true;
$config['paypal']['url'] = 'https://www.paypal.com/cgi-bin/webscr'; //change for sandbox testing
$config['paypal']['email'] = 'ernestoal2014@gmail.com';
$config['paypal']['currecy'] = 'USD';
$config['paypal']['currecySymbol'] = '$';

/*
 * Donation Bonuses
 * For the specific range of purchased coins a bonus is added as reward in percentage
 */
$config['bonuses'] = array(
	// for the range delimeter use "-"
	// the bonus value is in percentages,
	// but do not use the symbol in this field
	array('range' => '50-99',   'bonus' => '5'),
	array('range' => '100-149', 'bonus' => '10'),
	array('range' => '150-199', 'bonus' => '15'),
	array('range' => '200-300', 'bonus' => '30'),
);