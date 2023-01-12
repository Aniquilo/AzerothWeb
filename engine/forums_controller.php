<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Forums_Controller extends Core_Controller {

	/**
	 * Reference to the singleton
	 *
	 * @var	object
	 */
	private static $instance;

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
        parent::__construct();
        self::$instance =& $this;
        
        define('is_forums', true);

        $this->loadLibrary('forums.base');
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Core singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}
}