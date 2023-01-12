<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Item_refund extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loggedInOrReturn();

        $this->tpl->SetTitle('Item Refunding');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-refund.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('item_refund');

        $this->tpl->AddFooterJs('template/js/page.items.refund.js');
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        if (!$this->user->isOnline())
        {
            echo 'Error: You must be logged in.';
            die;
        }

        //load libs
        $this->loadLibrary('item.refund.system');
        $this->loadLibrary('accounts.finances');
        $this->loadLibrary('purchaseLog');

        //Setup the finances class
        $finance = new AccountFinances();

        //prepare the log
        $logs = new purchaseLog();
        
        //prepare multi errors
        $this->errors->NewInstance('refund_item');

        $refundId = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        //verify the refund id
        if (!$refundId)
        {
            echo 'The refund id is missing.';
            die;
        }

        //Try getting the refund record
        $row = ItemRefundSystem::GetRefundable($refundId);

        if (!$row)
        {
            echo 'The refund id is invalid.';
            die;
        }

        //verify the refund status
        if ($row['status'] != IRS_STATUS_NONE)
        {
            echo 'The refund record has been already refunded.';
            die;
        }

        //Check if the user is allowed to refund more items this week
        if (ItemRefundSystem::GetRefundsDone() >= 2)
        {
            echo 'You are not allowed to refund more items this week.';
            die;
        }

        $RealmId = (int)$row['realmId'];

        if (!$RealmId)
        {
            echo 'There is no realm assigned to your account.';
            die;
        }

        if (!$this->realms->realmExists($RealmId))
        {
            echo 'The realm assigned to your account is invalid.';
            die;
        }

        //get the realm
        $realm = $this->realms->getRealm($RealmId);

        //prepare commands class
        $command = $realm->getCommands();

        //check if the realm is online
        if ($command->CheckConnection() !== true)
        {
            echo 'The realm is currently unavailable. Please try again in few minutes.';
            die;
        }

        //check if the characters database is reachable
        if (!$realm->checkCharactersConnection())
        {
            echo "The website failed to load realm database. Please contact the administration for more information.";
            die;
        }

        //Get the character name by the guid
        $charName = $realm->getCharacters()->getCharacterName($row['character']);
        
        //start logging
        $logs->add('ITEM_REFUND', 'Item refund. Refundable: '.$row['id'].', Character: '.$charName.' in realm: '.$RealmId.'.', array(
            'refundable' => $row['id'],
            'character' => $charName,
            'realm' => $RealmId
        ));

        //try unsticking
        $refund = $command->RefundItem($row['entry'], $charName);
        
        //unset the class
        unset($command, $charName);
        
        //Check if the item was destroyed
        if ($refund === true)
        {
            ItemRefundSystem::RefundableSetStatus($row['id'], IRS_STATUS_REFUNDED);

            //Set the currency
            $finance->SetCurrency((int)$row['currency']);

            //Set the amount we are Giving
            $finance->SetAmount((int)$row['price']);

            //Give coins to the user
            $Reward = $finance->Reward('Item Refund');

            //check if the coins ware not given
            if ($Reward !== true)
            {
                ItemRefundSystem::SetError($row['id'], 'The finance class failed to add the required amount to the user.');
                
                //log
                $logs->update('The website failed to update the user account balance.', 'error');

                echo 'The website failed to update your account balance. Please contact the administration.';
                die;
            }
            
            //log
            $logs->update('The item has been refunded.', 'ok');

            //register success message
            $this->errors->registerSuccess('The item has been successfully refunded.');
            
            echo 'OK';
        }
        else
        {
            //log
            $logs->update('Soap failed to execute the refund command, soap return: '.$refund, 'error');

            echo 'The website failed to refund the item. Please try again later or contact the administration.';
            die;
        }
        exit;
    }
}