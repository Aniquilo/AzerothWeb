<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Pm extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();
    }
    
    public function index()
    {
        $this->tpl->UnderConstruction('Private Messages');

        $this->tpl->SetTitle('Private Messages');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/message-system.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('pm/pm');

        $this->tpl->LoadFooter();
    }

    public function compose()
    {
        $this->tpl->UnderConstruction('Private Messages');

        $this->tpl->SetTitle('Compose a Message');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/message-system.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('pm/compose');

        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.js');
        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.bbcode.js');
        $this->tpl->LoadFooter();
    }

    public function read()
    {
        $this->tpl->UnderConstruction('Private Messages');

        $this->tpl->SetTitle('Reading Message');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/message-system.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('pm/read');

        $this->tpl->LoadFooter();
    }
}