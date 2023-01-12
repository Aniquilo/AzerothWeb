<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Boosts extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();
        
        $this->loadConfig('boosts');

        if (!$this->configItem('Enabled', 'boosts'))
        {
            $this->tpl->Message('Error', 'An error occured!', 'This service has been disabled.');
        }
    }
    
    private function tableExists($RealmDb)
    {
        //Validate the article record
        $res = $RealmDb->query("SHOW TABLES LIKE 'player_boosts';");

        if ($res->rowCount() > 0)
        {
            return true;
        }

        return false;
    }

    public function index()
    {
        $pricing = $this->configItem('Pricing', 'boosts');

        //Predefine the realm id
        $RealmId = $this->user->GetRealmId();
        $Boosts = new BoostsData();

        $ActiveBoosts = array();
				
        //Find the active boosts for this account/realm
        if ($RealmDb = $this->realms->getRealm($RealmId)->getCharactersConnection())
        {
            if (!$this->tableExists($RealmDb))
            {
                $this->tpl->Message('Error!', 'An error has occured!', 'The server does not support this service!');
                die;
            }

            $userId = $this->user->get('id');
            
            //locate the records for this account if any
            $res = $RealmDb->prepare("SELECT * FROM `player_boosts` WHERE `account` = :acc AND `active` = '1' ORDER BY `unsetdate` ASC;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                while ($arr = $res->fetch())
                {
                    //verify that this boost is really active
                    $time = $this->getTime(true);
					$testeur = $arr['boosts'];
                    
                    if ($time->getTimestamp() > (int)$arr['unsetdate'])
                    {
		
                        //already expired
                        continue;
                    }
                    unset($time);
                    
                    //push to the active boosts
                    $ActiveBoosts[] = $arr;
                }
                unset($arr);
            }
            unset($res);
        }
        unset($RealmDb);

        $this->tpl->SetTitle('Boosts');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('boosts', array(
            'RealmId' => $RealmId,
            'Boosts' => $Boosts,
            'ActiveBoosts' => $ActiveBoosts,
            'pricing' => $pricing
        ));

        $this->tpl->AddFooterJs('template/js/page.boosts.js');
        $this->tpl->LoadFooter();
    }
    
    public function purchase()
    {
        //load libs
        $this->loadLibrary('accounts.finances');
        $this->loadLibrary('purchaseLog');

        //setup new instance of multiple errors
        $this->errors->NewInstance('purchase_boost');

        //bind the onsuccess message
        $this->errors->onSuccess('Your Boosts have been successfuly applied, please re-log.', '/boosts');

        //Define the variables
        $RealmId = $this->user->GetRealmId();
        $BoostId = isset($_POST['boost']) ? (int)$_POST['boost'] : false;
        $currency = isset($_POST['currency']) ? (int)$_POST['currency'] : false;
        $DurationId = isset($_POST['duration']) ? (int)$_POST['duration'] : false;

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare the log
        $logs = new purchaseLog();

        //The boosts storage
        $BoostsStorage = new BoostsData();

        if (!$BoostId)
        {
            //no boost selected
            $this->errors->Add('Please select boost first.');
        }
        else if (!($BoostDetails = $BoostsStorage->get($BoostId)))
        {
            //Verify the boost id
            $this->errors->Add('The selected boost is invalid.');
        }
        
        if (!$currency)
        {
            //no currency is selected
            $this->errors->Add('Please select a currency for the purchase.');
        }
        else if (!$finance->IsValidCurrency($currency))
        {
            //invalid currency
            $this->errors->Add('Error, invalid currency selected.');
        }
        
        if (!$DurationId)
        {
            $this->errors->Add('Please select boost duration.');
        }
        else if (!in_array($DurationId, array(BOOST_DURATION_10, BOOST_DURATION_15, BOOST_DURATION_30)))
        {
            $this->errors->Add('The selected boost duration is invalid.');
        }

        //check if realm exists
        if (!$this->realms->realmExists($RealmId))
        {
            $this->errors->Add("The selected realm is invalid.");
        }
        
        //get the realm
        $realm = $this->realms->getRealm($RealmId);

        //check if the characters database is reachable
        if (!$realm->checkCharactersConnection())
        {
            $this->errors->Add("The website failed to load realm database. Please contact the administration for more information.");
        }

        //Check for errors
        $this->errors->Check('/boosts');

        // Load the config
        $pricing = $this->configItem('Pricing', 'boosts');
        $price = $pricing[$DurationId][$currency];

        ######################################
        ######### CHECK FINANCES #############
        $finance->SetCurrency($currency);
        $finance->SetAmount($price);

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

        //Check for errors
        $this->errors->Check('/boosts');

        //Get the time
        $time = $this->getTime(true);
        
        //start logging
        $logs->add('BOOSTS', 'Boost service. Currency: '.$currency.', Boost: '.$BoostId.', Duration: '.$DurationId.', Price: '.$price.', RealmId: '.$RealmId.'.', array(
            'boost' => $BoostId,
            'duration' => $DurationId,
            'selected_currency' => $currency,
            'price' => $price,
            'realm' => $RealmId
        ));
        
        //set the realm
        if ($RealmDB = $realm->getCharactersConnection())
        {
            $userId = $this->user->get('id');

            # Check if the boost is already active
            $res = $RealmDB->prepare("SELECT * FROM `player_boosts` WHERE `account` = :acc AND `boosts` = :boost LIMIT 1;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->bindParam(':boost', $BoostId, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $arr = $res->fetch();
                
                if ($time->getTimestamp() > (int)$arr['unsetdate'])
                {
                    //already expired, remove from the database
                    $delete = $RealmDB->prepare("DELETE FROM `player_boosts` WHERE `account` = :acc AND `boosts` = :boost LIMIT 1;");
                    $delete->bindParam(':acc', $userId, PDO::PARAM_INT);
                    $delete->bindParam(':boost', $BoostId, PDO::PARAM_INT);
                    $delete->execute();
                }
                else
                {
                    $this->errors->Add('The selected boost is already active, please wait untill it has expired.');

                    //update the log
                    $logs->update('The selected boost is already active.', 'error');
                }
            }
            unset($res);
            
            //Check for errors
            $this->errors->Check('/boosts');
            
            //Calculate the expire time
            $DurationStrings = array(
                BOOST_DURATION_10 => '10 days',
                BOOST_DURATION_15 => '15 days',
                BOOST_DURATION_30 => '30 days'
            );
            
            $Expires = $time->getTimestamp() + strtotime($DurationStrings[$DurationId], 0);
            $userId = $this->user->get('id');
            $setDate = $time->getTimestamp();
 if ($BoostId == 5)
			{	
			//Give the rank VIP
            $update = $this->db->prepare("UPDATE `account_data` SET `rank` = :boost WHERE `id` = :acc LIMIT 1;");
            $update->bindParam(':acc', $userId, PDO::PARAM_INT);
            $update->bindParam(':boost', $BoostId, PDO::PARAM_INT);
            $update->execute();
			}
            //Give the boost
            $insert = $RealmDB->prepare("INSERT INTO `player_boosts` (`account`, `boosts`, `setdate`, `unsetdate`, `active`) VALUES (:acc, :boost, :setdate, :expire, '1');");
            $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
            $insert->bindParam(':boost', $BoostId, PDO::PARAM_INT);
            $insert->bindParam(':setdate', $setDate, PDO::PARAM_INT);
            $insert->bindParam(':expire', $Expires, PDO::PARAM_INT);
            $insert->execute();
            
            //check if the account was affected
            if ($insert->rowCount() > 0)
            {
                //update the log
                $logs->update('The boost has been insert with expire time: '.$Expires.' ['.$DurationStrings[$DurationId].'].');
                    
                //charge for the purchase
                $Charge = $finance->Charge("Purchased Boost", CA_SOURCE_TYPE_NONE);
                
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
                	
                $this->errors->triggerSuccess();
            }
            else
            {
                $this->errors->Add('The website failed to set your boost. Please contact the administration.');
                //log
                $logs->update('The website failed to insert the boost record.', 'error');
            }
            unset($insert, $DurationStrings);
        }
        else
        {
            $this->errors->Add("The website failed to connect to the server. Please contact the adminsitration.");
        }
        unset($RealmDB);

        $this->errors->Check('/boosts');
        exit;
    }
}