<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Notfound extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }

    public function index()
    {
        $this->tpl->Message('Error', 'Page Not Found', 'The page you are looking for was not found!');
    }
}