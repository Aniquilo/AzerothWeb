<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Purchase_gold extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('premium_services');
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Purchase In-Game Gold');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCss('template/style/page-purchase-gold.css');
        $this->tpl->LoadHeader();

        $data = array(
            'maxAmount' => (int)$this->configItem('IGG_MaxAmount', 'premium_services'),
            'price' => (int)$this->configItem('IGG_PricePerThousand', 'premium_services')
        );

        //Print the page view
        $this->tpl->LoadView('premium_services/purchase_gold', $data);

        $this->tpl->AddFooterJs('template/js/page.purchase.gold.js');
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('accounts.finances');

        //prepare the log
        $logs = new purchaseLog();

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare multi errors
        $this->errors->NewInstance('pStore_gold');

        //bind the onsuccess message
        $this->errors->onSuccess('Your purchase has been successfully delivered.', '/purchase_gold');

        $character = (isset($_POST['character']) ? $_POST['character'] : false);
        $GoldAmount = (isset($_POST['amount']) ? (int)$_POST['amount'] : false);

        //assume the realm is 1 (for now)
        $RealmId = $this->user->GetRealmId();

        if (!$character)
        {
            $this->errors->Add("Please select a character first.");
        }
        if (!$GoldAmount)
        {
            $this->errors->Add("Please enter the amount of gold you would like to purchase.");
        }
        else
        {
            //Verify the gold amount
            if ($GoldAmount < 1000)
            {
                $GoldAmount = 1000;
            }
            if ($GoldAmount > (int)$this->configItem('IGG_MaxAmount', 'premium_services'))
            {
                $this->errors->Add("The maximum that you can purchase is 100,000 gold.");
            }
        }

        $this->errors->Check('/purchase_gold');

        //Calculate the cost
        //get the left overs
        $leftOver = $GoldAmount % 1000;

        //any left over costs +1 gold coin
        if ($leftOver > 0)
        {
            $GoldAmount -= $leftOver;
            $GoldAmount += 1000;
        }

        //calculate the price
        $price = $GoldAmount * ((int)$this->configItem('IGG_PricePerThousand', 'premium_services') / 1000);

        ######################################
        ######### CHECK FINANCES #############
        $finance->SetCurrency(CURRENCY_GOLD);
        $finance->SetAmount((int)$price);

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

        $this->errors->Check('/purchase_gold');

        //start logging
        $logs->add('PSTORE_GOLD', 'In-game gold service. Using currency: Gold Coins, Amount of Purchase: '.$GoldAmount.', Price: '.$price.', Character: '.$character.', Selected realm: '.$RealmId.'.', array(
            'gold' => $GoldAmount,
            'selected_currency' => CURRENCY_GOLD,
            'price' => $price,
            'character' => $character,
            'realm' => $RealmId
        ));

        //send the gold
        $sentGold = $command->sendMoney($character, ($GoldAmount * 10000), 'In-Game Gold Delivery');

        //check if any of the actions have failed and log it
        if ($sentGold !== true)
        {
            $logs->update('The website failed to execute the send money command and returned: '.$sentGold.'.', 'error');
            $this->errors->Add("The website failed to deliver your purchase. Please contact the administration.");
        }
        else //check if one of those actions was successful
        {
            //update the log
            $logs->update('The in-game gold command has been executed successfully.');

            //charge for the purchase
            $Charge = $finance->Charge("In-Game Gold", CA_SOURCE_TYPE_NONE);
            
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
            
            //free up some memory
            unset($finance);

            //redirect				
            $this->errors->triggerSuccess();
            exit;
        }
        
        $this->errors->Check('/purchase_gold');
        exit;
    }
}