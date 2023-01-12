<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Support extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function howto()
    {
        $this->tpl->SetTitle('How To');
        $this->tpl->SetSubtitle('How To');
        $this->tpl->AddCSS('template/style/page-support-all.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('support/howto');

        $this->tpl->AddFooterJs('template/js/jquery-ui-1.8.16.custom.min.js');
        $this->tpl->LoadFooter();
    }

    public function tos()
    {
        $this->tpl->SetTitle('Terms of Use');
        $this->tpl->SetSubtitle('Terms of Use');
        $this->tpl->AddCSS('template/style/page-terms-of-use.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('support/tos');

        $this->tpl->LoadFooter();
    }

    public function cookie_policy()
    {
        $this->tpl->SetTitle('Cookie Policy');
        $this->tpl->SetSubtitle('Cookie Policy');
        $this->tpl->AddCSS('template/style/page-terms-of-use.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('support/cookie_policy');

        $this->tpl->LoadFooter();
    }

    public function rules()
    {
        $this->tpl->SetTitle('Rules and Regulations');
        $this->tpl->SetSubtitle('Rules and Regulations');
        $this->tpl->AddCSS('template/style/page-rules.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('support/rules');

        $this->tpl->LoadFooter();
    }
}