<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class StoreCategories
{
	public $data = array(
		/*array(
			'id'	=> 2,
			'name'  => 'Weapons',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'One-Handed Axes'),
				array('id' => '1', 'name' => 'Two-Handed Axes'),
				array('id' => '2', 'name' => 'Bows'),
				array('id' => '3', 'name' => 'Guns'),
				array('id' => '4', 'name' => 'One-Handed Maces'),
				array('id' => '5', 'name' => 'Two-Handed Maces'),
				array('id' => '6', 'name' => 'Polearms'),
				array('id' => '7', 'name' => 'One-Handed Swords'),
				array('id' => '8', 'name' => 'Two-Handed Swords'),
				//array('id' => '9', 'name' => 'Obsolete'),
				array('id' => '10', 'name' => 'Staves'),
				array('id' => '11', 'name' => 'One-Handed Exotics'),
				array('id' => '12', 'name' => 'Two-Handed Exotics'),
				array('id' => '13', 'name' => 'Fist Weapons'),
				//array('id' => '14', 'name' => 'Miscellaneous'),
				array('id' => '15', 'name' => 'Daggers'),
				array('id' => '16', 'name' => 'Thrown'),
				array('id' => '17', 'name' => 'Spears'),
				array('id' => '18', 'name' => 'Crossbows'),
				array('id' => '19', 'name' => 'Wands'),
				array('id' => '20', 'name' => 'Fishing Poles'),
			)
		),
		array(
			'id'	=> 4,
			'name'  => 'Armor',
			'sub_categories' => array(
				array('id' => '1', 'name' => 'Cloth'),
				array('id' => '2', 'name' => 'Leather'),
				array('id' => '3', 'name' => 'Mail'),
				array('id' => '4', 'name' => 'Plate'),
				//array('id' => '5', 'name' => 'Bucklers'),
				array('id' => '6', 'name' => 'Shields'),
				array('id' => '7', 'name' => 'Librams'),
				array('id' => '8', 'name' => 'Idols'),
				array('id' => '9', 'name' => 'Totems'),
				array('id' => '10', 'name' => 'Sigils'),
				array('id' => '0', 'name' => 'Miscellaneous'),
			)
		),
		array(
			'id'	=> 1,
			'name'  => 'Containers',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Bags'),
				array('id' => '1', 'name' => 'Soul Bags'),
				array('id' => '2', 'name' => 'Herb Bags'),
				array('id' => '3', 'name' => 'Enchanting Bags'),
				array('id' => '4', 'name' => 'Engineering Bags'),
				array('id' => '5', 'name' => 'Gem Bags'),
				array('id' => '6', 'name' => 'Mining Bags'),
				array('id' => '7', 'name' => 'Leatherworking Bags'),
				array('id' => '8', 'name' => 'Inscription Bags'),
			)
		),
		array(
			'id'	=> 0,
		 	'name'  => 'Consumables',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Consumables'),
				array('id' => '1', 'name' => 'Potions'),
				array('id' => '2', 'name' => 'Elixirs'),
				array('id' => '3', 'name' => 'Flasks'),
				array('id' => '4', 'name' => 'Scrolls'),
				array('id' => '5', 'name' => 'Food & Drink'),
				array('id' => '6', 'name' => 'Item Enhancements'),
				array('id' => '7', 'name' => 'Bandages'),
				array('id' => '8', 'name' => 'Other'),
			)
		),
		array(
			'id'	=> 16,
			'name'  => 'Glyphs',
			'sub_categories' => array(
				array('id' => '1', 'name' => 'Warrior'),
				array('id' => '2', 'name' => 'Paladin'),
				array('id' => '3', 'name' => 'Hunter'),
				array('id' => '4', 'name' => 'Rogue'),
				array('id' => '5', 'name' => 'Priest'),
				array('id' => '6', 'name' => 'Death Knight'),
				array('id' => '7', 'name' => 'Shaman'),
				array('id' => '8', 'name' => 'Mage'),
				array('id' => '9', 'name' => 'Warlock'),
				array('id' => '11', 'name' => 'Druid'),
			)
		),
		array(
			'id'	=> 7,
			'name'  => 'Trade Goods',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Trade Goods'),
				array('id' => '1', 'name' => 'Parts'),
				array('id' => '2', 'name' => 'Explosives'),
				array('id' => '3', 'name' => 'Devices'),
				array('id' => '4', 'name' => 'Jewelcrafting'),
				array('id' => '5', 'name' => 'Cloth'),
				array('id' => '6', 'name' => 'Leather'),
				array('id' => '7', 'name' => 'Metal & Stone'),
				array('id' => '8', 'name' => 'Meat'),
				array('id' => '9', 'name' => 'Herb'),
				array('id' => '10', 'name' => 'Elemental'),
				array('id' => '11', 'name' => 'Other'),
				array('id' => '12', 'name' => 'Enchanting'),
				array('id' => '13', 'name' => 'Materials'),
				array('id' => '14', 'name' => 'Armor Enchantment'),
				array('id' => '15', 'name' => 'Weapon Enchantment'),
			)
		),
		array(
			'id'	=> 9,
			'name'  => 'Recipes',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Books'),
				array('id' => '1', 'name' => 'Leatherworking'),
				array('id' => '2', 'name' => 'Tailoring'),
				array('id' => '3', 'name' => 'Engineering'),
				array('id' => '4', 'name' => 'Blacksmithing'),
				array('id' => '5', 'name' => 'Cooking'),
				array('id' => '6', 'name' => 'Alchemy'),
				array('id' => '7', 'name' => 'First Aid'),
				array('id' => '8', 'name' => 'Enchanting'),
				array('id' => '9', 'name' => 'Fishing'),
				array('id' => '10', 'name' => 'Jewelcrafting'),
				array('id' => '11', 'name' => 'Inscription'),
			)
		), 
		array(
			'id'	=> 3,
			'name'  => 'Gems',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Red'),
				array('id' => '1', 'name' => 'Blue'),
				array('id' => '2', 'name' => 'Yellow'),
				array('id' => '3', 'name' => 'Purple'),
				array('id' => '4', 'name' => 'Green'),
				array('id' => '5', 'name' => 'Orange'),
				array('id' => '6', 'name' => 'Meta'),
				array('id' => '7', 'name' => 'Simple'),
				array('id' => '8', 'name' => 'Prismatic'),
			)
		),
		array(
			'id'	=> 15,
			'name'  => 'Miscellaneous',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Junk'),
				array('id' => '1', 'name' => 'Reagents'),
				array('id' => '2', 'name' => 'Pets'),
				array('id' => '3', 'name' => 'Holiday'),
				array('id' => '4', 'name' => 'Other'),
				array('id' => '5', 'name' => 'Mounts'),
			)
		),*/
		
	
		/*array(
			'id'	=> 0,
			'name'  => 'Armas PVE',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Arcos'),
				array('id' => '1', 'name' => 'Armas de Asta'),
				array('id' => '2', 'name' => 'Armas de Fuego'),
				array('id' => '3', 'name' => 'Armas de Puño'),
				array('id' => '4', 'name' => 'Arrojadizas'),
				array('id' => '5', 'name' => 'Bastones'),
			)
		),
		array(
			'id'	=> 1,
			'name'  => 'Armas PVP',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Arcos'),
				array('id' => '1', 'name' => 'Armas de Asta'),
				array('id' => '2', 'name' => 'Armas de Fuego'),
				array('id' => '3', 'name' => 'Armas de Puño'),
				array('id' => '4', 'name' => 'Arrojadizas'),
				array('id' => '5', 'name' => 'Bastones'),
			)
		),
		array(
			'id'	=> 2,
			'name'  => 'Armaduras PVE',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Cascos'),
			)
		),
		array(
			'id'	=> 3,
			'name'  => 'Armaduras PVP',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Cascos'),
			)
		),
		array(
			'id'	=> 4,
			'name'  => 'Accesorios PVE',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Collares'),
			)
		),
		array(
			'id'	=> 5,
			'name'  => 'Accesorios PVP',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Collares'),
			)
		),*/
		array(
			'id'	=> 6,
			'name'  => 'Items de Leveo',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Abalorios PVE'),
				array('id' => '1', 'name' => 'Abalorios PVP'),
				array('id' => '2', 'name' => 'Armas PVE'),
				array('id' => '3', 'name' => 'Armas PVP'),
				array('id' => '4', 'name' => 'Hombreras PVE'),
				array('id' => '5', 'name' => 'Hombreras PVP'),
				array('id' => '6', 'name' => 'Pecheras PVE'),
			)
		),
		array(
			'id'	=> 7,
			'name'  => 'Contenedores',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Bolsas'),
				/*array('id' => '1', 'name' => 'Bolsa de Almas'),
				array('id' => '2', 'name' => 'Bolsa de profesiones'),
				array('id' => '3', 'name' => 'Carcajs & Municiones'),*/
			)
		),
		array(
			'id'	=> 8,
			'name'  => 'Miselanea',
			'sub_categories' => array(
				array('id' => '0', 'name' => 'Monturas terrestres'),
				array('id' => '1', 'name' => 'Monturas Voladores'),
				array('id' => '2', 'name' => 'Mascotas'),
				array('id' => '3', 'name' => 'Tabardos'),
				array('id' => '4', 'name' => 'Varios'),
				array('id' => '5', 'name' => 'Piedra de hogar'),
				/*array('id' => '6', 'name' => 'Camisas'),*/
			)
		),			
		
		
		// Not included item classes
		/*
		array(
			'id'	=> 12,
			'name'  => 'Quest'
		),
		array(
			'id'	=> 5,
			'name'  => 'Reagents'
		),
		array(
			'id'	=> 6,
			'name'  => 'Projectiles',
			'sub_categories' => array(
			)
		),
		array(
			'id'	=> 8,
			'name'  => 'Generic(OBSOLETE)'
		),
		array(
			'id'	=> 10,
			'name'  => 'Money'
		), 
		array(
			'id'	=> 11,
			'name'  => 'Quiver'
		),
		array(
			'id'	=> 13,
			'name'  => 'Key'
		), 
		array(
			'id'	=> 14,
			'name'  => 'Permanent(OBSOLETE)'
		),
		*/
	);

	public function __construct() { }
	
	public function GetAll()
	{
		return $this->data;
	}
	
	public function __destruct()
	{
		unset($this->data);
	}
}