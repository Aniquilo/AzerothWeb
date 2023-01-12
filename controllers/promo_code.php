<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Promo_code extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loggedInOrReturn();

        $this->tpl->SetTitle('Promotion Codes');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-promorion-codes.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('promo_code');

        $this->tpl->AddFooterJs('template/js/page.promo.codes.js');
        $this->tpl->LoadFooter();
    }

    public function lookup()
    {
        //Load the Tokens Module
        $this->loadLibrary('promo.codes');

        header('Content-type: text/json');

        $code = ((isset($_GET['code'])) ? $_GET['code'] : false);

        if (!$code)
        {
            echo json_encode(array('error' => 'The promo code is missing.'));
            die;
        }

        $code = preg_replace("/[^A-Za-z0-9]/", '', $code);

        //Setup new promo code
        $PCode = new PromoCode($code);

        //Verify promo code
        if ($PCode->Verify())
        {
            echo json_encode($PCode->getInfo());
            exit;
        }

        //If we're here then something is wrong
        echo json_encode(array('error' => $PCode->getLastError()));

        unset($PCode);
        exit;
    }

    public function redeem()
    {
        $this->loggedInOrReturn();
        
        //Load the promo code lib
        $this->loadLibrary('promo.codes');

        //prepare multi errors
        $this->errors->NewInstance('pcode');

        $RealmID = $this->user->GetRealmId();

        //Get the code
        $code = ((isset($_POST['code'])) ? $_POST['code'] : false);

        //Get the character name if passed
        $charName = ((isset($_POST['character'])) ? $_POST['character'] : false);

        if (!$code)
        {
            $this->errors->Add("Please enter promo code.");
        }

        $this->errors->Check('/promo_code');

        //Setup new promo code
        $PCode = new PromoCode($code);

        //set the account
        $PCode->setAccount($this->user->get('id'));

        //set the realm in case of item reward
        $PCode->setRealm($RealmID);

        //set character if online
        $PCode->setCharacter($charName);

        //Verify promo code
        if ($PCode->Verify())
        {
            //Reward the user
            if ($PCode->ProcessReward())
            {
                //bind the onsuccess message
                $this->errors->onSuccess('The promotion code was successfully redeemed.', '/promo_code');
                $this->errors->triggerSuccess();
                exit;
            }
            else
            {
                $this->errors->Add($PCode->getLastError());
            }
        }
        else
        {
            $this->errors->Add($PCode->getLastError());
        }

        $this->errors->Check('/promo_code');
        exit;
    }
}