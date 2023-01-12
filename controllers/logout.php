<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Logout extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        if (!$this->user->isOnline())
        {
            header("Refresh: 0; url=".base_url()."/");
            exit();
        }

        //logout the user
        $this->user->logout();

        header("Location: ".base_url()."/");
        exit;
    }
}