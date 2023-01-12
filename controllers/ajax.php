<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Ajax extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function serverStatus()
    {
        $realm = (int)$_GET['id'];
        $timeout = 0.5;
        $status = null;

        if (!$this->realms->realmExists($realm))
        {
            echo 'Unknown realm!';
            exit;
        }

        $realmConfig = $this->getRealmConfig($realm);

        if (($status = $this->cache->get('realm_status_' . $realm)) === false)
        {
            $sock = @fsockopen($realmConfig['address'], $realmConfig['port'], $errno, $errstr, $timeout);
            if ($sock)
            {
                $status = '1';
            } 
            else
            {
                $status = '0';
            }
            @fclose($sock);
            unset($sock);
            
            //Cache server status for 30 seconds
            $this->cache->store('realm_status_' . $realm, $status, "30");
        }

        echo $status;
        exit;
    }

    public function logonStatus()
    {
        $LogonServer = $this->configItem('LogonServer', 'logon');
        $timeout = 0.5;
        $status = null;

        if (($status = $this->cache->get('logon_status')) === false)
        {
            $status = '0';
            
            $sock = @fsockopen($LogonServer['host'], $LogonServer['port'], $errno, $errstr, $timeout);
            if ($sock)
            {
                $status = '1';
            }
            @fclose($sock);
            unset($sock);

            //Cache server status for 30 seconds
            $this->cache->store('logon_status', $status, "30");
        }

        echo $status;
        exit;
    }

    public function verifyAmount()
    {
        $silver = ((isset($_GET['silver'])) ? (int)$_GET['silver'] : 0);
        $gold = ((isset($_GET['gold'])) ? (int)$_GET['gold'] : 0);
        $realm = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : false);
        $realmConfig = $this->getRealmConfig($realm);

        //check if the curuser is online
        if (!$this->user->isOnline())
        {
            echo 'You must be logged in.';
            exit;
        }

        //check if the realm value was passed
        if (!$realm || !$realmConfig)
        {
            echo 'Website error: Cannot determine if the realm is online.';
            exit;
        }
        else
        {
            $sock = @fsockopen($realmConfig['address'], $realmConfig['port'], $ERROR_NO, $ERROR_STR, 0.5);
            if($sock)
            {
                @fclose($sock);
            } 
            else
            {
                echo 'The realm is currently unavailable. Please try again in few minutes.';
                exit;
            }
        }

        //now check the amounts
        if ($silver > 0 and $gold > 0)
        {
            if ($this->user->get('silver') >= $silver and $this->user->get('gold') >= $gold)
            {
                echo 'OK';
            }
            else
            {
                $text = 'Not enough money.';
                
                //check if the silver is short
                if ($this->user->get('silver') < $silver)
                {
                    $silverNeeded = $silver - $this->user->get('silver');
                }

                //check the gold
                if ($this->user->get('gold') < $gold)
                {
                    $goldNeeded = $gold - $this->user->get('gold');
                }

                //assamble the message
                if (isset($silverNeeded) and isset($goldNeeded))
                {
                    $text .= ' You are '. $silverNeeded .' silver and '. $goldNeeded .' gold short.';
                }
                else
                {
                    if (isset($silverNeeded))
                    {
                        $text .= ' You are '. $silverNeeded .' silver short.';
                    }
                    else
                    {
                        $text .= ' You are '. $goldNeeded .' gold short.';
                    }
                }
                
                //print
                echo $text;
            }
        }
        else if ($silver == 0 and $gold > 0)
        {
            //check the gold
            if ($this->user->get('gold') >= $gold)
            {
                echo 'OK';
            }
            else
            {
                $text = 'Not enough money.';
                $text .= ' You are '. ($gold - $this->user->get('gold')) .' gold short.';

                //print
                echo $text;
            }
        }
        else if ($silver > 0 and $gold == 0)
        {
            //check the gold
            if ($this->user->get('silver') >= $silver)
            {
                echo 'OK';
            }
            else
            {
                $text = 'Not enough money.';
                $text .= ' You are '. ($silver - $this->user->get('silver')) .' silver short.';

                //print
                echo $text;
            }
        }
        else
        {
            echo 'Error: The script has noting to do...';
        }
        exit;
    }

    public function acceptTerms()
    {
        $_SESSION['TermsAccepted'] = true;

        echo $_SESSION['TermsReturn'];
        exit;
    }

    public function getItem()
    {
        $entry = ((isset($_GET['entry'])) ? (int)$_GET['entry'] : false);
        $realmId = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : $this->user->GetRealmId());

        if (!$this->realms->realmExists($realmId))
        {
            $this->JsonError('Invalid realm!');
        }

        $realm = $this->realms->getRealm($realmId);
        $itemInfo = $realm->getIteminfo()->getInfo($entry);

        if ($itemInfo === false)
        {
            $this->JsonError('Failed to resolve item info!');
        }

        $itemInfo['class_str'] = Item_FindClass($itemInfo['class']);
        $itemInfo['subclass_str'] = Item_FindSubclass($itemInfo['class'], $itemInfo['subclass']);
        $itemInfo['quality_str'] = $this->getItemQualityString($itemInfo['quality']);
        $itemInfo['icon'] = $realm->getIteminfo()->getIcon($entry);

        $this->Json($itemInfo);
    }

    public function getMapInfo()
    {
        $key = ((isset($_GET['key'])) ? $_GET['key'] : false);

        //setup the maps data class
        $MD = new MapsData();

        //get the map data
        $data = $MD->get($key);

        //free memory
        unset($MD);

        header ("content-type: text/xml");

        //print the doc type
        echo '<?xml version="1.0" encoding="UTF-8"?>';

        //check if that key is valid
        if (!$data)
        {
            echo '<error>The map key is invalid.</error>';
        }
        else
        {
            echo '
            <info>
                <name>', $data['name'], '</name>
                <minLevel>', $data['minLevel'], '</minLevel>
                <maxLevel>', $data['maxLevel'], '</maxLevel>
                <type>', $data['type'], '</type>
                <zone>', $data['mapId'], '</zone>
                <points count="', count($data['points']), '">';
                    
                    //check if we got some points
                    if (count($data['points']) > 0)
                    {
                        foreach ($data['points'] as $point)
                        {
                            echo '<point styleTop="', $point['top'], '" styleLeft="', $point['left'], '" pointId="', $point['pointId'], '"></point>';
                        }
                    }
                
                echo '
                </points>
            </info>';
        }
        exit;
    }

    public function verifyPoint()
    {
        //Get the user selected realm
        $RealmId = $this->user->GetRealmId();

        $pointId = ((isset($_GET['point'])) ? (int)$_GET['point'] : false);
        $charName = ((isset($_GET['character'])) ? $_GET['character'] : false);

        header ("content-type: text/xml");
        
        //print the doc type
        echo '<?xml version="1.0" encoding="UTF-8"?>';

        //check
        if (!$pointId)
        {
            echo '<error>Invalid point id.</error>';
        }
        if (!$charName)
        {
            echo '<error>Invalid character.</error>';
        }

        //setup the maps data class
        $MD = new MapsData();

        //find the map key by pointId
        $mapKey = $MD->ResolveMapByPoint($pointId);
        
        //get the map data
        $mapData = $MD->get($mapKey);

        //free memory
        unset($MD);

        //get the character level
        $level = $this->realms->getRealm($RealmId)->getCharacters()->getCharacterData(false, $charName, 'level');

        //return the collected data
        echo '
        <info>
            <reqLevel>', $mapData['reqLevel'], '</reqLevel>
            <charLevel>', $level['level'], '</charLevel>
        </info>';
        exit;
    }

    public function boostDurationPrice()
    {
        $this->loadConfig('boosts');

        $DurationId = ((isset($_GET['id'])) ? (int)$_GET['id'] : false);

        if (!$DurationId)
        {
            $this->JsonError("The duration id is missing.");
            die;
        }
        
        $pricing = $this->configItem('Pricing', 'boosts');

        $this->Json($pricing[$DurationId]);
    }

    public function pollVote()
    {
        $pollId = isset($_POST['pollId']) ? (int)$_POST['pollId'] : false;
        $answer = isset($_POST['answer']) ? (int)$_POST['answer'] : false;

        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in to vote.');
        }

        if (!$pollId)
        {
            $this->JsonError('Missing poll id!');
        }

        if (!$answer)
        {
            $this->JsonError('Missing answer!');
        }

        // Validate the poll id
        $res = $this->db->prepare("SELECT * FROM `polls` WHERE `id` = ? ORDER BY `id` DESC LIMIT 1;");
        $res->execute(array($pollId));

        if ($res->rowCount() < 1)
        {
            $this->JsonError('Invalid poll id!');
        }

        $poll = $res->fetch();

        // Check if the poll is disabled
        if ((int)$poll['disabled'] == 1)
        {
            $this->JsonError('The poll is disabled!');
        }

        $userId = $this->user->get('id');

        // Insert the poll answer
        $insert = $this->db->prepare("INSERT INTO `polls_votes` (`poll_id`, `answer_id`, `user_id`) VALUES (:pollid, :answer, :userid);");
        $insert->bindParam(':pollid', $pollId, PDO::PARAM_INT);
        $insert->bindParam(':answer', $answer, PDO::PARAM_INT);
        $insert->bindParam(':userid', $userId, PDO::PARAM_INT);
        $insert->execute();

        if ($insert->rowCount() > 0)
        {
            $this->loadLibrary('polls');

            $this->Json(array('success' => true, 'answers' => PollsLib::GetAnswers($pollId)));
        }

        $this->JsonError('Failed to apply your vote!');
    }

    public function forum_moveinfo()
    {
        $res = $this->db->prepare("SELECT * FROM `wcf_categories` ORDER BY `position` ASC;");
        $res->execute();

        if ($res->rowCount() > 0)
        {
            $categories = $res->fetchAll();

            foreach ($categories as $i => $category)
            {
                $res2 = $this->db->prepare("SELECT * FROM `wcf_forums` WHERE `category` = :id ORDER BY `position` ASC;");
                $res2->bindParam(':id', $category['id'], PDO::PARAM_INT);
                $res2->execute();

                $categories[$i]['forums'] = false;

                if ($res2->rowCount() > 0)
                {
                    $categories[$i]['forums'] = array();

                    while ($forum = $res2->fetch())
                    {
                        // Check the view roles
                        $view_roles = explode(',', $forum['view_roles']);
                        
                        if (!in_array(0, $view_roles) && !$this->user->hasAnyOfRoles($view_roles))
                            continue;
                        
                        if (strlen($forum['name']) == 0 && (int)$category['flags'] & WCF_FLAGS_CLASSES_LAYOUT)
                            $forum['name'] = $this->realms->getClassString($forum['class']);

                        $categories[$i]['forums'][] = $forum;
                    }
                }
            }

            $this->Json($categories);
        }

        $this->JsonError('There are no categories in the database.');
    }
}