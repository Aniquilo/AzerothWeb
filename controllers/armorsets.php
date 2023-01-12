<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Armorsets extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loggedInOrReturn();

        $RealmId = $this->user->GetRealmId();
        $filter = (isset($_GET['filter']) ? (int)$_GET['filter'] : 0);
        $perPage = 5;

        $this->tpl->SetTitle('Armor Sets');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-armor-sets.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('armorsets', array(
            'RealmId' => $RealmId,
            'filter' => $filter,
            'perPage' => $perPage
        ));

        $this->tpl->LoadFooter();
    }

    public function purchase()
    {
        $this->loggedInOrReturn();

        $this->loadLibrary('coin.activity');
        $this->loadLibrary('purchaseLog');
        $this->loadLibrary('accounts.finances');

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare the log
        $logs = new purchaseLog();

        //prepare multi errors
        $this->errors->NewInstance('pStore_armorsets');

        //bind the onsuccess message
        $this->errors->onSuccess('The armor set was successfully sent.', '/armorsets');

        $character = (isset($_POST['character']) ? $_POST['character'] : false);
        $armorset = (isset($_POST['armorset']) ? (int)$_POST['armorset'] : false);

        //Get the user selected realm
        $RealmId = $this->user->GetRealmId();

        if (!$character)
        {
            $this->errors->Add("Please select a character first.");
        }

        //find the armorset record
        $res = $this->db->prepare("SELECT `id`, `price`, `realm`, `items` FROM `armorsets` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $armorset, PDO::PARAM_INT);
        $res->execute();

        //check if we have found the record
        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The armor set record is missing.");
        }
        else
        {
            //fetch the armorset record
            $row = $res->fetch();
        }
        unset($res);

        //Set the currency and price
        $finance->SetCurrency(CURRENCY_GOLD);
        $finance->SetAmount((int)$row['price']);

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

        //check if the itemset realm is the current selected one
        if ($row['realm'] != '-1' && $RealmId != $row['realm'])
        {
            $this->errors->Add("The selected armor set is for another realm.");
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
        if (!$realm->getCharacters()->isMyCharacter(false, $character))
        {
            $this->errors->Add("The selected character does not belong to this account.");
        }

        $this->errors->Check('/armorsets');

        //start logging
        $logs->add('PSTORE_ARMORSETS', 'Armor sets. Armorset: '.$armorset.', Price: '.$row['price'].', Character: '.$character.', realmId: '.$RealmId.'.', array(
            'armorset' => $armorset,
            'selected_currency' => CURRENCY_GOLD,
            'price' => (int)$row['price'],
            'character' => $character,
            'realm' => $RealmId
        ));

        //prepare the items string
        $itemsString = str_replace(',', ' ', $row['items']);

        //level the character
        $sendItems = $command->sendItems($character, $itemsString, 'Armor Set Delivery');
        
        //check if the command was successfull
        if ($sendItems === true)
        {
            //update the log
            $logs->update('The send items command has been executed successfully.');

            //charge for the purchase
            $Charge = $finance->Charge("Armorset Purchase", CA_SOURCE_TYPE_NONE);
            
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
            $logs->update('Soap failed to execute the send items command. Errors: '.implode(', ', $sendItems).'.', 'error');
        }
            
        //check for fatal errors before proceeding to the complete page
        $this->errors->Check('/armorsets');
        exit;
    }

    public function get_filter()
    {
        $perPage = (isset($_GET['perPage']) ? (int)$_GET['perPage'] : 5);
        $category = (isset($_GET['category']) ? (int)$_GET['category'] : 0);
        $character = (isset($_GET['character']) ? $_GET['character'] : false);
        $realmId = (isset($_GET['realm']) ? (int)$_GET['realm'] : 1);

        $realm = $this->realms->getRealm($realmId);

        //define some defaults
        $where = "";
        $isFiltered = false;

        if ($category and $category > 0)
        {
            $where = "AND `category` = :filter";
            $isFiltered = true;
        }

        //get the character info
        if ($character and $character != '')
        {
            $charClass = $realm->getCharacters()->getCharacterData(false, $character, 'class');

            //append the conditions to the where variable
            $where .= " AND `class` IN('0', :class)";
        }

        //count the items
        $count_res = $this->db->prepare("SELECT COUNT(*) FROM `armorsets` WHERE `realm` = '-1' ".$where." or `realm` = :realm ".$where);
        $count_res->bindParam(':realm', $realmId, PDO::PARAM_INT);
        if (isset($charClass))
        {
            $count_res->bindParam(':class', $charClass['class'], PDO::PARAM_INT);
        }
        if ($isFiltered)
        {
            $count_res->bindParam(':filter', $category, PDO::PARAM_INT);
        }
        $count_res->execute();
        $count_row = $count_res->fetch(PDO::FETCH_NUM);

        $count = $count_row[0];
                    
        unset($count_row);
        unset($count_res);

        $totalPages = ceil($count / $perPage);

        header('Content-Type: application/json');

        echo json_encode(array(
            'totalPages' => (int)$totalPages,
            'totalRecords' => (int)$count
        ));
        exit;
    }

    public function get_page()
    {
        $page = ((isset($_GET['page'])) ? (int)$_GET['page'] : 1);
        $perPage = ((isset($_GET['perPage'])) ? (int)$_GET['perPage'] : 5);
        $category = (isset($_GET['category']) ? (int)$_GET['category'] : 0);
        $character = (isset($_GET['character']) ? $_GET['character'] : false);
        $realmId = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : 1);

        $realm = $this->realms->getRealm($realmId);

        //math the offset
        $offset = ($page - 1) * $perPage;

        //define some defaults
        $where = "";
        $isFiltered = false;
                        
        if ($category and $category > 0)
        {
            $where = "AND `category` = :filter";
            $isFiltered = true;
        }

        //get the armor set categories
        $res = $this->db->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");
        if ($res->rowCount() > 0)
        {
            while ($arr = $res->fetch())
            {
                $categories[$arr['id']] = $arr['name'];
            }
        }
        unset($res);

        //get the character info
        if ($character and $character != '')
        {
            $charClass = $realm->getCharacters()->getCharacterData(false, $character, 'class');
            //append the conditions to the where variable
            $where .= " AND `class` IN('0', :class)";
        }

        //get the database records
        $res = $this->db->prepare("SELECT * FROM `armorsets` WHERE `realm` = '-1' ".$where." or `realm` = :realm ".$where." ORDER BY id DESC LIMIT ".$offset.",".$perPage);
        $res->bindParam(':realm', $realmId, PDO::PARAM_INT);
        if (isset($charClass))
        {
            $res->bindParam(':class', $charClass['class'], PDO::PARAM_INT);
        }
        if ($isFiltered)
        {
            $res->bindParam(':filter', $category, PDO::PARAM_INT);
        }
        $res->execute();

        $totalSets = (int)$res->rowCount();
        $armorSets = array();

        if ($totalSets > 0)
        {
            $i = 1;
            while ($arr = $res->fetch())
            {
                //null the array
                unset($subInfo);

                //explode the items
                $items = explode(',', $arr['items']);

                //check for set specifications
                if ($arr['tier'] != '')
                {
                    $subInfo[] = $arr['tier'];
                }
                if ($arr['class'] != '' and $arr['class'] > 0)
                {
                    $subInfo[] = 'Class: ' . $this->realms->getClassString($arr['class']);
                }
                if ($arr['type'] != '')
                {
                    $subInfo[] = 'Type: ' . $arr['type'];
                }
                $subInfo[] = 'Items: '.count($items);

                // Add to the array
                $armorSets[] = array(
                    'id' => (int)$arr['id'],
                    'order' => $i,
                    'html' => 
                        '<ul class="armor-set container_3">
                            <li class="set-head">
                                <div id="info">
                                    <p id="set-name">'.$arr['name'].'</p>
                                    <span id="set-info">'.implode(' | ', $subInfo).'</span>
                                </div>
                                <div id="price"><p>'.$arr['price'].'</p> <span>Gold Coins</span></div>
                                <div id="arrow"></div>
                            </li>
                            <li class="armor-set-items" id="armor-set-'.$arr['id'].'-items">'.$arr['items'].'</li>
                        </ul>'
                );
                 
                $i++;
            }
        }

        header('Content-Type: application/json');

        echo json_encode(array(
            'totalSets' => $totalSets,
            'armorSets' => $armorSets
        ));
        exit;
    }
}