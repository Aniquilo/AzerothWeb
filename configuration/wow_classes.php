<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['wow_races'] = array(
    1 => "Human",
	2 => "Orc",
	3 => "Dwarf",
	4 => "Night elf",
	5 => "Undead",
	6 => "Tauren",
	7 => "Gnome",
	8 => "Troll",
	9 => "Goblin",
	10 => "Blood elf",
	11 => "Draenei",
    22 => "Worgen",
    24 => "Pandaren",
	25 => "Alliance Pandaren",
	26 => "Horde Pandaren",
);

$config['alliance_races'] = array(1,3,4,7,11,22,25);
$config['horde_races'] = array(2,5,6,8,10,26);

$config['wow_classes'] = array(
	1 => 'Warrior',
	2 => 'Paladin',
	3 => 'Hunter',
	4 => 'Rogue',
	5 => 'Priest',
	6 => 'Death Knight',
	7 => 'Shaman',
	8 => 'Mage',
	9 => 'Warlock',
	10 => 'Monk',
	11 => 'Druid',
	12 => 'Demon Hunter',
);