<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

interface emulator_Iteminfo
{
    public function __construct($realmId);
    public function getInfo($entry);
    public function getInfoCache($entry);
    public function getIcon($entry);
    public function getIconCache($entry);
}