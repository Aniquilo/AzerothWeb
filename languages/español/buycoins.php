<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Main page
$lang['get_gold_coins'] = 'Consigue monedas de oro';
$lang['select_payment_method'] = 'Seleccione el método de pago que desea utilizar.';
$lang['please_read_info'] = 'Antes de comprar, tómese un momento para ver nuestros <a href="'.base_url().'/tos">Términos de Uso</a> y <a href="'.base_url().'/rules">Reglas y Regulaciones</a>.';

// Paypal page
$lang['you_will_be_charged_via'] = 'You will be charged <br/>via PayPal';
$lang['you_will_be_charged_in'] = 'You will be charged in';
$lang['remember_refunds'] = 'Recuerde que todos los reembolsos deben ser aprobados por nosotros.';
$lang['purchase'] = 'Compra';

// Success
$lang['payment_complete'] = 'Pago completo';
$lang['payment_complete_info'] = '¡Gracias por apoyar a nuestro servidor! Si no recibe sus monedas de oro en 1 hora, comuníquese con un maestro del juego.';