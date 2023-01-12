<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Recustomization extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('premium_services');
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Character Recustomization');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        $data = array(
            'price' => (int)$this->configItem('Recustomization_Price', 'premium_services')
        );

        //Print the page view
        $this->tpl->LoadView('premium_services/recustomization', $data);

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
        $this->errors->NewInstance('pStore_recustomization');

        //bind the onsuccess message
        $this->errors->onSuccess('Successfull character re-customization.', '/recustomization');

        $character = (isset($_POST['character']) ? $_POST['character'] : false);

        //assume the realm is 1 (for now)
        $RealmId = $this->user->GetRealmId();

        //define how much a faction change is going to cost
        $recustomizationPrice = (int)$this->configItem('Recustomization_Price', 'premium_services');

        if (!$character)
        {
            $this->errors->Add("Please select a character first.");
        }

        //Set the currency and price
        $finance->SetCurrency(CURRENCY_GOLD);
        $finance->SetAmount($recustomizationPrice);

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

        //Check for errors
        $this->errors->Check('/recustomization');

        //start logging
        $logs->add('PSTORE_CUSTOMIZE', 'Recustomization service. Using currency: Gold Coins, Price: '.$recustomizationPrice.', Character: '.$character.', Selected realm: '.$RealmId.'.', array(
            'selected_currency' => CURRENCY_GOLD,
            'price' => $recustomizationPrice,
            'character' => $character,
            'realm' => $RealmId
        ));

        //recustomize the character
        $recustomization = $command->Customize($character);
        
        //check if the command was successfull
        if ($recustomization === true)
        {
            //update the log
            $logs->update('The recustomization command has been executed successfully.');

            //charge for the purchase
            $Charge = $finance->Charge("Character Recustomization", CA_SOURCE_TYPE_NONE);
            
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
            $logs->update('Soap failed to execute the recustomization command. Error: ' . $recustomization, 'error');
        }

        $this->errors->Check('/recustomization');
        exit;
    }
}