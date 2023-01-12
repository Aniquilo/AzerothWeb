<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Features extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Features');
        $this->tpl->SetSubtitle('Features');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('features');

        $this->tpl->LoadFooter();
    }
}