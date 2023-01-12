<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Buycoins extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadConfig('payments');
    }
    
    public function index()
    {
        $this->tpl->SetTitle(lang('get_gold_coins', 'buycoins'));
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-buy-coins.css');
        $this->tpl->LoadHeader();

        $paypalConfig = $this->configItem('paypal', 'payments');
        $stripeConfig = $this->configItem('stripe', 'payments');
        $paymentwallConfig = $this->configItem('paymentwall', 'payments');

        //Print the page view
        $this->tpl->LoadView('buycoins/buycoins', array(
            'paypal_enabled' => $paypalConfig['enabled'],
            'stripe_enabled' => $stripeConfig['enabled'],
            'paymentwall_enabled' => $paymentwallConfig['enabled'],
        ));

        $this->tpl->LoadFooter();
    }

    public function paypal()
    {
        $this->tpl->SetTitle(lang('get_gold_coins', 'buycoins').' - PayPal');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-buycoins-pp.css');
        $this->tpl->LoadHeader();

        $paypalConfig = $this->configItem('paypal', 'payments');

        //Print the page view
        $this->tpl->LoadView('buycoins/paypal', array(
            'paypalConfig' => $paypalConfig,
            'coins_name' => $this->configItem('coins_name', 'payments'),
            'bonuses' => $this->configItem('bonuses', 'payments'),
        ));

        $this->tpl->AddFooterJs('template/js/page.buy.gcoins.js');
        $this->tpl->LoadFooter();
    }

    public function stripe()
    {
        $this->tpl->SetTitle(lang('get_gold_coins', 'buycoins').' - Stripe');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-buycoins-pp.css');
        $this->tpl->LoadHeader();

        $stripeConfig = $this->configItem('stripe', 'payments');

        //Print the page view
        $this->tpl->LoadView('buycoins/stripe', array(
            'stripeConfig' => $stripeConfig,
            'coins_name' => $this->configItem('coins_name', 'payments'),
            'bonuses' => $this->configItem('bonuses', 'payments'),
        ));

        $this->tpl->AddFooterJs('template/js/page.buy.gcoins.js');
        $this->tpl->LoadFooter();
    }

    public function paymentwall()
    {
        $this->tpl->SetTitle(lang('get_gold_coins', 'buycoins').' - Paymentwall');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        $paymentwallConfig = $this->configItem('paymentwall', 'payments');
        
        //Print the page view
        $this->tpl->LoadView('buycoins/paymentwall', array(
            'paymentwallConfig' => $paymentwallConfig
        ));

        $this->tpl->LoadFooter();
    }

    public function success()
    {
        $this->tpl->SetTitle(lang('get_gold_coins', 'buycoins'));
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('buycoins/success');

        $this->tpl->LoadFooter();
    }
}