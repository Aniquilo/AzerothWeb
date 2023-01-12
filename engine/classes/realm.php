<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Realm
{
    private $core;
    private $id;
    private $config;
    private $characters;
    private $commands;
    private $realmstats;
    private $iteminfo;
    private $charsConnection;
    private $worldConnection;

    public function __construct($id, $config)
    {
        $this->core =& get_instance();

        $this->id = $id;
        $this->config = $config;
        $this->characters = null;
        $this->commands = null;
        $this->realmstats = null;
        $this->iteminfo = null;
        $this->charsConnection = null;
        $this->worldConnection = null;
    }

    /**
	 * Get the realm id
	 * @return Int
	 */
	public function getId()
	{
		return $this->id;
	}

    /**
	 * Get the realm name
	 * @return String
	 */
	public function getName()
	{
		return addslashes($this->config['name']);
    }
    
    /**
	 * Get the realm description
	 * @return String
	 */
	public function getDescription()
	{
		return addslashes($this->config['descr']);
    }

    /**
	 * Get the realm emulator name
	 * @return String
	 */
	public function getEmulator()
	{
		return $this->config['emulator'];
    }

    /**
	 * Get the realm info for the details page
	 * @return String
	 */
	public function getInfo()
	{
		return $this->config['info'];
    }

    /**
	 * Get the realm characters class object
	 * @return Object
	 */
	public function getCharacters()
	{
        if ($this->characters != null)
        {
            return $this->characters;
        }

        require_once ROOTPATH . '/engine/emulators/interfaces/characters.php';

        $filePath = ROOTPATH . '/engine/emulators/' . $this->config['emulator'] . '/characters.php';

        if (file_exists($filePath))
        {
            require_once $filePath;

            $className = $this->config['emulator'] . '_Characters';

            if (class_exists($className))
            {
                $this->characters = new $className($this->id);

                return $this->characters;
            }
            else
            {
                die ('Failed to load the characters class [' . $className . '] for emulator ' . $this->config['emulator'] . '.');
            }
        }
        else
        {
            die ('Failed to load the characters class for emulator ' . $this->config['emulator'] . '.');
        }
    }
    
    /**
	 * Get the realm commands class object
	 * @return Object
	 */
    public function getCommands()
	{
		if ($this->commands != null)
        {
            return $this->commands;
        }

        require_once ROOTPATH . '/engine/emulators/interfaces/commands.php';

        $filePath = ROOTPATH . '/engine/emulators/' . $this->config['emulator'] . '/commands.php';

        if (file_exists($filePath))
        {
            require_once $filePath;

            $className = $this->config['emulator'] . '_Commands';

            if (class_exists($className))
            {
                $this->commands = new $className($this->id);

                return $this->commands;
            }
            else
            {
                die ('Failed to load the commands class [' . $className . '] for emulator ' . $this->config['emulator'] . '.');
            }
        }
        else
        {
            die ('Failed to load the commands class for emulator ' . $this->config['emulator'] . '.');
        }
    }
    
    /**
	 * Get the realm realmstats class object
	 * @return Object
	 */
    public function getRealmstats()
    {
        if ($this->realmstats != null)
        {
            return $this->realmstats;
        }

        require_once ROOTPATH . '/engine/emulators/interfaces/realmstats.php';

        $filePath = ROOTPATH . '/engine/emulators/' . $this->config['emulator'] . '/realmstats.php';

        if (file_exists($filePath))
        {
            require_once $filePath;

            $className = $this->config['emulator'] . '_Realmstats';

            if (class_exists($className))
            {
                $this->realmstats = new $className($this->id);

                return $this->realmstats;
            }
            else
            {
                die ('Failed to load the realmstats class [' . $className . '] for emulator ' . $this->config['emulator'] . '.');
            }
        }
        else
        {
            die ('Failed to load the realmstats class for emulator ' . $this->config['emulator'] . '.');
        }
    }

    /**
	 * Get the realm info info class object
	 * @return Object
	 */
    public function getIteminfo()
    {
        if ($this->iteminfo != null)
        {
            return $this->iteminfo;
        }

        require_once ROOTPATH . '/engine/emulators/interfaces/iteminfo.php';
        
        $filePath = ROOTPATH . '/engine/emulators/' . $this->config['emulator'] . '/iteminfo.php';

        if (file_exists($filePath))
        {
            require_once $filePath;

            $className = $this->config['emulator'] . '_Iteminfo';

            if (class_exists($className))
            {
                $this->iteminfo = new $className($this->id);

                return $this->iteminfo;
            }
            else
            {
                die ('Failed to load the iteminfo class [' . $className . '] for emulator ' . $this->config['emulator'] . '.');
            }
        }
        else
        {
            die ('Failed to load the iteminfo class for emulator ' . $this->config['emulator'] . '.');
        }
    }

    /**
	 * Get config value
	 * @param String $key
	 * @return String
	 */
	public function getConfig($key = false)
	{
        if ($key === false)
        {
            return $this->config;
        }
        
		if (array_key_exists($key, $this->config))
		{
			return $this->config[$key];
        }
        
		return false;
    }
    
    public function getCharactersConnection()
	{
		//check if we have the connection stored
		if (!$this->charsConnection)
		{
            //store the newly made connection and return it
            $this->charsConnection = $this->core->PDOConnect($this->config['CharsDatabase']);
        }
        
		return $this->charsConnection;		
    }
    
    public function checkCharactersConnection()
    {
        return ($this->getCharactersConnection() !== false);
    }
	
	public function getWorldConnection()
	{
		//check if we have the connection stored
		if (!$this->worldConnection)
		{
            //store the newly made connection and return it
            $this->worldConnection = $this->core->PDOConnect($this->config['WorldDatabase']);
		}
		
		return $this->worldConnection;
    }
}