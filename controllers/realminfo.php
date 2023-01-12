<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Realminfo extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $RealmId = isset($_GET['id']) ? (int)$_GET['id'] : $this->user->GetRealmId();

        if (!$this->realms->realmExists($RealmId))
        {
            $this->tpl->Message('Realm Details', 'Error!', 'The realm does not exist.');
        }

        //Set the title
        $this->tpl->SetTitle($this->realms->getRealm($RealmId)->getName() . ' Realm Details');
        $this->tpl->AddCSS('template/style/page-realm-details.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('realminfo');

        //print the footer
        $this->tpl->LoadFooter();
    }
}