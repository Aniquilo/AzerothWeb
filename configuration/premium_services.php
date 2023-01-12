<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

// Enable or disable the teleporter service
$config['Teleporter_Enabled'] = true;

// Character services
$config['RaceChange_Price'] = 5; // Price in gold coins
$config['FactionChange_Price'] = 10; // Price in gold coins
$config['Recustomization_Price'] = 5; // Price in gold coins

// In-game Gold
$config['IGG_MaxAmount'] = 100000; // The maximum amount of gold that can be purchased
$config['IGG_PricePerThousand'] = 2; // Price in gold coins for 1,000 in-game gold

// Character levels
$config['LevelUp'] = array(
    array(
        'level' 		=> 60,
        'money' 		=> 20000000,    //2k gold (amount of gold x 10000)
        'bags' 			=> 4,           // How many bags
        'bagsId' 		=> 14155,       //Mooncloth Bag
        'price' 		=> 4,           // Price in gold coins
        'description'   => 'Level 60, 2k Gold and 4x 16 slot bags'
    ),
    array(
        'level' 		=> 70,
        'money' 		=> 30000000,    //3k gold
        'bags' 			=> 4,
        'bagsId' 		=> 14156,       //Bottomless Bag
        'price' 		=> 6,
        'description'   => 'Level 70, 3k Gold and 4x 18 slot bags'
    ),
    array(
        'level' 		=> 80,
        'money' 		=> 50000000,    //5k gold
        'bags' 			=> 4,
        'bagsId' 		=> 21876,       //Primal Mooncloth Bag
        'price' 		=> 8,
        'description'   => 'Level 80, 5k Gold and 4x 20 slot bags'
    )
);

// Display Name Change
$config['DNChange_PriceSilver'] = 100; // Price in silver
$config['DNChange_PriceGold'] = 10; // Price in gold