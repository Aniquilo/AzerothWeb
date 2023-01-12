<?php
if (!defined('init_config'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$tooltips_config["bind"] = array(
	0 => null,
	1 => "Binds when picked up",
	2 => "Binds when equipped",
	3 => "Binds when used",
	4 => "Quest Item"
);

$tooltips_config["slots"] = array(
	0 => null,
	1 => "Head",
	2 => "Neck",
	3 => "Shoulder",
	4 => "Shirt",
	5 => "Chest",
	6 => "Waist",
	7 => "Legs",
	8 => "Feet",
	9 => "Wrists",
	10 => "Hands",
	11 => "Finger",
	12 => "Trinket",
	13 => "One-Hand",
	14 => "Shield",
	15 => "Ranged",
	16 => "Back",
	17 => "Two-Hand",
	18 => "Bag",
	19 => "Tabard",
	20 => "Robe",
	21 => "Main hand",
	22 => "Off hand",
	23 => "Holdable",
	24 => "Ammo",
	25 => "Thrown",
	26 => "Ranged",
	27 => "Quiver",
	28 => "Relic"
);

$tooltips_config["damages"] = array(
	0 => null, // Physical
	1 => "Holy",
	2 => "Fire",
	3 => "Nature",
	4 => "Frost",
	5 => "Shadow",
	6 => "Arcane"
);

$tooltips_config["spelltriggers"] = array(
	0 => "Use: ",
	1 => "Equip: ",
	2 => "Chance on hit: ",
	3 => "Unknown: ",
	4 => "Soulstone",
	5 => "Use: ",
	6 => "Teaches: "
);

$tooltips_config["armor_sub"] = array(
	0 => "Miscellaneous",
	1 =>  "Cloth",
	2 => "Leather",
	3 => "Mail",
	4 => "Plate",
	5 => null,
	6 => " Shield",
	7 => "Libram",
	8 => "Idol",
	9 =>  "Totem",
	10 => " Sigil",
);

$tooltips_config["weapon_sub"] = array(
	0 => "Axe",
	1 => "Axe",
	2 => "Bow",
	3 => "Gun",
	4 => "Mace",
	5 => "Mace",
	6 => "Polearm",	
	7 => "Sword",
	8 => "Sword",
	9 => "Obsolete",
	10 => "Staff",
	11 => "Exotic",
	12 => "Exotic",
	13 => "Fist Weapon",
	14 => "Miscellaneous",
	15 => "Dagger",
	16 => "Thrown",
	17 => "Spear",
	18 => "Crossbow",
	19 => "Wand",
	20 => "Fishing Pole"
);

$tooltips_config["stats"] = array(
	1 => "Mana",
	2 => "Health",
	3 => "Agility",
	4 => "Strength",
	5 => "Intellect",
	6 => "Spirit",
	7 => "Stamina",
	12 => "defense rating by %d.",
	13 => "dodge rating by %d.",
	14 => "parry rating by %d.",
	15 => "shield block rating by %d.",
	16 => "melee hit rating by %d.",
	17 => "ranged hit rating by %d.",
	18 => "spell hit rating by %d.",
	19 => "melee critical strike rating by %d.",
	20 => "ranged critical strike rating by %d.",
	21 => "spell critical strike rating by %d.",
	28 => "melee haste rating by %d.",
	29 => "ranged haste rating by %d.",
	30 => "spell haste rating by %d.",
	31 => "hit rating by %d.",
	32 => "critical strike rating by %d.",
	35 => "resilience rating by %d.",
	36 => "haste rating by %d.",
	37 => "expertise rating by %d.",
	38 => "attack power by %d.",
	39 => "ranged power by %d.",
	40 => "feral power by %d.",
	41 => "healing by %d.",
	42 => "damage with spells by %d.",
	43 => "restores %d mana per 5 sec.",
	44 => "armor penetration rating by %d.",
	45 => "spell power by %d.",
	46 => "restore %d health per 5 sec.",
	47 => "spell penetration rating by %d.",
	48 => "block value by %d.",
	49 => "mastery rating by %d."
);

$tooltips_config["bag_types"] = array(
	0 => 'Bag',
	1 => 'Quiver',
	2 => 'Ammo Pouch',
	4 => 'Soul Bag',
	8 => 'Leatherworking Bag',
	32 => 'Herb Bag',
	64 => 'Enchanting bag',
	128 => 'Engineering Bag',
	512 => 'Gem Bag',
	1024 => 'Mining Bag'
);