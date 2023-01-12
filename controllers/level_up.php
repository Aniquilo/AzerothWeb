<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Level_up extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('premium_services');
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Character Level Up');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        $data = array(
            'levelsConfig' => $this->configItem('LevelUp', 'premium_services')
        );

        //Print the page view
        $this->tpl->LoadView('premium_services/level_up', $data);

        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('accounts.finances');

        //get the levels config
        $levelsConfig = $this->configItem('LevelUp', 'premium_services');

        //prepare the log
        $logs = new purchaseLog();

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare multi errors
        $this->errors->NewInstance('pStore_levels');

        //bind the onsuccess message
        $this->errors->onSuccess('Your purchase has been successfully delivered.', '/level_up');

        $level = (isset($_POST['levels']) ? (int)$_POST['levels'] : false);
        $character = (isset($_POST['character']) ? $_POST['character'] : false);

        //assume the realm is 1 (for now)
        $RealmId = $this->user->GetRealmId();

        if (!$character)
        {
            $this->errors->Add("Please select a character first.");
        }
        if ($level === false)
        {
            $this->errors->Add("Please select your desired level.");
        }
        else if (!isset($levelsConfig[$level]))
        {
            $this->errors->Add("There was a problem with your level selection, if the problem persists please contact the administration.");
        }

        //overright the variable with the actual data
        $levelConfig = $levelsConfig[$level];

        ######### CHECK FINANCES #############
        $finance->SetCurrency(CURRENCY_GOLD);
        $finance->SetAmount((int)$levelConfig['price']);

        //check if the user has enough balance
        if ($BalanceError = $finance->CheckBalance())
        {
            if (is_array($BalanceError))
            {
                //insufficient amount
                foreach ($BalanceError as $currency)
                {
                    $this->errors->Add("You do not have enough " . ucfirst($currency) . " Coins.");
                }
            }
            else
            {
                //technical error
                $this->errors->Add('Error, the website failed to verify your account balance.');
            }
        }
        unset($BalanceError);

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

        //check if the character belongs to this account
        if (!$realm->getCharacters()->isMyCharacter(false, $character))
        {
            $this->errors->Add("The selected character does not belong to your account.");
        }

        $this->errors->Check('/level_up');

        //get the character name
        $charData = $realm->getCharacters()->getCharacterData(false, $character, 'level');
        
        //check if the character is already higher level
        if ((int)$charData['level'] < (int)$levelConfig['level'])
        {
            //start logging
            $logs->add('PSTORE_LEVEL', 'Character level service. Character: '.$character.' to level '.$levelConfig['level'].'. Using currency: Gold Coins, price value: '.$levelConfig['price'].', Selected realm: '.$RealmId.'.', array(
                'level' => $levelConfig['level'],
                'selected_currency' => CURRENCY_GOLD,
                'price' => (int)$levelConfig['price'],
                'character' => $character,
                'realm' => $RealmId
            ));

            //level the character
            $levelUp = $command->levelTo($character, $levelConfig['level']);

            //send the gold
            $sentGold = $command->sendMoney($character, $levelConfig['money'], 'Premium Store Delivery');

            //make the bags string
            $bagsString = "";
            for ($i = 0; $i < $levelConfig['bags']; $i++) { $bagsString .= $levelConfig['bagsId'] . " "; }
            
            //send the bags
            $sentBags = $command->sendItems($character, $bagsString, 'Premium Store Delivery');
            
            //check if one of those actions was successful
            if ($levelUp === true || $sentGold === true || $sentBags === true)
            {
                //check if any of the actions have failed and log it
                if ($levelUp === true)
                {
                    $logs->update('The level up command has been executed successfully.');
                }
                if ($sentGold === true)
                {
                    $logs->update('The send money command has been executed successfully.');
                }
                if ($sentBags === true)
                {
                    $logs->update('The send items command has been executed successfully.');
                }

                //charge for the purchase
                $Charge = $finance->Charge("Level Up", CA_SOURCE_TYPE_NONE);
                
                if ($Charge === true)
                {
                    //update the log
                    $logs->update('The user has been charged for his purchase.', 'ok');
                }
                else
                {
                    //update the log
                    $logs->update('The user was not charged for his purchase, website failed to update.', 'error');
                }
                unset($Charge);
                
                //check if any of the actions have failed and log it
                if ($levelUp !== true)
                {
                    $logs->update('The website failed to execute the level command and returned: '.$levelUp.'.', 'error');
                }
                if ($sentGold !== true)
                {
                    $logs->update('The website failed to execute the send money command and returned: '.$sentGold.'.', 'error');
                }
                if ($sentBags !== true)
                {
                    $logs->update('The website failed to execute the send items command and returned errors: '.implode(', ', $sentBags).'.', 'error');
                }

                //free up some memory
                unset($finance);

                //redirect				
                $this->errors->triggerSuccess();
                exit;
            }
            else
            {
                $this->errors->Add("The website failed to deliver your purchase. Please contact the administration.");
                //update the log
                $logs->update('Soap failed to execute any of the level up commands.', 'error');
            }
        }
        else
        {
            $this->errors->Add("The selected character is already higher level.");
        }

        $this->errors->Check('/level_up');
        exit;
    }
}