<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Notification extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        //check if we are allowed to display notification
        if (!isset($_SESSION['AvailableNotification']) || $_SESSION['AvailableNotification'] === false)
        {
            header("Refresh: 0; url=".base_url()."/");
            die();
        }

        $_SESSION['AvailableNotification'] = false;

        $data = $this->notifications->GetFirst();

        if ($data === false)
        {
            header("Refresh: 0; url=".base_url()."/");
            die();
        }

        //Set the title
        $this->tpl->SetTitle($data['title']);
        $this->tpl->SetSubtitle($data['title']);

        //Print the header
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('notification', array('data' => $data));

        //print the footer
        $this->tpl->LoadFooter();
    }
}