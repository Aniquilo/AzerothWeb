<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Home extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Armory');
        $this->tpl->SetSubtitle('Armory');
        $this->tpl->AddCSS('template/style/armory-home.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('armory/home');

        $this->tpl->AddFooterJS('template/js/page.armory.home.js');
        $this->tpl->LoadFooter();
    }
}