<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Downloads extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        //Set the title
        $this->tpl->SetTitle('Downloads');
        $this->tpl->SetSubtitle('Downloads');

        //CSS
        $this->tpl->AddCSS('template/style/page-support-all.css');

        //Print the header
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('downloads');

        //print the footer
        $this->tpl->LoadFooter();
    }
}