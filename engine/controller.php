<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Core_Controller extends CORE {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
        parent::__construct();
	}

	// --------------------------------------------------------------------

    protected function Json($result)
    {
        header('Content-Type: application/json');
        die (json_encode($result));
    }

    // --------------------------------------------------------------------

    protected function JsonError($message, $heading = false)
    {
        if (!$heading)
        {
            $heading = 'An error occurred!';
        }

        $result = array();
        $result['heading'] = $heading;
        $result['error'] = $message;

        header('Content-Type: application/json');
        die (json_encode($result));
    }
}