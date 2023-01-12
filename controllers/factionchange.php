<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Factionchange extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('premium_services');
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Character Faction Change');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        $data = array(
            'price' => (int)$this->configItem('FactionChange_Price', 'premium_services')
        );

        //Print the page view
        $this->tpl->LoadView('premium_services/factionchange', $data);

        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('accounts.finances');

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare the log
        $logs = new purchaseLog();

        //prepare multi errors
        $this->errors->NewInstance('pStore_faction');

        //bind the onsuccess message
        $this->errors->onSuccess('Successfull character faction change.', '/factionchange');

        $character = (isset($_POST['character']) ? $_POST['character'] : false);

        $RealmId = $this->user->GetRealmId();

        //define how much a faction change is going to cost
        $factionChangePrice = (int)$this->configItem('FactionChange_Price', 'premium_services');

        if (!$character)
        {
            $this->errors->Add("Please select a character first.");
        }

        //Set the currency and price
        $finance->SetCurrency(CURRENCY_GOLD);
        $finance->SetAmount($factionChangePrice);

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

        $this->errors->Check('/factionchange');

        //start logging
        $logs->add('PSTORE_FACTION', 'Faction change service. Using currency: Gold Coins, Price: '.$factionChangePrice.', Character: '.$character.', Selected realm: '.$RealmId.'.', array(
            'selected_currency' => CURRENCY_GOLD,
            'price' => $factionChangePrice,
            'character' => $character,
            'realm' => $RealmId
        ));

        //level the character
        $FactionChange = $command->FactionChange($character);
        
        //check if the command was successfull
        if ($FactionChange === true)
        {
            //update the log
            $logs->update('The faction change command has been executed successfully.');

            //charge for the purchase
            $Charge = $finance->Charge("Faction Change", CA_SOURCE_TYPE_NONE);
            
            if ($Charge === true)
            {
                //update the log
                $logs->update('The user has been successfully charged for his purchase.', 'ok');
            }
            else
            {
                //update the log
                $logs->update('The user was not charged for his purchase, website failed to update.', 'error');
            }
            unset($Charge);
            
            //free up some memory
            unset($finance);

            //redirect				
            $this->errors->triggerSuccess();
            exit;
        }
        else
        {
            $this->errors->Add("The website failed to complete your order. Please contact the administration.");
            //update the log
            $logs->update('Soap failed to execute the faction change command. Error: ' . $FactionChange, 'error');
        }
        
        $this->errors->Check('/factionchange');
        exit;
    }
}