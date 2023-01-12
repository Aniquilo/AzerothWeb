<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class trinity_Realmstats implements emulator_Realmstats
{
    private $core;
    private $realmId;
    private $realm;
	private $uptimeConfig;
	private $uptimeRow;
	
	//constructor
	public function __construct($realmId)
	{
        $this->core =& get_instance();
        $this->realmId = $realmId;
        $this->realm = $this->core->realms->getRealm($realmId);
        $this->uptimeConfig = $this->realm->getConfig('UPDATE_TIME');
        $this->prepareUptimeRow();
	}
	
	private function prepareUptimeRow()
	{
		$res = $this->core->auth_db->prepare("SELECT `starttime`, `uptime` FROM `uptime` WHERE `realmid` = :id ORDER BY `starttime` DESC LIMIT 1;");
		$res->bindParam(':id', $this->realmId, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$this->uptimeRow = $res->fetch();
		}
		else
		{
			$this->uptimeRow = false;
		}
		unset($res);
		
		return true;
	}
	
    public function getStatus()
	{
	  	if (!$this->uptimeRow)
		{	   		
	    	return 'offline';
		}
	   	else
		{
			//check if it's set in the config
			if ($this->uptimeConfig)
			{
				$confVal = $this->uptimeConfig;
				
				//find out how did we pass the value
				if (is_numeric($confVal))
				{
					//the value is int
					$updateTime = (int)$confVal;
				}
				else if (ctype_digit($confVal))
				{
					//the value consists of all digits
					$updateTime = (int)$confVal;
				}
				else
				{
					//convert string to time
					$updateTime = strtotime($confVal, 0);
				}
			}
			else
			{
				//default 10 minutes in seconds
				$updateTime = 600;
			}
			
			//get the time which should be equal or greater than now if the server is online
	 		$time = $this->uptimeRow['starttime'] + $this->uptimeRow['uptime'] + $updateTime;

	   		if ($time < time())
			{ 
	     		return 'offline';
			}
	    	else
			{
				return 'online';
	   		}
	  	}
		
		return false;
    }
    	
    public function getUptime()
    {	 
	 	$num = $this->uptimeRow['uptime'];
	 
      	$day = floor($num/86400);
      	$hours = floor(($num - $day*86400)/3600);
      	$minutes = floor(($num - $day*86400 - $hours*3600)/60);
	   
	  	if ($day <= 0 and $hours <= 0)
		{
       		$return = $minutes . ($minutes > 1 ? ' minutes' : ' minute');
		}
	  	else if ($day <= 0)
		{
       		$return = $hours . ($hours > 1 ? ' hours' : ' hour') . ' and ' . $minutes . ($minutes > 1 ? ' minutes' : ' minute');
		}
	  	else
		{
       		$return = $day . ($day > 1 ? ' days ' : ' day ') . $hours . ($hours > 1 ? ' hours' : ' hour') . ' and ' . $minutes.' min';
		}

     	return $return;
    }

	public function getOnline()
	{
        $db = $this->core->realms->getRealm($this->realmId)->getCharactersConnection();

		//count the Alliance
		$res = $db->prepare("SELECT COUNT(`guid`) AS a FROM `characters` WHERE `online` = '1' AND `race` IN (".implode(',', $this->core->realms->getAllianceRaces()).");");
		$res->execute();
		$allyRes = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);
	
		//Count the Horde
		$res = $db->prepare("SELECT COUNT(`guid`) AS h FROM `characters` WHERE `online` = '1' AND `race` IN (".implode(',', $this->core->realms->getHordeRaces()).");");
		$res->execute();
		$hordeRes = $res->fetch(PDO::FETCH_ASSOC);
		unset($res);

		//get the count
		$allyCount = $allyRes['a'];
		$hordeCount = $hordeRes['h'];
		$totalCount = $allyCount + $hordeCount;
		
		return array('total' => $totalCount, 'alliance' => $allyCount, 'horde' => $hordeCount);
	}
	
	public function GetRealmDetails()
	{
		$cached = $this->core->cache->get('realm_stats_'.$this->realmId);
        
		if ($cached !== false)
		{
			return $cached;
        }
        
        return $this->CompileRealmDetails();
    }
    
    private function CompileRealmDetails()
    {
        //Connect
        if ($RealmDB = $this->realm->getCharactersConnection())
        {
            $data = array(
                'total' => 0,
                'alliance' => 0,
                'horde' => 0,
                'races' => array(),
                'classes' => array()
            );

            //count the Alliance
            if ($allyRaces = $this->core->configItem('alliance_races'))
            {
                // Remove pandarens and worgens
                if (false !== $key = array_search(22, $allyRaces)) {
                    unset($allyRaces[$key]);
                }
                if (false !== $key = array_search(24, $allyRaces)) {
                    unset($allyRaces[$key]);
                }
                if (false !== $key = array_search(25, $allyRaces)) {
                    unset($allyRaces[$key]);
                }
                if (false !== $key = array_search(26, $allyRaces)) {
                    unset($allyRaces[$key]);
                }

                $res = $RealmDB->query("SELECT COUNT(*) FROM `characters` WHERE `level` > 9 AND `race` IN (".(implode(', ', $allyRaces)).");");
                $row = $res->fetch(PDO::FETCH_NUM);
                $data['alliance'] = (int)$row[0];
                $data['total'] += $data['alliance'];
                unset($res, $row);
            }
        
            //count the Horde
            if ($hordeRaces = $this->core->configItem('horde_races'))
            {
                // Remove pandarens and worgens
                if (false !== $key = array_search(22, $hordeRaces)) {
                    unset($hordeRaces[$key]);
                }
                if (false !== $key = array_search(24, $hordeRaces)) {
                    unset($hordeRaces[$key]);
                }
                if (false !== $key = array_search(25, $hordeRaces)) {
                    unset($hordeRaces[$key]);
                }
                if (false !== $key = array_search(26, $hordeRaces)) {
                    unset($hordeRaces[$key]);
                }

                $res = $RealmDB->query("SELECT COUNT(*) FROM `characters` WHERE `level` > 9 AND `race` IN (".(implode(', ', $hordeRaces)).");");
                $row = $res->fetch(PDO::FETCH_NUM);
                $data['horde'] = (int)$row[0];
                $data['total'] += $data['horde'];
                unset($res, $row);
            }
            
            //Count races
            if ($this->core->configItem('wow_races'))
            {
                foreach ($this->core->configItem('wow_races') as $race => $name)
                {
                    // Skip pandarens
                    if (in_array($race, array(22, 24, 25, 26)))
                        continue;
                    
                    $res = $RealmDB->query("SELECT COUNT(*) FROM `characters` WHERE `level` > 9 AND `race` = '".$race."';");
                    $row = $res->fetch(PDO::FETCH_NUM);
                    $data['races'][$race] = (int)$row[0];
                    unset($res, $row);
                }
            }

            //Count classes
            if ($this->core->configItem('wow_classes'))
            {
                foreach ($this->core->configItem('wow_classes') as $class => $name)
                {
                    // Skip monk and demon hunter
                    if (in_array($class, array(10, 12)))
                        continue;

                    $res = $RealmDB->query("SELECT COUNT(*) FROM `characters` WHERE `level` > 9 AND `class` = '".$class."';");
                    $row = $res->fetch(PDO::FETCH_NUM);
                    $data['classes'][$class] = (int)$row[0];
                    unset($res, $row);
                }
            }

            // Cache it for an year
            $this->core->cache->store('realm_stats_'.$this->realmId, $data, strtotime('1 day', 0));

            return $data;
        }

        return false;
    }
	
	public function __destruct()
	{
        unset($this->realmId);
        unset($this->realm);
        unset($this->uptimeConfig);
        unset($this->uptimeRow);	
	}
}