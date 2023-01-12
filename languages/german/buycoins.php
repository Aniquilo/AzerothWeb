<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Main page
$lang['get_gold_coins'] = 'Get Gold Coins';
$lang['select_payment_method'] = 'Please select the payment method you would like to use.';
$lang['please_read_info'] = 'Please before purchasing take a moment to view our <a href="'.base_url().'/tos">Terms of Use</a> and <a href="'.base_url().'/rules">Rules & Regulations</a>.';

// Paypal page
$lang['you_will_be_charged_via'] = 'You will be charged <br/>via PayPal';
$lang['you_will_be_charged_in'] = 'You will be charged in';
$lang['remember_refunds'] = 'Please remember all refunds must be approved by us.';
$lang['purchase'] = 'Purchase';

// Success
$lang['payment_complete'] = 'Payment Complete';
$lang['payment_complete_info'] = 'Thanks for supporting our server! If you don\'t receive your gold coins with in 1 hour, please contact a game master.';