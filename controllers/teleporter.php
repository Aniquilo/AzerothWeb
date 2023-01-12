<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Teleporter extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('premium_services');

        if (!$this->configItem('Teleporter_Enabled', 'premium_services'))
        {
            $this->tpl->Message('Error', 'An error occured!', 'This service has been disabled.');
        }
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Teleporter');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-teleporter.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('teleporter');

        $this->tpl->AddFooterJs('template/js/jquery.easyTooltip.js');
        $this->tpl->AddFooterJs('template/js/page.teleporter.js');
        $this->tpl->AddFooterJs('template/js/northrend.js');
        $this->tpl->AddFooterJs('template/js/kalimdor.js');
        $this->tpl->AddFooterJs('template/js/eastern-kingdoms.js');
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('teleport');

        $RealmId = $this->user->GetRealmId();

        $pointId = (isset($_POST['point']) ? (int)$_POST['point'] : false);
        $charName = (isset($_POST['character']) ? $_POST['character'] : false);

        //bind the onsuccess message
        $this->errors->onSuccess($charName . ' has been successfully teleported.', '/teleporter');

        //get the cooldown
        $cooldown = $this->user->getCooldown('teleport');
        $cooldownTime = '120 minutes';

        if (!$charName)
        {
            $this->errors->Add("Please select a character.");
        }

        if (!$pointId)
        {
            $this->errors->Add("Please select a teleport location.");
        }

        if (!$RealmId)
        {
            $this->errors->Add("There is no realm assigned to your account.");
        }

        //check the cooldown
        if (time() < $cooldown)
        {
            $this->errors->Add('This tool is on a cooldown, please try again later.');
        }

        //check if realm exists
        if (!$this->realms->realmExists($RealmId))
        {
            $this->errors->Add("The selected realm is invalid.");
        }

        //get the realm
        $realm = $this->realms->getRealm($RealmId);

        //prepare commands class
        $command = $realm->getCommands();

        //check if the realm is online
        if ($command->CheckConnection() !== true)
        {
            $this->errors->Add("The realm is currently unavailable. Please try again in few minutes.");
        }

        //check if the characters database is reachable
        if (!$realm->checkCharactersConnection())
        {
            $this->errors->Add("The website failed to load realm database. Please contact the administration for more information.");
        }

        $this->errors->Check('/teleporter');

        //load the character lib
        $this->loadLibrary('purchaseLog');
        
        //prepare the log
        $logs = new purchaseLog();
        
        //setup the maps data class
        $MD = new MapsData();

        //setup the map points data class
        $MP = new MapPoints();
        
        //get some character data
        $charData = $realm->getCharacters()->getCharacterData(false, $charName, array('guid', 'level'));

        //find the map key by pointId
        $mapKey = $MD->ResolveMapByPoint($pointId);

        //get the map data
        $mapData = $MD->get($mapKey);
        
        if (!$realm->getCharacters()->isMyCharacter(false, $charName))
        {
            $this->errors->Add('The selected character does not belong to this account.');
        }
        
        if ($mapData['reqLevel'] > $charData['level'])
        {
            $this->errors->Add('The selected character does not meet the level requirement. The location requires a minimum of atleast ' . $charData['level'] . ' level.');
        }

        $this->errors->Check('/teleporter');

        //The character seems to be valid
        //start logging
        $logs->add('TELEPORTER', 'Starting log session. Teleporting player: '.$charName.', to point: '.$pointId.', selected realm: '.$RealmId.'.', array(
            'teleport_point' => $pointId,
            'character' => $charName,
            'realm' => $RealmId
        ));

        //get the coords	
        if ($coords = $MP->get($pointId))
        {
            $teleport = null;
            
            //if the character is Online use SOAP to teleport using commands
            if ($realm->getCharacters()->isCharacterOnline($charData['guid']))
            {
                //try teleporting using soap
                $teleport = $command->Teleport($charName, $coords['x'], $coords['y'], $coords['z'], $coords['map']);

                //update the log
                $logs->update('The character is online using method SOAP.', 'pending');
            }
            else
            {
                //prepare the coords in suitable format
                $coords2 = array(
                    'position_x'	=> $coords['x'],
                    'position_y' 	=> $coords['y'],
                    'position_z' 	=> $coords['z'],
                    'map'			=> $coords['map'],
                );

                //try teleporting using PHP and SQL
                $teleport = $realm->getCharacters()->Teleport($charData['guid'], $coords2);

                //free memory
                unset($coords2);
                
                //update the log
                $logs->update('The character is offline using method SQL.', 'pending');
            }
            
            //update the cooldown if the character was unstucked
            if ($teleport === true)
            {
                //set cooldown because we got no errors
                $this->user->setCooldown('teleport', strtotime('+'.$cooldownTime));

                //update the log
                $logs->update('The character was teleported successfully.', 'ok');

                //redirect
                $this->errors->triggerSuccess();
            }
            else
            {
                $this->errors->Add('The website failed to teleport your character. Please try again later or contact the administration.');
                //update the log
                $logs->update('Failed to teleport the character. Return: '.$teleport.'.', 'error');
            }
        }
        else
        {
            $this->errors->Add('The website failed to teleport your character. Please try again later or contact the administration.');
            //update the log
            $logs->update('Failed to get coordinates for point id: '.$pointId.'.', 'error');
        }
        unset($coords);
        unset($charData);
        unset($MD);
        unset($MP);
        unset($logs);
        
        $this->errors->Check('/teleporter');
        exit;
    }
}