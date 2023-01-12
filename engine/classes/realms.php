<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Realms
{
    private $core;
    private $realmsConfig;
    private $realms;

    public function __construct()
    {
        $this->core =& get_instance();
        $this->realms = array();
        
        $this->realmsConfig = $this->core->loadConfig('realms');

        // Init realms
        foreach ($this->realmsConfig as $id => $realmConfig)
        {
            $this->realms[(int)$id] = new Realm((int)$id, $realmConfig);
        }
    }

    public function getFirstRealm()
	{
		if ($this->realms)
		{
			foreach ($this->realms as $id => $realm)
			{
				return $realm;
			}
		}
		
		return null;
	}

    public function getRealms()
    {
        return $this->realms;
    }

    public function getRealm($id)
    {
        return (isset($this->realms[(int)$id]) ? $this->realms[(int)$id] : false);
    }

    public function realmExists($id)
    {
        return (isset($this->realms[(int)$id]));
    }

    public function getRaceString($id)
	{
		$races = $this->core->configItem('wow_races');
		
		return (isset($races[(int)$id]) ? $races[(int)$id] : 'Unknown Race');
	}
    
    public function getAllianceRaces()
    {
        return $this->core->configItem('alliance_races');
    }

    public function getHordeRaces()
    {
        return $this->core->configItem('horde_races');
    }

	public function getClassString($id)
	{
		$classes = $this->core->configItem('wow_classes');
		
		return (isset($classes[(int)$id]) ? $classes[(int)$id] : 'Unknown Class');
	}
	
	public function ResolveFaction($race)
	{
        $allianceRaces = $this->core->configItem('alliance_races');
        $hordeRaces = $this->core->configItem('horde_races');

        if (in_array((int)$race, $allianceRaces))
        {
            return FACTION_ALLIANCE;
        }
        else if (in_array((int)$race, $hordeRaces))
        {
            return FACTION_HORDE;
        }

		return false;
    }
    
	public function getCharacterAvatar($character)
	{
        $race = $this->getRaceString($character['race']);
        $class = $this->getClassString($character['class']);
		$gender = ((int)$character['gender']) ? "f" : "m";

		if ($character['class'] == 6)
		{
			$level = 70;
			$class = "Deathknight";
		}
		else
		{
			// If character is below 30, use lv 1 image
			if ($character['level'] < 30)
			{
				$level = 1;
			}
			// If character is below 65, use lv 60 image
			elseif ($character['level'] < 65)
			{
				$level = 60;
			}
			// 65+, use lvl70 image
			else
			{
				$level = 70;
			}
		}

		if (in_array($race, array("Blood elf", "Night elf")))
		{
			$race = preg_replace("/ /", "", $race);
		}

		$file = $class."-".strtolower($race)."-".$gender."-".$level;

		if (!file_exists(ROOTPATH . "/resources/armory/avatars/".$file.".gif"))
		{
			return 'default';
		}
        
        return $file;
	}
}