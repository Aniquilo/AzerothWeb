<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

interface emulator_Realmstats
{
    public function __construct($realmId);
    public function getStatus();
    public function getUptime();
    public function getOnline();
    public function GetRealmDetails();
}