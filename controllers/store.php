<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Store extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loggedInOrReturn();

        $RealmId = $this->user->GetRealmId();

        $search = (isset($_GET['search']) ? $_GET['search'] : '');
        $quality = (isset($_GET['quality']) ? $_GET['quality'] : '-1');

        //define items per page
        $perPage = 6;

        $this->tpl->SetTitle('Store');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-store.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('store/store', array(
            'RealmId' => $RealmId,
            'search' => $search,
            'quality' => $quality,
            'perPage' => $perPage
        ));

        $this->tpl->LoadFooter();
    }

    public function get_items()
    {
        $realmId 		= (isset($_POST['realm']) && $_POST['realm'])   ? (int)$_POST['realm'] 	        : $this->user->GetRealmId();
        $character 	    = isset($_POST['character']) 					? $_POST['character'] 		    : false;
        $category 	    = isset($_POST['category']) 					? (int)$_POST['category'] 	    : false;
        $subcategory    = isset($_POST['subcategory']) 				    ? (int)$_POST['subcategory']    : false;
        $search 		= isset($_POST['search']) 					    ? $_POST['search'] 		        : false;
        $quality 	    = isset($_POST['quality']) 					    ? (int)$_POST['quality'] 	    : false;
        $minlevel 	    = isset($_POST['minlevel']) 					? (int)$_POST['minlevel']       : false;
        $maxlevel 	    = isset($_POST['maxlevel']) 					? (int)$_POST['maxlevel']       : false;
        $havecurrency   = isset($_POST['havecurrency']) 				? ($_POST['havecurrency'] == 'false' ? false : true) : false;

        if (!$this->realms->realmExists($realmId))
        {
            die ('<li class="info">Invalid realm.</li>');
        }

        // Prepare the where string and parameters array
        $where_str = '';
        $Parameters = array();

        if ($category !== false && $category > -1)
        {
            $where_str .= ' AND `class` = :class';
            $Parameters[] = array('key' => 'class', 'value' => $category, 'type' => PDO::PARAM_INT);
        }

        if ($subcategory !== false && $subcategory > -1)
        {
            $where_str .= ' AND `subclass` = :subclass';
            $Parameters[] = array('key' => 'subclass', 'value' => $subcategory, 'type' => PDO::PARAM_INT);
        }

        if ($quality !== false && $quality > -1)
        {
            $where_str .= ' AND `Quality` = :quality';
            $Parameters[] = array('key' => 'quality', 'value' => $quality, 'type' => PDO::PARAM_INT);
        }
            
        if ($search !== false && $search != '')
        {
            $where_str .= " AND `name` LIKE CONCAT('%', :name, '%')";
            $Parameters[] = array('key' => 'name', 'value' => $search, 'type' => PDO::PARAM_STR);
        }

        if ($minlevel !== false && $maxlevel !== false)
        {
            $where_str .= " AND (`RequiredLevel` >= :minlevel AND `RequiredLevel` <= :maxlevel)";
            $Parameters[] = array('key' => 'minlevel', 'value' => $minlevel, 'type' => PDO::PARAM_INT);
            $Parameters[] = array('key' => 'maxlevel', 'value' => $maxlevel, 'type' => PDO::PARAM_INT);
        }

        if ($havecurrency !== false)
        {
            $where_str .= " AND (`gold` <= :gold AND `silver` <= :silver)";
            $Parameters[] = array('key' => 'gold', 'value' => $this->user->get('gold'), 'type' => PDO::PARAM_INT);
            $Parameters[] = array('key' => 'silver', 'value' => $this->user->get('silver'), 'type' => PDO::PARAM_INT);
        }

        // Run the query
        $res = $this->db->prepare("SELECT * FROM `store_items` WHERE (LOCATE(:realm, realm) > 0) ".$where_str." ORDER BY `Quality` DESC, `ItemLevel` DESC LIMIT 100;");
        $res->bindParam(':realm', $realmId, PDO::PARAM_INT);

        foreach ($Parameters as $k => $fltr)
            $res->bindParam(':' . $fltr['key'], $fltr['value'], $fltr['type']);
            
        $res->execute();
        
        if ($res->rowCount() > 0)
        {
            // Loop through the results and print the item rows
            while ($arr = $res->fetch())
            {
                echo '
                <li class="item">
                    <div id="hover"></div>
                    <div class="icon_cont">
                        <a href="'.item_url($arr['entry'], $realmId).'" onclick="return false;" id="icon" class="q', $arr['Quality'], '" data-realm="', $realmId, '" rel="item=', $arr['entry'], '" ', item_icon_style($arr['entry'], $realmId), '></a>
                    </div>
                    <div id="middle">
                        <a href="'.item_url($arr['entry'], $realmId).'" onclick="return false;" class="q', $arr['Quality'], '" data-realm="', $realmId, '" rel="item=', $arr['entry'], '">', $arr['name'], '</a>
                        <p>';
                        
                        if ((int)$arr['gold'] > 0)
                            echo '<span class="gold">', $arr['gold'], '</span> Gold Coins';
                        
                        if ((int)$arr['gold'] > 0 && (int)$arr['silver'] > 0)
                            echo ' &nbsp; / &nbsp; ';
                        
                        if ((int)$arr['silver'] > 0)
                            echo '<span class="silver">', $arr['silver'], '</span> Silver Coins';
                    
                        echo '
                        </p>
                    </div>
                    <input type="button" value="Purchase" class="simple_button purchase_button" data-id="', $arr['id'], '" data-price-gold="', $arr['gold'], '" data-price-silver="', $arr['silver'], '" />
                    <div class="clear"></div>
                </li>';
            }
        }
        else
        {
            echo '<li class="info">No items ware found.</li>';
        }
        exit;
    }

    public function purchase()
    {
        //check if the curuser is online
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in.');
        }

        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('item.refund.system');

        //prepare the log
        $logs = new purchaseLog();

        //prepare multi errors
        $this->errors->NewInstance('store');
           
        $charName = (isset($_POST['character']) ? $_POST['character'] : false);
        $item = (isset($_POST['item']) ? (int)$_POST['item'] : false);
        $currency = (isset($_POST['currency']) ? $_POST['currency'] : false);
        
        $RealmId = $this->user->GetRealmId();

        if (!$charName)
        {
            $this->JsonError("Please select a character first.");
        }

        if (!$item)
        {
            $this->JsonError("Please select an item.");
        }

        if (!$currency || !in_array($currency, array('gold', 'silver')))
        {
            $this->JsonError('The selected currency is invalid.');
        }

        //check if realm exists
        if (!$this->realms->realmExists($RealmId))
        {
            $this->JsonError("The selected realm is invalid.");
        }

        //get the realm
        $realm = $this->realms->getRealm($RealmId);

        //prepare commands class
        $command = $realm->getCommands();

        //check if the realm is online
        if ($command->CheckConnection() !== true)
        {
            $this->JsonError("The realm is currently unavailable. Please try again in few minutes.");
        }

        //check if the characters database is reachable
        if (!$realm->checkCharactersConnection())
        {
            $this->JsonError("The realm is currently unavailable. Please try again in few minutes.");
        }

        //check if the character belongs to this account
        if (!$realm->getCharacters()->isMyCharacter(false, $charName))
        {
            $this->JsonError("The selected character does not belong to this account.");
        }

        //get the account gold and silver
        $accountGold = (int)$this->user->get('gold');
        $accountSilver = (int)$this->user->get('silver');

        // Get character guid
        $charData = $realm->getCharacters()->getCharacterData(false, $charName, 'guid');

        //Get the character GUID
        $characterGUID = $charData['guid'];
        
        unset($charData);
        
        //get the store item record
        $res = $this->db->prepare("SELECT `id`, `entry`, `silver`, `gold` FROM `store_items` WHERE `id` = :item AND `realm` LIKE CONCAT('%', :realm, '%') LIMIT 1;");
        $res->bindParam(':item', $item, PDO::PARAM_INT);
        $res->bindParam(':realm', $RealmId, PDO::PARAM_INT);
        $res->execute();
                
        //check if we have found the item
        if ($res->rowCount() == 0)
        {
            $this->JsonError('The item does not exist in the store.');
        }

        //fetch the record
        $row = $res->fetch();
        
        //money string for logs
        $moneyString = '';

        //now check if the account has the money needed
        //if the currency is silver
        if ($currency == 'silver')
        {
            //Check if the item is purchasable with silver
            if ((int)$row['silver'] > 0)
            {
                if ($accountSilver >= (int)$row['silver'])
                {
                    //remove the money from the account
                    $accountSilver = $accountSilver - (int)$row['silver'];
                }
                else
                {
                    //save the item error
                    $this->JsonError('You do not have enough silver to buy this item.');
                }
            }
            else
            {
                $this->JsonError('This item cannot be purchased with silver.');
            }

            $moneyString = $row['silver'] . ' Silver';
        }
        else
        {
            //Check if the item is purchasable with silver
            if ((int)$row['gold'] > 0)
            {
                if ($accountGold >= (int)$row['gold'])
                {
                    //remove the money from the account
                    $accountGold = $accountGold - (int)$row['gold'];
                }
                else
                {
                    $this->JsonError('You do not have enough gold to buy this item.');
                }
            }
            else
            {
                $this->JsonError('This item cannot be purchased with gold.');
            }

            $moneyString = $row['gold'] . ' Gold';
        }
        
        //the user has the money send the item
        $price = $currency == 'silver' ? (int)$row['silver'] : (int)$row['gold'];

        //start logging
        $logs->add('STORE', 'Store. Item: '.$item.', currency: '.$currency.', price: '.$price.', character: '.$charName.' in realm: '.$RealmId.'.', array(
            'item' => $item,
            'item_entry' => (int)$row['entry'],
            'selected_currency' => ($currency == 'silver' ? CURRENCY_SILVER : CURRENCY_GOLD),
            'price' => $price,
            'character' => $charName,
            'realm' => $RealmId
        ));

        //send the items
        $sentMail = $command->sendItems($charName, $row['entry'], 'Store Item Delivery');
        
        //make sure the mail was sent
        if ($sentMail === true)
        {
            //update the log
            $logs->update('The item send command was successfully executed.');

            $userId = $this->user->get('id');
            $theTime = $this->getTime();

            //insert in the store_activity table
            $insert = $this->db->prepare("INSERT INTO `store_activity` (`account`, `source`, `text`, `time`, `itemId`, `money`) VALUES (:acc, 'STORE', 'Purchase', :time, :itemId, :money);");
            $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
            $insert->bindParam(':time', $theTime, PDO::PARAM_STR);
            $insert->bindParam(':itemId', $item, PDO::PARAM_INT);
            $insert->bindParam(':money', $moneyString, PDO::PARAM_STR);
            $insert->execute();
            unset($insert);
            
            //Add the item as refundable
            $refundable = ItemRefundSystem::AddRefundable($row['entry'], $RealmId, $price, $currency == 'silver' ? CURRENCY_SILVER : CURRENCY_GOLD, $characterGUID);

            if ($refundable)
            {
                $logs->update('Successfully added the item as refundable.');
            }

            //update the account money
            $update = $this->db->prepare("UPDATE `account_data` SET `silver` = :silver, `gold` = :gold WHERE `id` = :account LIMIT 1;");
            $update->bindParam(':silver', $accountSilver, PDO::PARAM_INT);
            $update->bindParam(':gold', $accountGold, PDO::PARAM_INT);
            $update->bindParam(':account', $userId, PDO::PARAM_INT);	
            $update->execute();
            
            if ($update->rowCount() > 0)
            {
                //Update the user currencies for logging purpose mainly
                $this->user->set('silver', $accountSilver);
                $this->user->set('gold', $accountGold);

                //log into coin activity
                $ca = new CoinActivity();
                $ca->set_SourceType(CA_SOURCE_TYPE_NONE);
                $ca->set_SourceString('Item Purchase');
                $ca->set_CoinsType($currency == 'silver' ? CA_COIN_TYPE_SILVER : CA_COIN_TYPE_GOLD);
                $ca->set_ExchangeType(CA_EXCHANGE_TYPE_MINUS);
                $ca->set_Amount($price);
                $ca->execute();
                unset($ca);

                //update the log
                $logs->update('The user has been successfully charged for his purchase.', 'ok');
            }
            else
            {
                //update the log
                $logs->update('The user was not charged for his purchase, website failed to update.', 'error');
            }					
        }
        else
        {
            $this->JsonError("The website failed to deliver your purchase. Please contact the administration.");

            //update the log
            $logs->update('Soap failed to execute the send item command, errors: '.implode(', ', $sentMail).'.', 'error');
        }
        
        //return success
        $this->Json(array('success' => true));
    }

    /* Old method for multiple items...
    public function purchase()
    {
        $this->loggedInOrReturn();

        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('item.refund.system');

        //prepare the log
        $logs = new purchaseLog();

        //prepare multi errors
        $this->errors->NewInstance('store');
            
        $items = (isset($_POST['items']) ? $_POST['items'] : false);
        $charName = (isset($_POST['character']) ? $_POST['character'] : false);

        $RealmId = $this->user->GetRealmId();

        if (!$charName)
        {
            $this->errors->Add("Please select a character first.");
        }

        if (!$items)
        {
            $this->errors->Add("There ware no items to send.");
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

        //check if the character belongs to this account
        if (!$realm->getCharacters()->isMyCharacter(false, $charName))
        {
            $this->errors->Add("The selected character does not belong to this account.");
        }

        $this->errors->Check('/store');

        //get the account gold and silver
        $accountGold = (int)$this->user->get('gold');
        $accountSilver = (int)$this->user->get('silver');

        //start logging
        $logs->add('STORE', 'Store. Initial user currencies: '.$accountSilver.' silver, '.$accountGold.' gold. To character: '.$charName.' in realm: '.$RealmId.'.', array(
            'character' => $charName,
            'realm' => $RealmId
        ));

        //create error array
        $itemErrors = array();

        //create negative items string
        $itemsString = false;
        
        $charData = $realm->getCharacters()->getCharacterData(false, $charName, 'guid');

        //Get the character GUID
        $characterGUID = $charData['guid'];
        
        unset($charData);
        
        //loop the item list
        foreach ($items as $index => $data)
        {
            //set the default hasMoney to false
            $hasMoney = false;
            
            list($id, $currency) = explode(',', $data);
            
            //save the currency of this item in case of error
            $itemErrors[$index]['id'] = $id;
            $itemErrors[$index]['currency'] = $currency;
            $itemErrors[$index]['error'] = '';
            
            //get the store items records
            $res = $this->db->prepare("SELECT `id`, `entry`, `silver`, `gold` FROM `store_items` WHERE `id` = :item AND `realm` LIKE CONCAT('%', :realm, '%') LIMIT 1;");
            $res->bindParam(':item', $id, PDO::PARAM_INT);
            $res->bindParam(':realm', $RealmId, PDO::PARAM_INT);
            $res->execute();
                            
            //check if we have found the item
            if ($res->rowCount() > 0)
            {
                //fetch the record
                $row = $res->fetch();
                
                //now check if the account has the money needed
                if ($currency == 'gold' or $currency == 'silver')
                {
                    //if the currency is silver
                    if ($currency == 'silver')
                    {
                        //Check if the item is purchasable with silver
                        if ((int)$row['silver'] > 0)
                        {
                            if ($accountSilver >= (int)$row['silver'])
                            {
                                //define that the account has the money
                                $hasMoney = true;
                                //remove the money from the account
                                $accountSilver = $accountSilver - (int)$row['silver'];
                            }
                            else
                            {
                                //save the item error
                                $itemErrors[$index]['error'] = 'You do not have enough silver to buy this item.';
                                //log
                                $logs->update('The user does not have enough silver to complete the purchase of item (id: '.$id.').', 'error');
                            }
                        }
                        else
                        {
                            //save the item error
                            $itemErrors[$index]['error'] = 'This item cannot be purchased with silver.';
                            //log
                            $logs->update('The user is trying to purchase item (id: '.$id.') with the wrong currency.', 'error');
                        }
                        $moneyString = $row['silver'] . ' Silver';
                    }
                    else
                    {
                        //Check if the item is purchasable with silver
                        if ((int)$row['gold'] > 0)
                        {
                            if ($accountGold >= (int)$row['gold'])
                            {
                                //define that the account has the money
                                $hasMoney = true;
                                //remove the money from the account
                                $accountGold = $accountGold - (int)$row['gold'];
                            }
                            else
                            {
                                //save the item error
                                $itemErrors[$index]['error'] = 'You do not have enough gold to buy this item.';
                                //log
                                $logs->update('The user does not have enough gold to complete the purchase of item (id: '.$id.').', 'error');
                            }
                        }
                        else
                        {
                            //save the item error
                            $itemErrors[$index]['error'] = 'This item cannot be purchased with gold.';
                            //log
                            $logs->update('The user is trying to purchase item (id: '.$id.') with the wrong currency.', 'error');
                        }
                        $moneyString = $row['gold'] . ' Gold';
                    }
                    
                    //if the character has the money send the item
                    if ($hasMoney)
                    {
                        $theTime = $this->getTime();
                        
                        $currencyType = $currency == 'silver' ? CA_COIN_TYPE_SILVER : CA_COIN_TYPE_GOLD;
                        $price = $currency == 'silver' ? $row['silver'] : $row['gold'];
                        $userId = $this->user->get('id');

                        //insert in the store_activity table
                        $insert = $this->db->prepare("INSERT INTO `store_activity` (`account`, `source`, `text`, `time`, `itemId`, `money`) VALUES (:acc, 'STORE', 'Purchase', :time, :itemId, :money);");
                        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
                        $insert->bindParam(':time', $theTime, PDO::PARAM_STR);
                        $insert->bindParam(':itemId', $id, PDO::PARAM_INT);
                        $insert->bindParam(':money', $moneyString, PDO::PARAM_STR);
                        $insert->execute();
                        unset($insert);
                        
                        //log into coin activity
                        $ca = new CoinActivity();
                        $ca->set_SourceType(CA_SOURCE_TYPE_NONE);
                        $ca->set_SourceString('Item Purchase');
                        $ca->set_CoinsType($currencyType);
                        $ca->set_ExchangeType(CA_EXCHANGE_TYPE_MINUS);
                        $ca->set_Amount($price);
                        $ca->execute();
                        unset($ca);
                        
                        $currencyType = $currency == 'silver' ? CURRENCY_SILVER : CURRENCY_GOLD;
                        
                        //Add the item as refundable
                        $refundable = ItemRefundSystem::AddRefundable($row['entry'], $RealmId, $price, $currencyType, $characterGUID);
                        
                        //update the log
                        $logs->update('Finance ok ('.$moneyString.'), proceeding to sending item (id: '.$id.').' . ($refundable ? ' Successfully added the item as refundable.' : ''));
                        
                        //sending... append the item entry to the items string for the SOAP command
                        if (!$itemsString)
                        {
                            $itemsString = $row['entry'];
                        }
                        else
                        {
                            $itemsString .= ' ' . $row['entry'];
                        }
                        
                        unset($currencyType, $price, $theTime);
                    }
                }
                else
                {
                    //save the item error
                    $itemErrors[$index]['error'] = 'The selected currency for this item is invalid.';
                    //log
                    $logs->update('The user is using invalid currency for this purchase of item (id: '.$id.').', 'error');
                }
            }
            else
            {
                //save the item error
                $itemErrors[$index]['error'] = 'The item does not exist in the store.';
                //log
                $logs->update('The user is trying to purchase invalid item (id: '.$id.') that do not exist in the store.', 'error');
            }
            unset($res);
        }
        //end of the item loop
        
        //if we have any items to send
        if ($itemsString)
        {
            //Count the items in this string
            $qItems = explode(' ', $itemsString);
            
            //update the log
            $logs->update('Total items in the cart: ' . count($qItems));
                    
            //send the items
            $sentMail = $command->sendItems($charName, $itemsString, 'Store Item Delivery');
            
            //make sure the mail was sent
            if ($sentMail === true)
            {
                $userId = $this->user->get('id');

                //update the account money
                $update = $this->db->prepare("UPDATE `account_data` SET `silver` = :silver, `gold` = :gold WHERE `id` = :account LIMIT 1;");
                $update->bindParam(':silver', $accountSilver, PDO::PARAM_INT);
                $update->bindParam(':gold', $accountGold, PDO::PARAM_INT);
                $update->bindParam(':account', $userId, PDO::PARAM_INT);	
                $update->execute();
                
                if ($update->rowCount() > 0)
                {
                    //update the log
                    $logs->update('The mail was sent and the user has been successfully charged for his purchase. New values: '.$accountSilver.' silver, '.$accountGold.' gold.', 'ok');
                }
                else
                {
                    //update the log
                    $logs->update('The user was not charged for his purchase, website failed to update. Values that should have been applied: '.$accountSilver.' silver, '.$accountGold.' gold.', 'error');
                }					
            }
            else
            {
                $this->errors->Add("The website failed to deliver your purchase. Please contact the administration.");
                //update the log
                $logs->update('Soap failed to send the items, errors: '.implode(', ', $sentMail).'.', 'error');
            }
        }
        
        unset($characterGUID);
        
        //check for fatal errors before proceeding to the complete page
        $this->errors->Check('/store');
        
        //save the item array on a session
        $_SESSION['StoreItemReturn'] = $itemErrors;
        $_SESSION['StoreItemReturnChar'] = $charName;
        
        //redirect				
        header("Location: ".base_url()."/store/complete");
        exit;
    }*/

    public function complete()
    {
        $this->loggedInOrReturn();

        $RealmId = $this->user->GetRealmId();
        $realm = $this->realms->getRealm($RealmId);

        //check if we have the session set
        if (!isset($_SESSION['StoreItemReturn']))
        {
            header("Location: ".base_url()."/store");
            die;
        }

        $items = array();

        if (isset($_SESSION['StoreItemReturn']))
        {
            foreach ($_SESSION['StoreItemReturn'] as $id => $data)
            {
                $res = $this->db->prepare("SELECT `entry`, `name`, `Quality` FROM `store_items` WHERE `id` = :id LIMIT 1;");
                $res->bindParam(':id', $data['id'], PDO::PARAM_INT);
                $res->execute();

                if ($res->rowCount() > 0)
                {
                    $items[(int)$data['id']] = $res->fetch();
                }
            }
        }

        $this->tpl->SetTitle('Store');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-store-complete.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('store/complete', array(
            'RealmId' => $RealmId,
            'items' => $items
        ));

        $this->tpl->LoadFooter();
    }
}