<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Admin_Controller extends Core_Controller {

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
        
        // This is required for the error output HTML
        $this->errors->SetIsACP(true);

        // Check if any user is online
        $this->loggedInOrReturn();

        //check for permissions
        if (!$this->user->hasPermission(PERMISSION_ACCESS_ACP))
        {
            header('HTTP/1.0 404 not found');
            die;
        }

        // Set template variables
        $this->tpl->SetTemplateDirectory('admin_template');
        $this->tpl->SetTitle('Admin Panel');
	}

    public function IsACP()
    {
        return true;
    }

    public function CheckPermission($permission)
    {
        //check for permissions
		$this->errors->NewInstance('permissions');
		
		if (!$this->user->hasPermission($permission))
		{
			$this->errors->Add('You do not have the required permissions.');
		}
		
		$this->errors->Check('/admin/home');
    }

    public function CheckPermissionSilent($permission)
    {
		if (!$this->user->hasPermission($permission))
		{
            header('HTTP/1.0 404 not found');
			die;
		}
    }

    public function ErrorBox($text)
	{
        $this->tpl->LoadHeader();

		echo '<!-- The content -->
			<section id="content">
				<div class="tab" id="maintab">
					<h2>An error has occured!</h2>
					<p>' . $text . '</p>
		        </div>';
		
        $this->tpl->LoadFooter();
        die;
    }

    public function PrintPage($viewName, $params = array())
    {
        //Print the header
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView($viewName, $params);

        //print the footer
        $this->tpl->LoadFooter();
    }

    public function RecursiveRemoveDirectory($directory, $empty = false)
	{
		if (substr($directory,-1) == '/')
		{
			$directory = substr($directory, 0, -1);
		}
		
		if (!file_exists($directory) || !is_dir($directory))
		{
			return false;
		}
		elseif (is_readable($directory))
		{
			$handle = opendir($directory);
			
			while (false !== ($item = readdir($handle)))
			{
				if ($item != '.' && $item != '..')
				{
					$path = $directory.DIRECTORY_SEPARATOR.$item;
					if (is_dir($path)) 
					{
						$this->RecursiveRemoveDirectory($path);
					}
					else
					{
						unlink($path);
					}
				}
			}
			closedir($handle);
			
			if ($empty == false)
			{
				if (!rmdir($directory))
				{
					return false;
				}
			}
		}
		
		return true;
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