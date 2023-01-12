<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class trinity_Characters implements emulator_Characters
{
    private $core;
	private $realmId;
    private $DB;
    
	//The debuff applied to a dead character
	private $deathDebuffId = '8326';
        
    /**
	 * Array of table names
	 */
	protected $tables = array(
        "characters" => "characters",
        "guild_member" => "guild_member",
		"guild" => "guild",
		"gm_tickets" => "gm_ticket"
	);

	/**
	 * Array of column names
	 */
	protected $columns = array(

		"characters" => array(
			"guid" => "guid",
			"account" => "account",
			"name" => "name",
			"race" => "race",
			"class" => "class",
			"gender" => "gender",
			"level" => "level", 
			"zone" => "zone",
			"online" => "online",
			"money" => "money",
			"totalKills" => "totalKills",
			"arenaPoints" => "arenaPoints",
			"totalHonorPoints" => "totalHonorPoints",
			"position_x" => "position_x",
			"position_y" => "position_y",
			"position_z" => "position_z",
			"orientation" => "orientation",
			"map" => "map",
			"chosenTitle" => "chosenTitle"
		),
        
        "guild" => array(
			"guildid" => "guildid",
			"name" => "name",
			"leaderguid" => "leaderguid"
		),

		"guild_member" => array(
			"guildid" => "guildid",
			"guid" => "guid"
        ),
        
		"gm_tickets" => array(
			"ticketId" => "id",
			"guid" => "playerGuid",
            "message" => "description",
            "name" => "name",
			"createTime" => "createTime",
			"completed" => "completed",
            "closedBy" => "closedBy",
            "assignedTo" => "assignedTo",
            "comment" => "comment",
            "viewed" => "viewed"
		)
    );
    
	//constructor
	public function __construct($realmId)
	{
        $this->core =& get_instance();
        $this->realmId = $realmId;
        $this->DB = $this->core->realms->getRealm($realmId)->getCharactersConnection();

		return true;
	}
	
	public function getAccountCharacters($account = false)
	{
        if (!$account)
        {
		    $account = $this->core->user->get('id');
        }

		$res = $this->DB->prepare("SELECT `guid`, `name`, `level`, `race`, `class`, `gender` FROM `characters` WHERE `account` = :account ORDER BY `level` DESC, `name` ASC;");
		$res->bindParam(':account', $account, PDO::PARAM_INT);
		$res->execute();
				
		if ($res->rowCount() > 0)
		{
			return $res->fetchAll();
        }
        
		return false;
	}
	
	public function FindHightestLevelCharacter($acc)
	{
		$res = $this->DB->prepare("SELECT `guid`, `name`, `level`, `race`, `class`, `gender` FROM `characters` WHERE `account` = :account ORDER BY `level` DESC LIMIT 1;");
		$res->bindParam(':account', $acc, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$return = $res->fetch();
		}
		else
		{
			$return = false;
		}
		unset($res);
		
		return $return;
	}
	
	public function isMyCharacter($guid = false, $name = false, $account = false)
    {
		if ($guid === false && $name === false)
		{
			return false;
		}
		
		if (!$account)
			$account = $this->core->user->get('id');
		
		$res = $this->DB->prepare("SELECT `guid`, `account` FROM `characters` WHERE ".($guid === false ? "`name` = :name" : "`guid` = :guid")." AND `account` = :account LIMIT 1;");
		if ($guid !== false)
		{
			$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		}
		else
		{
			$res->bindParam(':name', $name, PDO::PARAM_STR);
		}
		$res->bindParam(':account', $account, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
			return false;
		  
      	return true;
    }

	public function getCharacterName($guid)
    {
		$res = $this->DB->prepare("SELECT `name` FROM `characters` WHERE `guid` = :guid LIMIT 1;");
		$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$res->execute();
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
		
    	if (!$row)
		{
      	  	return false;
		}
		  
        return $row['name'];
    }
	
	public function getCharacterData($guid = false, $name = false, $columns = 'all')
    {
		if ($guid === false && $name === false)
		{
			return false;
		}
        
        // Translate table columns
        $columnsData = $this->getAllColumns('characters');
        
        if ($columns == 'all')
        {
            $columns = array_keys($columnsData);
        }

		//empty string
		$queryColumns = "";
		
		//check if we wanna get multiple columns
		if (is_array($columns))
		{
			foreach ($columns as $key)
			{
				//check if it's valid key
				if (isset($columnsData[$key]))
				{
					$queryColumns .= "`" . $columnsData[$key] . "` AS " . $key . ", ";
				}
			}
			//check if the query has any valid columns at all
			if ($queryColumns != "")
			{
				//remove the last "," symbol from the query
				$queryColumns = substr($queryColumns, 0, strlen($queryColumns) - 2);
			}
			else
				return false;
		}
		else
		{
			//check if the column is valid
			if (isset($columnsData[$columns]))
				$queryColumns = "`" . $columnsData[$columns] . "` AS " . $columns;
			else
				return false;
		}
		
		$res = $this->DB->prepare("SELECT ". $queryColumns . " FROM `characters` WHERE ".($guid === false ? "`name` = :name" : "`guid` = :guid")." LIMIT 1;");
		if ($guid !== false)
		{
			$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		}
		else
		{
			$res->bindParam(':name', $name, PDO::PARAM_STR);
		}
		$res->execute();
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
		
    	if (!$row)
		{
      	  	return false;
		}
		
		//free memory
		unset($queryColumns);
		unset($columnsData);
		  
        return $row;
    }
    
    public function getCharacterGuild($guid)
    {
        $res = $this->DB->prepare(" SELECT `guild`.* 
                                    FROM `guild_member` 
                                    LEFT JOIN `guild` ON `guild_member`.`guildid` = `guild`.`guildid` 
                                    WHERE `guild_member`.`guid` = :guid 
                                    LIMIT 1;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->execute();
    
        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getStats($guid)
    {
        $res = $this->DB->prepare("SELECT * FROM `character_stats` WHERE `guid` = :guid LIMIT 1;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->execute();
    
        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems($guid)
    {
        $res = $this->DB->prepare(" SELECT `character_inventory`.`slot`, `character_inventory`.`item`, `item_instance`.`itemEntry`, `item_instance`.`enchantments`  
                                    FROM `character_inventory`, `item_instance` 
                                    WHERE `character_inventory`.`item` = `item_instance`.`guid` AND `character_inventory`.`slot` >= 0 AND `character_inventory`.`slot` <= 18 AND `character_inventory`.`guid` = :guid AND `character_inventory`.`bag` = 0;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTalentSpecsInfo($guid)
    {
        $res = $this->DB->prepare("SELECT `talentGroupsCount`, `activeTalentGroup` FROM `characters` WHERE `guid` = :guid LIMIT 1;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getTalents($guid, $specId)
    {
        $res = $this->DB->prepare("SELECT `spell` FROM `character_talent` WHERE `guid` = :guid AND `talentGroup` = :spec ORDER BY `spell` DESC;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->bindParam(':spec', $specId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGlyphs($guid, $specId)
    {
        $res = $this->DB->prepare("SELECT * FROM `character_glyphs` WHERE `guid` = :guid AND `talentGroup` = :spec LIMIT 1;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->bindParam(':spec', $specId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getProfessions($guid)
    {
        //Define the professions (skill ids) we need
        $professionsString = "164,165,171,182,186,197,202,333,393,755,773,129,185,356,794";

        $res = $this->DB->prepare("SELECT `skill`, `value`, `max` FROM `character_skills` WHERE `guid` = :guid AND `skill` IN(".$professionsString.");");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentAchievements($guid, $limit = 5)
    {
        $res = $this->DB->prepare("SELECT `achievement`, `date` FROM `character_achievement` WHERE `guid` = :guid ORDER BY `date` DESC LIMIT :limit;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->bindParam(':limit', $limit, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArenaTeam($guid, $type)
    {
        $res = $this->DB->prepare(" SELECT 
                                        `arena_team_member`.`arenaTeamId` AS `arenateamid`, 
                                        `arena_team`.`name` AS `teamName`, 
                                        `arena_team`.`rating` AS `teamRating`, 
                                        `arena_team`.`rank` AS `teamRank` 
                                    FROM `arena_team_member`, `arena_team` 
                                    WHERE `arena_team_member`.`guid` = :guid AND `arena_team`.`arenaTeamId` = `arena_team_member`.`arenaTeamId` AND `arena_team`.`type` = :type 
                                    LIMIT 1;");
        $res->bindParam(':guid', $guid, PDO::PARAM_INT);
        $res->bindParam(':type', $type, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        return $res->fetch(PDO::FETCH_ASSOC);
    }

    public function getArenaTeamMembers($teamId)
	{
		$res = $this->DB->prepare("	SELECT 
										`arena_team_member`.`guid`, 
										`arena_team_member`.`personalRating` AS rating,
										`arena_team_member`.`seasonGames` AS games,
										`arena_team_member`.`seasonWins` AS wins,
										`characters`.`name`,
										`characters`.`class`,
										`characters`.`race`,
										`characters`.`level`
									FROM `arena_team_member` 
									RIGHT JOIN `characters` ON `characters`.`guid` = `arena_team_member`.`guid` 
									WHERE `arena_team_member`.`arenateamid` = :teamId ORDER BY guid ASC;");
        $res->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            return false;
        }
		
        return $res->fetchAll(PDO::FETCH_ASSOC);
	}

	public function isCharacterOnline($guid)
    {		
		$res = $this->DB->prepare("SELECT `guid`, `online` FROM `characters` WHERE `guid` = :guid LIMIT 1");
		$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$res->execute();
        
        if ($res->rowCount() == 0)
		{
	  		return false;
        }

		$row = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);

    	if ($row['online'] == '1')
		{
      	  return true;
		}
		  
        return false;
    }
	
	public function characterHasMoney($guid, $cost)
    { 
		$account = $this->core->user->get('id');
		
		$res = $this->DB->prepare("SELECT `guid`, `account`, `money` FROM `characters` WHERE `guid` = :guid AND `account` = :account LIMIT 1");
		$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$res->bindParam(':account', $account, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() == 0)
		{
	  		return false;
        }
        
        $row = $res->fetch(PDO::FETCH_ASSOC);

		if ($row['money'] < $cost)
		{
	  		return false;
		}
	 
        return true;
    }
	
	public function ResolveGuild($guid)
	{
		//find out if the char is a guild member
		$res = $this->DB->prepare("SELECT `guildid`, `guid` FROM `guild_member` WHERE `guid` = :guid LIMIT 1;");
		$res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			//we are a member of a guild
			$row = $res->fetch();
			unset($res);
			
			//resolve the guild name
			$res2 = $this->DB->prepare("SELECT `name` FROM `guild` WHERE `guildid` = :guild LIMIT 1;");
			$res2->bindParam(':guild', $row['guildid'], PDO::PARAM_INT);
			$res2->execute();
			
			//check if we have found it
			if ($res2->rowCount() > 0)
			{
				//fetch
				$row2 = $res2->fetch();
				unset($res2);
				
				//return both the name and guildid
				return array('guildid' => $row['guildid'], 'name' => $row2['name']);
			}
			else
			{
				return false;
			}
		}
		else
		{
			//we are not member of any guild
			return false;
		}
		unset($res);
		
		return false;
	}
	
	public function Teleport($guid, $coords)
	{
		//if the coords are passed in array
		if (is_array($coords))
		{
			$position_x = $coords['position_x'];
			$position_y = $coords['position_y'];
			$position_z = $coords['position_z'];
			$map = $coords['map'];
		}
		else
		{
			//else passed as string
			list($position_x, $position_y, $position_z, $map) = explode(',', $coords);
		}
				
		$update_res = $this->DB->prepare("UPDATE `characters` SET position_x = :x, position_y = :y, position_z = :z, map = :map WHERE `guid` = :guid LIMIT 1;");
		$update_res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$update_res->bindParam(':x', $position_x, PDO::PARAM_STR);
		$update_res->bindParam(':y', $position_y, PDO::PARAM_STR);
		$update_res->bindParam(':z', $position_z, PDO::PARAM_STR);
		$update_res->bindParam(':map', $map, PDO::PARAM_INT);
		$update_res->execute();
		
		//assume successful update
		$return = true;
		
		//check if the characters as actually updated
		if ($update_res->rowCount() == 0)
		{
			$return = false;
		}
		unset($update_res);
		
      	return $return; 
	}
	
	//// Use by name prefered, pass guid as false
	public function Unstuck($guid = false, $name = false)
	{
		if ($guid !== false)
		{
			//get the player name
			$res = $this->DB->prepare("SELECT `name` FROM `characters` WHERE `guid` = :guid LIMIT 1;");
			$res->bindParam(':guid', $guid, PDO::PARAM_INT);
			$res->execute();
        
            if ($res->rowCount() == 0)
            {
                return false;
            }

			$row = $res->fetch(PDO::FETCH_ASSOC);
            $name = $row['name'];
            unset($res, $row);
        }
        
        $realm = $this->core->realms->getRealm($this->realmId);
        $commands = $realm->getCommands();

		//try reviving the character aswell
        $commands->ExecuteCommand(".revive ".$name);
         
 		/* Old Style
		$revive_res = $this->DB->prepare("DELETE FROM `character_aura` WHERE `guid` = :guid AND `spell` = :spell");
		$revive_res->bindParam(':guid', $guid, PDO::PARAM_INT);
		$revive_res->bindParam(':spell', $this->deathDebuffId, PDO::PARAM_INT);
		$revive_res->execute();
		unset($revive_res);
		*/	
		
		//unstuck using the soap teleport command
		$soap = $commands->ExecuteCommand(".tele name ".$name." \$home");

	    return (isset($soap['sent']) && $soap['sent'] !== false);
	}
    
    /**
	 * Get the name of a table
	 * @param String $name
	 * @return String
	 */
	public function getTable($name)
	{
		if (array_key_exists($name, $this->tables))
		{
			return $this->tables[$name];
		}
	}

	/**
	 * Get the name of a column
	 * @param String $table
	 * @param String $name
	 * @return String
	 */
	public function getColumn($table, $name)
	{
		if (array_key_exists($table, $this->columns) && array_key_exists($name, $this->columns[$table]))
		{
			return $this->columns[$table][$name];
		}
	}

	/**
	 * Get a set of all columns
	 * @param String $name
	 * @return Array
	 */
	public function getAllColumns($table)
	{
		if (array_key_exists($table, $this->columns))
		{
			return $this->columns[$table];
		}
    }
    
	public function __destruct()
	{
		unset($this->realmId);
		$this->DB = NULL;
		unset($this->DB);		
	}
}