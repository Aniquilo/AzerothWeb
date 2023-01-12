<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

interface emulator_Commands
{
    public function __construct($realmId);
    public function ExecuteCommand($command);
    public function CheckConnection();
    public function sendItems($charName, $items, $subject);
    public function sendMoney($charName, $money, $subject);
    public function levelTo($charName, $level);
    public function FactionChange($charName);
    public function RaceChange($charName);
    public function Customize($charName);
    public function Revive($charName);
    public function Teleport($charName, $x, $y, $z, $mapId);
    public function RefundItem($entry, $charName);
}