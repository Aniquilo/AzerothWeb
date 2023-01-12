<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Authentication Emulator
$config['emulator'] = 'trinity';

// Bnet, whether your emulator uses bnet accounts or not
$config['bnet'] = false;

// Expansion ID used for register
$config['expansion'] = 2;

// Do account require activation by email
$config['activation'] = false;

// Auth database config
$config['DatabaseHost'] = 'localhost';
$config['DatabasePort'] = 3306;
$config['DatabaseUser'] = 'root';
$config['DatabasePass'] = '';
$config['DatabaseName'] = 'trinity_auth';
$config['DatabaseEncoding'] = 'utf8';