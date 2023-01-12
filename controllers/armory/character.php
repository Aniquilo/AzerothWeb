<?php

class Character extends Core_Controller
{
	private $canCache;
	
	private $guid;
    private $realmId;
    private $realm;
	private $realmName;

	private $name;
	private $class;
	private $className;
	private $race;
	private $raceName;
    private $level;
    private $avatar;
	private $account;
	private $gender;

	private $stats;
	private $items;
    private $iteminfos = array();
    private $itemsets = array();
    private $itemPlayerData = array();
    
	private $achievements;
	private $professions;
	
	private $talentTabs = array();
	private $talents = array();
	private $glyphs = array();
	private $talentSpecsInfo;
    
    private $dbcModel;

	function __construct()
	{
		parent::__construct();
		
		$this->canCache = true;
        $this->items = array();
        
        $this->loadLibrary('data.model.wotlk');
        $this->dbcModel = new data_model_wotlk();
	}

	/**
	 * Initialize
	 */
	public function index()
	{
        $realmId = (isset($_GET['realm']) ? (int)$_GET['realm'] : false);
        $guid = (isset($_GET['character']) ? $_GET['character'] : false);

        // Validate realmId
        if (!$this->realms->realmExists($realmId))
        {
            $this->tpl->Message('Error!', 'An error has occured!', 'Invalid realm id!');
        }

        // Get realm object
        $this->realm = $this->realms->getRealm($realmId);

        // Name to guid
		if (!is_numeric($guid))
        {
            $name = ucfirst($guid);

            if ($findGuid = $this->realm->getCharacters()->getCharacterData(false, $name, 'guid'))
            {
                $guid = (int)$findGuid['guid'];
            }
        }

		if (is_numeric($realmId) && is_numeric($guid))
		{
			$this->guid = $guid;
			$this->realmId = $realmId;
		}
		else
		{
			$this->realmId = false;
			$this->guid = false;
		}
		
		if ($this->guid != false)
		{
			$this->getProfile();
		}
		else
		{
			$this->tpl->Message('Error!', 'An error has occured!', 'The character could not be found!');
		}
    }
    
    /**
	 * Load the profile
	 * @return String
	 */
	private function getProfile()
	{
        $charData = false;
		$CacheKey = "characters/character_" . ($this->realmId ? $this->realmId : 0) . "_" . $this->guid;
		$cache = $this->cache->get($CacheKey);

		if ($cache !== false)
		{
			$charData = $cache;
		}
		else
		{
            // Make sure characters database is available
            if (!$this->realm->checkCharactersConnection())
            {
                $this->tpl->Message('Error!', 'An error has occured!', 'The armory is currently unavailable!');
            }

            // Load all items and info
            $this->getInfo();
            
            // Load the info about the talents
            $this->getTalentInfo();
            
            // Load professions
            $this->getProfessionsInfo();
            
            $charData = array(
                "name" => $this->name,
                "race" => $this->race,
                "faction" => $this->getFactionName($this->race),
                "avatar" => $this->avatar,
                "class" => $this->class,
                "level" => $this->level,
                "gender" => $this->gender,
                "items" => $this->items,
                "guild" => $this->guild,
                "pvp" => $this->pvp,
                "raceName" => $this->raceName,
                "className" => $this->className,
                "className_clean" => str_replace(' ', '', strtolower($this->className)),
                "realmName" => $this->realmName,
                "health" => (isset($this->stats['maxhealth']) ? $this->stats['maxhealth'] : 'Unknown'),
                "stats" => $this->prepareStats($this->stats),
                "secondBar" => $this->secondBar,
                "secondBarValue" => $this->secondBarValue,
                "realmId" => $this->realmId,
                //Talent & Glyph tables
                "talent_active_spec" => $this->getActiveSpec(),
                "talent_tables" => $this->getTalentTables(),
                "talent_specs" => $this->getSpecsTable(),
                "glyph_tables" => $this->getGlyphTables(),
                //achievements
                "recent_achievements" => $this->achievements,
                //professions
                "main_professions" => $this->professions['main'],
                "secondary_professions" => $this->professions['secondary'],
                //Get the arena teams
                "arena_teams_table" => $this->getArenaTeamsTable(),
                //Define the expansion to use for design crap
                "expansion_str" => ($this->isCataclysm() ? 'cata' : 'wotlk')
            );

            if ($this->canCache)
            {
                // Cache for 30 min
                $this->cache->store($CacheKey, $charData, 60 * 30);
            }
		}

        $this->tpl->SetTitle($this->name);
        $this->tpl->SetSubtitle('Armory');
        $this->tpl->AddCSS('template/style/armory-character.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('armory/character', $charData);

        $this->tpl->LoadFooter();
	}
    
    private function isCataclysm()
    {
        return false;
    }

	public function getItem($id, $slot = 0)
	{
        $iconCache = $this->realm->getIteminfo()->getIconCache($id);
        
        $rel = 'item='.$id.'&lvl='.$this->level;

        if (isset($this->itemPlayerData[$slot]))
        {
            $itemEnch = $this->itemPlayerData[$slot];

            if ($itemEnch['enchant'])
            {
                $rel .= '&ench='.$itemEnch['enchant']['id'];
            }

            if ($itemEnch['hasExtraSocket'])
            {
                $rel .= '&sock';
            }

            if ($itemEnch['gems'])
            {
                $gems = array();
                foreach ($itemEnch['gems'] as $gem)
                {
                    $gems[] = $gem['GemID'];
                }
                $rel .= '&gems='.implode(':', $gems);
                unset($gems);
            }
        }

        // Check for itemset
        if (isset($this->iteminfos[$slot]) && $this->iteminfos[$slot] && (int)$this->iteminfos[$slot]['itemset'] > 0)
        {
            $pcs = $this->itemsets[(int)$this->iteminfos[$slot]['itemset']];
            $rel .= '&pcs='.implode(':', $pcs);
        }

        if ($iconCache !== false)
        {
            return '<a href="' . item_url($id, $this->realmId) . '" 
                        rel="'.$rel.'" 
                        data-item-slot="'.$slot.'" 
                        data-realm="'.$this->realmId.'" 
                        target="_newtab">
                        <span class="icon" style="background-image: url(\'https://wow.zamimg.com/images/wow/icons/large/'.$iconCache.'.jpg\');"></span>
                    </a>';
        }
        else
        {
            $this->canCache = false;
            return '<a href="' . item_url($id, $this->realmId) . '" 
                        rel="'.$rel.'" 
                        data-item-slot="'.$slot.'" 
                        data-realm="'.$this->realmId.'" 
                        target="_newtab">
                        <span class="icon" style="background-image: url(\'https://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg\');" data-update-icon></span>
                    </a>';
        }
	}
    
    private function getDisplayName()
    {
        $res2 = $this->db->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :id LIMIT 1;");
        $res2->bindParam(':id', $this->account, PDO::PARAM_INT);
        $res2->execute();
        
        if ($res2->rowCount() > 0)
        {
            $row = $res2->fetch();
            $this->accountName = $row['displayName'];
            unset($row);
        }
        else
        {
            $this->accountName = 'Unknown';
        }
        unset($res2);
    }

	/**
	 * Get character info
	 */
	private function getInfo()
	{
        $characterData = $this->realm->getCharacters()->getCharacterData($this->guid);
        $characterStats = $this->realm->getCharacters()->getStats($this->guid);
        
		$this->pvp = array(
            'kills' => (array_key_exists("totalKills", $characterData)) ? $characterData['totalKills'] : false,
            'honor' => (array_key_exists("totalHonorPoints", $characterData)) ? $characterData['totalHonorPoints'] : false,
            'arena' => (array_key_exists("arenaPoints", $characterData)) ? $characterData['arenaPoints'] : false
        );

		// Assign the character data as real variables
		foreach ($characterData as $key => $value)
		{
			$this->$key = $value;
		}

		// Assign the character stats
		$this->stats = $characterStats;
	
		// Get the account display name
        $this->getDisplayName();

        // Get the character guild
        $this->guild = $this->realm->getCharacters()->getCharacterGuild($this->guid);

		$this->raceName = $this->realms->getRaceString($this->race);
		$this->className = $this->realms->getClassString($this->class);
		$this->realmName = $this->realm->getName();
        $this->avatar = $this->realms->getCharacterAvatar($characterData);
        
        // Find out which power field to use
        switch ($this->className)
        {
            default:
                $this->secondBar = "mana";
                $this->secondBarValue = $characterStats['maxpower1'];
                break;

            case "Warrior":
                $this->secondBar = "rage";
                $this->secondBarValue = $characterStats['maxpower2'];
                break;

            case "Hunter":
                $this->secondBar = "focus";
                $this->secondBarValue = $characterStats['maxpower3'];
                break;
            
            case "Rogue":
                $this->secondBar = "energy";
                $this->secondBarValue = $characterStats['maxpower4'];
                break;

            case "Death knight":
                $this->secondBar = "runic";
                $this->secondBarValue = $characterStats['maxpower7'];
                break;
        }

		// Load the items
		$items = $this->realm->getCharacters()->getItems($this->guid);

		// Item slots
		$slots = array(
            0 => "head",
            1 => "neck",
            2 => "shoulders",
            3 => "body",
            4 => "chest",
            5 => "waist",
            6 => "legs",
            7 => "feet",
            8 => "wrists",
            9 => "hands",
            10 => "finger1",
            11 => "finger2",
            12 => "trinket1",
            13 => "trinket2",
            14 => "back",
            15 => "mainhand",
            16 => "offhand",
            17 => "ranged",
            18 => "tabard"
        );

		if (is_array($items))
		{
			// Loop through to prepare item data
			foreach ($items as $item)
			{
				//skyfire and trinity
				if (isset($item['enchantments']))
				{
					$this->itemPlayerData[$item['slot']] = array(
						'enchant' 			=> $this->GetItemEnchant($item['enchantments']),
						'gems'				=> $this->GetItemGems($item['enchantments']),
						'hasExtraSocket'	=> $this->hasExtraSocket($item['enchantments']),
					);
                }
                
                $itemInfo = $this->realm->getIteminfo()->getInfo($item['itemEntry']);

                // Store iteminfo
                $this->iteminfos[$item['slot']] = $itemInfo;

                // Collect itemset pieces
                if ($itemInfo && (int)$itemInfo['itemset'] > 0)
                {
                    if (!isset($this->itemsets[(int)$itemInfo['itemset']]))
                        $this->itemsets[(int)$itemInfo['itemset']] = array();

                    $this->itemsets[(int)$itemInfo['itemset']][] = (int)$itemInfo['entry'];
                }

                unset($itemInfo);
            }
            
            // Loop through to assign the items
			foreach ($items as $item)
			{
				$this->items[$slots[$item['slot']]] = $this->getItem($item['itemEntry'], $item['slot']);
            }
            
            // Free up memory
            unset($this->iteminfos, $this->itemsets, $this->itemPlayerData);
		}

		// Loop through to make sure none are empty
		foreach ($slots as $key => $value)
		{
			if (!array_key_exists($value, $this->items))
			{
				switch($value)
				{
					default: $image = $value; break;
					case "trinket1": $image = "trinket"; break;
					case "trinket2": $image = "trinket"; break;
					case "finger1": $image = "finger"; break;
					case "finger2": $image = "finger"; break;
					case "back": $image = "chest"; break;
				}

                $this->items[$value] = '<span class="icon" style="background-image: url(\''.base_url().'/resources/armory/slots/'.$image.'.gif\');"></span>';
			}
		}
		
		//Get recent achievements
        $charRecentAchievements = $this->realm->getCharacters()->getRecentAchievements($this->guid);
        
		if ($charRecentAchievements)
		{
			$temp = array();
			
			//loop trough the char achievements and get some info
			foreach ($charRecentAchievements as $key => $achievementData)
			{
				//try getting some info about the achievement
				if ($achievementInfo = $this->dbcModel->getAchievementInfo($achievementData['achievement']))
				{
					//append the date of the achievement
					$achievementInfo['date'] = date("d/m/Y", $achievementData['date']);
                    $achievementInfo['url'] = wowdb_url($this->realmId).'/?achievement='.$achievementInfo['id'];
                    
					$achievementInfo['name_link'] = '<a href="'.$achievementInfo['url'].'" target="_new" rel="np">'.str_replace("'", "&prime;", $achievementInfo['name']).'</a>';
					
					//Determine which translation to use
					if ((int)$achievementInfo['points'] > 0)
					{
						$search = array('[NAME]', '[POINTS]');
						$replace = array($achievementInfo['name_link'], $achievementInfo['points']);
						//Prepare the text
						$achievementInfo['text'] = str_replace($search, $replace, 'Earned the achievement [NAME] for [POINTS] points.');
						unset($search, $replace);
					}
					else
					{
						$achievementInfo['text'] = str_replace('[NAME]', $achievementInfo['name_link'], 'Earned the achievement [NAME].');
					}
					
					//append to the achievements
					$temp[] = $achievementInfo;
				}
			}
			$this->achievements = $temp;
			
			unset($key, $achievementData, $achievementInfo, $temp);
		}
		else
		{
			$this->achievements = false;
		}
		
		unset($charRecentAchievements);
	}
    
    private function prepareStats($stats)
    {
        $statTabs = array(
            array(
                'strength' => array('name' => 'Strength', 'value' => $stats['strength']),
                'agility' => array('name' => 'Agility', 'value' => $stats['agility']),
                'intellect' => array('name' => 'Intellect', 'value' => $stats['intellect']),
                'stamina' => array('name' => 'Stamina', 'value' => $stats['stamina']),
                'spirit' => array('name' => 'Spirit', 'value' => $stats['spirit'])
            ),
            array(
                'armor' => array('name' => 'Armor', 'value' => $stats['armor']),
                'resilience' => array('name' => 'Resilience', 'value' => $stats['resilience']),
                'blockPct' => array('name' => 'Block', 'value' => $stats['blockPct']),
                'dodgePct' => array('name' => 'Dodge', 'value' => $stats['dodgePct']),
                'parryPct' => array('name' => 'Parry', 'value' => $stats['parryPct'])
            ),
            array(
                'attackPower' => array('name' => 'Attack Power', 'value' => $stats['attackPower']),
                'rangedAttackPower' => array('name' => 'Ranged Attack Power', 'value' => $stats['rangedAttackPower']),
                'spellPower' => array('name' => 'Spell Power', 'value' => $stats['spellPower'])
            ),
            array(
                'critPct' => array('name' => 'Critical', 'value' => $stats['critPct']),
                'rangedCritPct' => array('name' => 'Ranged Critical', 'value' => $stats['rangedCritPct']),
                'spellCritPct' => array('name' => 'Spell Critical', 'value' => $stats['spellCritPct'])
            )
        );

        return $statTabs;
    }

	private function getIcon($entry)
	{
		return $this->realm->getIteminfo()->getIcon($entry);
	}
	
	private function GetItemEnchant($enchantments)
	{
		//explode the item enchantments
		$enchantments = explode(' ', $enchantments);
		
		//make a little loop
		for ($i = 0; $i <= 5; $i++)
		{
			if ($enchantments[$i] != 0)
			{
				//return the first found enchant
				return $this->dbcModel->getEnchantmentInfo($enchantments[$i]);
			}
		}
		
		//as default no enchant
		return false;
	}
	
	private function hasExtraSocket($enchantments)
	{
		//explode the item enchantments
		$enchantments = explode(' ', $enchantments);
		
		if ((int)$enchantments[18] > 0)
		{
			switch ((int)$enchantments[18])
			{
				case 3319:
				case 3717:
				case 3723:
				case 3729:
				case 3848:
					return true;
				default:
					return false;
			}
		}
		
		return false;
	}
	
	private function GetItemGems($enchantments)
	{
		//explode the item enchantments
		$enchantments = explode(' ', $enchantments);
		
		//temp array
		$temp = array();
		
		//make a little loop, this should get max of 3 standart gems
		for ($i = 6; $i <= 14; $i++)
		{
			if ($enchantments[$i] != 0)
			{
				$info = $this->dbcModel->getEnchantmentInfo($enchantments[$i]);
				
				//verify that this is a gem and not a gem bonus
				if ((int)$info['GemID'] > 0)
				{
					//if meta, get conditions data
					if ((int)$info['color'] == 1 && (int)$info['EnchantmentCondition'] > 0)
					{
						//By default we have no required gems
						$requiries = $this->getMetaRequiries($info['EnchantmentCondition']);
					}
					
					//Try getting the icon
					if ((int)$info['GemID'] > 0)
					{
						if (!($icon = $this->getIcon((int)$info['GemID'])))
						{
							$icon = false;
						}
					}
					else
					{
						$icon = false;
					}
					
					//Set the gem slot
					switch ($i)
					{
						case 6: $slot = 0; break;
						case 9: $slot = 1; break;
						case 12: $slot = 2; break;
					}
					
					//add to the temp array
					$temp[] = array(
						'GemID'		=> (int)$info['GemID'],
						'icon'		=> $icon,
						'color'		=> (int)$info['color'],
						'requires'	=> (isset($requiries) ? $requiries : false),
						'slot'		=> $slot,
						'text'		=> $info['description']
					);
					
					unset($icon, $info);
				}
			}
		}
		
		if (!empty($temp))
			return $temp;
		
		//as default no gems
		return false;
	}
	
	private function GemColorFix($color)
	{
		switch ($color)
		{
			case 3: return 4;
			case 4: return 8;
		}
		
		return $color;
	}
	
	private function getMetaRequiries($ConditionId)
	{
		//By default we have no required gems
		$requiries = false;
		
		//Get the condition data
		$condition = $this->dbcModel->getEnchantmentConditions((int)$ConditionId);
		
		//Check what gems we require
		for ($i = 1; $i <= 5; $i++)
		{
			if (((int)$condition['Color' . $i] > 0 && (int)$condition['Value' . $i] > 0) || ((int)$condition['Color' . $i] > 0 && (int)$condition['CompareColor' . $i] > 0))
			{
				$requiries[] = array(
					'color' => $this->GemColorFix($condition['Color' . $i]), 
					'count' => $condition['Value' . $i], 
					'comparator' => $condition['Comparator' . $i], 
					'compareColor' => $this->GemColorFix($condition['CompareColor' . $i])
				);
			}
		}
		
		unset($condition);
		
		return $requiries;
	}
	
	private function getFactionName($race)
	{
		$faction = $this->realms->ResolveFaction($race);
		
		return ($faction == FACTION_ALLIANCE ? 'alliance' : 'horde');
	}
	
	private function getProfessionsInfo()
	{
		//Get the character's professions
        $professions = $this->realm->getCharacters()->getProfessions($this->guid);
        
		//Prevent undefined notice
		$this->professions['secondary'] = false;
		
		if ($professions)
		{
			//get the main professions
			foreach ($professions as $key => $row)
			{
                if ($info = $this->getProfessionInfo((int)$row['skill']))
				{
					$row = array(
						'skill' 	=> $row['skill'],
						'value' 	=> $row['value'],
						'max' 		=> $row['max'],
						'name' 		=> $info['name'],
						'icon' 		=> $info['icon'],
						'category' 	=> $info['category']
                    );
                    
                    //calculate the percentages
                    $row['percent'] = $this->percent($row['value'], $row['max']);
                    
                    if ($row['category'] === 0)
                    {
                        $this->professions['main'][] = $row;
                    }
                    else if ($row['category'] === 1)
                    {
                        $this->professions['secondary'][] = $row;
                    }
                }
			}
		}
		
		//make sure we have two records for them main profs
		for ($i = 0; $i < 2; $i++)
		{
			if (!isset($this->professions['main'][$i]))
				$this->professions['main'][$i] = false;
		}
		
		unset($professions);
	}
    
    //We can store the information about professions in array
	private function getProfessionInfo($id)
	{
		$data = array(
			//Primary
			164	=> array('name' => 'Blacksmithing', 	'icon' => 'Trade_BlackSmithing',			'category' => 0),
			165	=> array('name' => 'Leatherworking', 	'icon' => 'Trade_LeatherWorking',			'category' => 0),
			171	=> array('name' => 'Alchemy', 			'icon' => 'Trade_Alchemy',					'category' => 0),
			182	=> array('name' => 'Herbalism', 		'icon' => 'Trade_Herbalism',				'category' => 0),
			186	=> array('name' => 'Mining', 			'icon' => 'Trade_Mining',					'category' => 0),
			197	=> array('name' => 'Tailoring', 		'icon' => 'Trade_Tailoring',				'category' => 0),
			202	=> array('name' => 'Engineering', 		'icon' => 'Trade_Engineering',				'category' => 0),
			333	=> array('name' => 'Enchanting', 		'icon' => 'Trade_Engraving',				'category' => 0),
			393	=> array('name' => 'Skinning', 			'icon' => 'INV_Misc_Pelt_Wolf_01',			'category' => 0),
			755	=> array('name' => 'Jewelcrafting', 	'icon' => 'INV_Misc_Gem_01',				'category' => 0),
			773	=> array('name' => 'Inscription', 		'icon' => 'INV_Inscription_Tradeskill01',	'category' => 0),
			//Secondery
			129	=> array('name' => 'First Aid', 		'icon' => 'Spell_Holy_SealOfSacrifice',		'category' => 1),
			185	=> array('name' => 'Cooking', 			'icon' => 'INV_Misc_Food_15',				'category' => 1),
			356	=> array('name'	=> 'Fishing',			'icon' => 'Trade_Fishing',					'category' => 1),
			794 => array('name' => 'Archaeology', 		'icon' => 'trade_archaeology',				'category' => 1),
		);
		
		if (isset($data[(int)$id]))
		{
			return $data[(int)$id];
		}
		
		return false;
    }
    
	public function percent($num_amount, $num_total)
	{
		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = number_format($count2, 0);
		
		return $count;
	}
	
	private function getTalentInfo()
	{
		$tClass = false;
		
		switch ($this->class)
		{
			case 1: $tClass = 1; break;
			case 2: $tClass = 2; break;
			case 3: $tClass = 4; break;
			case 4: $tClass = 8; break;
			case 5: $tClass = 16; break;
			case 6: $tClass = 32; break;
			case 7: $tClass = 64; break;
			case 8: $tClass = 128; break;
			case 9: $tClass = 256; break;
			case 11: $tClass = 1024; break;
		}
		
		//Get the character's talent specs info
		$this->talentSpecsInfo = $this->realm->getCharacters()->getTalentSpecsInfo($this->guid);
		
		//loop the specs 
		for ($spec = 0; $spec < $this->talentSpecsInfo['talentGroupsCount']; $spec++)
		{
            //Get the character talents for the spec
            $talents = $this->realm->getCharacters()->getTalents($this->guid, $spec);
            if ($talents)
            {
                $this->talents[$spec] = array();
                foreach ($talents as $talentRow)
                {
                    $this->talents[$spec][] = $talentRow['spell'];
                }
            }
            else
            {
                $this->talents[$spec] = false;
            }
            unset($talents);

			//Get the glyphs for the spec
			$this->glyphs[$spec] = $this->getCharGlyphsTable($spec);
        }
        
		//Get the talent tree info
		$this->talentTabs = $this->dbcModel->getTalentTabs($tClass);
		
		//Get the base talents for each talent tree
		if (!empty($this->talentTabs))
		{
			foreach ($this->talentTabs as $key => $tab)
			{
				$this->talentTabs[$key]['talents'] = $this->dbcModel->getTalentsForTab($tab['id']);
			}
		}
		
		$this->getSpecsTable();
		
		unset($tClass);
	}
	
	private function getActiveSpec()
	{
		return $this->talentSpecsInfo['activeTalentGroup'];
	}
	
	private function getSpecsTable()
	{
		$table = false;
		
		//loop the specs 
		for ($spec = 0; $spec < $this->talentSpecsInfo['talentGroupsCount']; $spec++)
		{
			//defaults
			$table[$spec] = array(
				'title'		=> 'Undetermined',
				'icon'		=> false,
				'points'	=> '0/0/0',
				'active'	=> ($spec == $this->talentSpecsInfo['activeTalentGroup'] ? true : false),
				'mainTree'	=> false
			);
			$tabPoints = array();
			
			//loop the tabs and determine the points spend
			foreach ($this->talentTabs as $key => $tab)
			{
				$tabPoints[$tab['order']] = 0;
				
				foreach ($tab['talents'] as $talent)
				{
					$tabPoints[$tab['order']] = $tabPoints[$tab['order']] + $this->getPointsSpendOnTalent($talent, $spec);
				}
			}
			
			//determine the spec title
			//Check if any points are spend
			if ($tabPoints[0] > 0 || $tabPoints[1] > 0 ||$tabPoints[2] > 0)
			{
				//set the points string for the spec
				$table[$spec]['points'] = implode('/', $tabPoints);
				
				$temp = $tabPoints;
				arsort($temp);
				$mainTree = key($temp);
				unset($temp);
				
				$table[$spec]['mainTree'] = $mainTree;
				
				//find the main tab
				foreach ($this->talentTabs as $key => $tab)
				{
					if ($tab['order'] == $mainTree)
					{
						$table[$spec]['title'] = $tab['name'];
						$table[$spec]['icon'] = $tab['icon'];
						//break the loop
						break;
					}
				}
			}
			
			unset($tabPoints);
		}
		
		return $table;
	}
	
	private function getPointsSpendOnTalent($talent, $spec)
	{
		$points = 0;
		
		if (!$this->talents[$spec])
			return 0;
		
		//loop trough the talent ranks and check if the character has spend any points
		for ($i = 1; $i <= 5; $i++)
		{
			//check if the talent has this rank
			if ($talent['rank'.$i] != '0')
			{
				$search = array_search($talent['rank'.$i], $this->talents[$spec]);
				if ($search !== false)
				{
					$points = $i;
				}
				unset($search);
			}
			else
				break;
		}
		
		return $points;
	}
	
	private function getTalentTable($specId = 0)
	{
		$table = false;
		
		if (!empty($this->talentTabs))
		{
			$table = array();
			
			foreach ($this->talentTabs as $key => $tab)
			{
				//save some tab info
				$table[$key]['id'] = $tab['id'];
				$table[$key]['order'] = $tab['order'];
				$table[$key]['name'] = $tab['name'];
				$table[$key]['icon'] = $tab['icon'];
				//Now we create the talent table
				$table[$key]['table'] = array();
				//WOTLK
				//every talent tree has 11 rows
				//each row has 4 columns
				//CATA
				//every talent tree has 7 rows
				//each row has 4 columns
				$rowsToLoop = ($this->isCataclysm() ? 6 : 10);
				//let's dance tango
				for ($row = 0; $row <= $rowsToLoop; $row++)
				{
					//loop trough the columns
					for ($col = 0; $col <= 3; $col++)
					{
						//column has no talent by default
						if (!isset($table[$key]['table'][$row][$col]))
							$table[$key]['table'][$row][$col] = false;
						
						//check if this column has talent
						if ($talent = $this->findTalentRow($tab, $row, $col))
						{
							//On patch 4.0.6a there are multiple records for the same position
							//try fixing the invalid records
							//check if the current talent row is valid by trying to get the talent icon
							if (!$this->getSpellIcon($talent['rank1']))
							{
								//erase the record for this talent
								$tab = $this->eraseTabTalent($talent, $tab);
								//get the next record
								$talent = $this->findTalentRow($tab, $row, $col);
								//verify it
								if (!$talent)
									continue;
							}
							
							//Set the character data for this talent
							$charTalentData = $this->findCharacterTalentData($talent, $specId);
							
							//First check if we have arrows preset
							if ($table[$key]['table'][$row][$col])
								$charTalentData['arrows'] = $table[$key]['table'][$row][$col]['arrows'];
							
							$table[$key]['table'][$row][$col] = $charTalentData;
							
							unset($charTalentData);
							
							//Get the spell icon
							$table[$key]['table'][$row][$col]['icon'] = $this->getSpellIcon($talent['rank1']);
							
							//Check if this talent is dependant
							if ((int)$talent['dependsOn'] > 0)
							{
								if ($dependency = $this->findTalentDependency($talent, $key))
								{
									//arrow defaults
									$arrowData = array('pointing' => '');
									
									//Let's find out how to place the dependency arrow
									//first determine if there is need for horizontal arrow
									if ($dependency['col'] != $talent['col'])
									{
										//determine if it's pointing left
										if ($dependency['col'] > $talent['col'])
											$arrowData['pointing'] = 'left';
										else
											$arrowData['pointing'] = 'right';
										
										//determine if we should point down aswell
										if ($dependency['row'] != $talent['row'])
										{
											$arrowData['pointing'] = $arrowData['pointing'] . 'down';
											//determine how many rows down we should go
											$arrowData['rows'] = (($talent['row'] + 1) - ($dependency['row'] + 1)) - 1;
										}
									}
									else
									//It's only vertically pointing
									{
										$arrowData['pointing'] = 'down';
										//determine how many rows down we should go
										$arrowData['rows'] = (($talent['row'] + 1) - ($dependency['row'] + 1)) - 1;
									}
									
									//save the arrow data
									$table[$key]['table'][$dependency['row']][$dependency['col']]['arrows'][] = $arrowData;
									
									unset($arrowData);
								}
								unset($dependency);
							}
						}
						unset($talent);
					}
					unset($col);
				}
				unset($row, $rowsToLoop);
			}
			unset($key, $tab);
		}
		
		return $table;
	}
	
	private function getTalentTables()
	{
		$table = false;
		
		//loop the specs 
		for ($spec = 0; $spec < $this->talentSpecsInfo['talentGroupsCount']; $spec++)
		{
			$table[$spec] = $this->getTalentTable($spec);
		}
		
		return $table;
	}
	
	private function getCharGlyphsTable($spec)
	{
		//Get some info about the glyphs and convert to table
		$charGlyphsData = $this->realm->getCharacters()->getGlyphs($this->guid, $spec);
		
		//handle glyph records for diferrent emulators
		return $this->getGlyphsTableTrinity($charGlyphsData);
	}
	
	//TrinityCore and Skyfire
	private function getGlyphsTableTrinity($data)
	{
		//determine the glyphs count
		$glyphs = (isset($data['glyph9']) ? 9 : 6);
			
		$temp = array();
		//let's make it an array with record for each glyph
		for ($i = 1; $i <= $glyphs; $i++)
		{
			$glyphId = $data['glyph'.$i];
			
			if ((int)$glyphId > 0)
				$temp[] = $this->dbcModel->getGlyphInfo($glyphId);
			else
				$temp[] = false;
		}
		unset($i, $glyphs, $glyphId, $data);
		
		return $temp;
	}
	
	private function getGlyphTable($spec)
	{
		$table = array(
			'minor'		=> array(),
			'major'		=> array(),
			'hasPrime'	=> false,
			'prime'		=> array(),
		);
		
		if (isset($this->glyphs[$spec]) && $this->glyphs[$spec] !== false)
		{
			$table['hasPrime'] = ($this->isCataclysm() ? true : false);
			
			//Loop the glyphs to do some cosmetics
			foreach ($this->glyphs[$spec] as $key => $glyph)
			{
				if ($glyph)
				{
					//flag 2 = prime
					//flag 1 = minor
					//flag 0 = major
					if (($glyph['typeflags'] & 2) == 2)
					{
						$glyph['icon'] = 'inv_glyph_prime'.str_replace(' ', '', strtolower($this->className));
						//push to the table
						$table['prime'][] = $glyph;
					}
					else if (($glyph['typeflags'] & 1) == 1)
					{
						$glyph['icon'] = 'inv_glyph_minor'.str_replace(' ', '', strtolower($this->className));
						//push to the table
						$table['minor'][] = $glyph;
					}
					else
					{
						$glyph['icon'] = 'inv_glyph_major'.str_replace(' ', '', strtolower($this->className));
						//push to the table
						$table['major'][] = $glyph;
					}
				}
			}
		}
		
		//fill in the gaps
		while (count($table['minor']) < 3)
			$table['minor'][] = array('id' => 0, 'spellid' => 0, 'name' => 'Empty', 'icon' => 'inventoryslot_empty');
			
		while (count($table['major']) < 3)
			$table['major'][] = array('id' => 0, 'spellid' => 0, 'name' => 'Empty', 'icon' => 'inventoryslot_empty');
			
		while (count($table['prime']) < 3)
			$table['prime'][] = array('id' => 0, 'spellid' => 0, 'name' => 'Empty', 'icon' => 'inventoryslot_empty');
		
		return $table;
	}
	
	private function getGlyphTables()
	{
		$table = false;
		
		//loop the specs 
		for ($spec = 0; $spec < $this->talentSpecsInfo['talentGroupsCount']; $spec++)
		{
			$table[$spec] = $this->getGlyphTable($spec);
		}
		
		return $table;
	}
	
	private function findTalentDependency($talentData, $tabId)
	{
		if (isset($this->talentTabs[$tabId]))
		{
			foreach ($this->talentTabs[$tabId]['talents'] as $talent)
			{
				if ($talent['id'] == $talentData['dependsOn'])
				{
					return $talent;
				}
			}
		}
		
		return false;
	}
	
	private function findTalentRow($tab, $row, $col)
	{
		foreach ($tab['talents'] as $key => $talent)
		{
			if ($talent['row'] == $row)
			{
				if ($talent['col'] == $col)
				{
					return $talent;
				}
			}
		}
		
		return false;
	}
	
	private function eraseTabTalent($rTalent, $tab)
	{
		$new = array();
		
		foreach ($tab['talents'] as $key => $talent)
		{
			if ($talent['rank1'] == $rTalent['rank1'] && $talent['rank2'] == $rTalent['rank2'])
			{
				//this is the invalid talent, skip it
				continue;
			}
			
			//push to the new table
			$new[] = $talent;
		}
		
		$tab['talents'] = $new;
		
		return $tab;
	}
	
	private function findCharacterTalentData($talent, $spec)
	{
		//By default we return the first talent rank with zero points spend
		$return = array(
			'spell'		=> $talent['rank1'],
			'points'	=> 0,
			'max_rank'	=> 0
		);
		
		//loop trough the talent ranks and check if the character has spend any points
		for ($i = 1; $i <= 5; $i++)
		{
			//check if the talent has this rank
			if ($talent['rank'.$i] != '0')
			{
				if (isset($this->talents[$spec]) && $this->talents[$spec])
				{
					$search = array_search($talent['rank'.$i], $this->talents[$spec]);
					if ($search !== false)
					{
						$return['spell'] = $talent['rank'.$i];
						$return['points'] = $i;
					}
					unset($search);
				}
				
				$return['max_rank'] = $i;
			}
			else
				break;
			}
		
		return $return;
	}
	
	private function getSpellIcon($spell)
	{
		if ($icon = $this->dbcModel->getSpellIcon($spell))
			return str_replace(' ', '-', $icon['icon']);
			
		return false;
	}
	
	private function getArenaTeamsTable()
	{
		$teams = array(
            0 => $this->realm->getCharacters()->getArenaTeam($this->guid, 2),
            1 => $this->realm->getCharacters()->getArenaTeam($this->guid, 3),
            2 => $this->realm->getCharacters()->getArenaTeam($this->guid, 5)
        );

        foreach ($teams as $i => $team)
        {
            if ($team)
            {
                $members = $this->realm->getCharacters()->getArenaTeamMembers($team['arenateamid']);

                if ($members)
                {
                    foreach ($members as $i2 => $member)
                    {
                        $members[$i2]['className'] = $this->realms->getClassString($member['class']);
                        $members[$i2]['raceName'] = $this->realms->getRaceString($member['race']);

                        if ((int)$member['guid'] == $this->guid)
                        {
                            $teams[$i]['player'] = $member;
                            unset($members[$i2]);
                        }
                    }
                }

                $teams[$i]['members'] = $members;
            }
        }

        return $teams;
	}
}