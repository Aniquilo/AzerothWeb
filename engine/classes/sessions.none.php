<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Session
{		
	public function __construct()
	{		
		// set default sessions save handler
		ini_set('session.save_handler', 'files'); 
	}

	protected function _start()
	{
	    global $config;
	  
		session_name($config['SESSION_COOKIE']);
        session_set_cookie_params(strtotime($config['SESSION_LIFETIME'], 0), $config['SESSION_COOKIE_PATH'], $config['SESSION_COOKIE_DOMAIN'], $config['SESSION_COOKIE_SECURE'], $config['SESSION_COOKIE_HTTPONLY']);
		@session_start();
				
		return true;
	}

	public function register()
	{
		//Start the session if needed
		if (!isset($_SESSION))
		{
		    $this->_start();
		}
	}
	
    public function _open($save_path, $session_name)
	{	
	    return true;
    }

    public function _close()
    {
        return true;
    }

    public function _read($id)
	{
        return true; 
    }

    public function _write($id, $data)
	{
        return true; 
    }

    public function _destroy($id)
	{
	    return true;
    }

    public function _clean($max)
	{
   	    return true;
    }			
}