<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Search extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $search = isset($_POST['search']) ? $_POST['search'] : false;

        if (!$search || strlen($search) < 3)
        {
            $this->JsonError('Please enter search text.');
        }

        $search = filter_var($search, FILTER_SANITIZE_STRING);

        $characters = array();
        $guilds = array();
        
        // Collect realms info
        $realms = array();
        
        //Get characters, guilds, items for each realm
        foreach ($this->realms->getRealms() as $realm)
        {
            $realms[] = array(
                'id' => $realm->getId(),
                'name' => $realm->getName()
            );
            
            // Make sure characters database is available
            if ($realm->checkCharactersConnection())
            {
                // Find characters
                $found_characters = $this->findCharacter($search, $realm->getId());
                
                if ($found_characters)
                {
                    foreach ($found_characters as $found_character)
                    {
                        array_push($characters, 
                            array(
                                'guid' => $found_character['guid'],
                                'name' => $found_character['name'],
                                'race' => $found_character['race'],
                                'gender' => $found_character['gender'],
                                'class' => $found_character['class'],
                                'level' => $found_character['level'],
                                'className' => $this->realms->getClassString($found_character['class']),
                                'raceName' => $this->realms->getRaceString($found_character['race']),
                                'avatar' => $this->realms->getCharacterAvatar($found_character),
                                'realm' => $realm->getId(),
                                'realmName' => $realm->getName()
                            )
                        );
                    }
                }

                /*// Find guilds
                $found_guilds = $this->findGuild($search, $realm->getId());

                if ($found_guilds)
                {
                    foreach ($found_guilds as $found_guild)
                    {
                        array_push($guilds, 
                            array(
                                'id' => $found_guild['guildid'],
                                'name' => $found_guild['name'],
                                'members' => $found_guild['GuildMemberCount'],
                                'realm' => $realm->getId(),
                                'realmName' => $realm->getName(),
                                'ownerGuid' => $found_guild['leaderguid'],
                                'ownerName' => $found_guild['leaderName']
                            )
                        );
                    }
                }*/
            }
        }

        $result = array(
            'html' => $this->tpl->LoadView('armory/results', array(
                'realms' => $realms,
                'characters' => $characters,
                'guilds' => $guilds
            ), false)
        );

        $this->Json($result);
    }

    public function findCharacter($searchString = "", $realmId = 1)
	{
        $table = $this->realms->getRealm($realmId)->getCharacters()->getTable('characters');
        $columns = $this->realms->getRealm($realmId)->getCharacters()->getAllColumns('characters');

		//Connect to the character database		
		$db = $this->realms->getRealm($realmId)->getCharactersConnection();
		
		//Get the connection and run a query
		$res = $db->prepare("SELECT ".prepare_columns($columns)." FROM `".$table."` WHERE UPPER(`".$columns['name']."`) LIKE CONCAT('%', UPPER(:search), '%') ORDER BY `".$columns['level']."` DESC LIMIT 100;");
        $res->bindParam(':search', $searchString, PDO::PARAM_STR);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetchAll();
        }
        
		return false;
    }
    
    public function findGuild($searchString = "", $realmId = 1)
	{
		$table = $this->realms->getRealm($realmId)->getCharacters()->getTable('guild');
        $columns = $this->realms->getRealm($realmId)->getCharacters()->getAllColumns('guild');

		//Connect to the character database		
		$db = $this->realms->getRealm($realmId)->getCharactersConnection();
		
		//Get the connection and run a query
		$res = $db->prepare("SELECT ".prepare_columns($columns)." FROM `".$table."` WHERE UPPER(`".$columns['name']."`) LIKE CONCAT('%', UPPER(:search), '%') ORDER BY `".$columns['guildid']."` DESC LIMIT 100;");
        $res->bindParam(':search', $searchString, PDO::PARAM_STR);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetchAll();
        }
        
		return false;
	}
}