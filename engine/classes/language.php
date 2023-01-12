<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Language
{
	private $core;
	private $language;
	private $requestedFiles;
	private $data;
	private $clientData;

	public function __construct()
	{
		$this->core = &get_instance();
		
        $this->requestedFiles = array();
        $this->data = array();

		// Load the language
		$this->language = $this->core->configItem('Language');

		if (!is_dir(ROOTPATH.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$this->language))
		{
			$this->language = "english";

			if (!is_dir(ROOTPATH.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$this->language))
			{
				die("The language <b>".$this->core->configItem('Language')."</b> does not exist, and neither does English. Please install at least one language.");
			}
		}

		$this->load("general");
	}

	/**
	 * Get the currently active language name
	 * in lowercase, such as "english"
	 * @return String
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * Translate the JSON-stored language string to the desired language
	 * @param String $json
	 * @return String
	 */
	public function translateLanguageColumn($json)
	{
		$data = json_decode($json, true);

		if (is_array($data))
		{
			if (array_key_exists($this->language, $data))
			{
				return $data[$this->language];
			}
			else
			{
				return reset($data);
			}
		}
		else
		{
			return $json;
		}
	}

	/**
	  * Get the selected language
	 * @param String $json
	 * @return String
	 */
	public function getColumnLanguage($json)
	{
		$data = json_decode($json, true);

		if (is_array($data))
		{
			if (array_key_exists($this->language, $data))
			{
				return $this->language;
			}
			else
			{
				die($json." does not contain an entry for <b>".$this->language."</b> language.");
			}
		}
		else
		{
			return $this->language;
		}
	}

	/**
	 * Get a language string
	 * @param String $id
	 * @param String $file defaults to 'general'
	 */
	public function get($id, $file = 'general')
	{
		if (!in_array($file, $this->requestedFiles))
		{
			$this->load($file);
		}

		// Try to find the string in the current language
		if (array_key_exists($id, $this->data[$this->language][$file]))
		{
			return $this->data[$this->language][$file][$id];
		}
		else
		{
			return "Language string not found (".$id." in ".$file.")";
		}
	}

	/**
	 * Load a language file
	 * @param String $file
	 * @param String $language defaults to the current language
	 */
	private function load($file, $language = false)
	{
		// Default to the current language
		if (!$language)
		{
			$language = $this->language;
		}

		// Prevent errors
		if (!array_key_exists($language, $this->data))
		{
			$this->data[$language] = array();
		}

		// Add it to the list of requested files if it doesn't exist already
		if (!in_array($file, $this->requestedFiles))
		{
			array_push($this->requestedFiles, $file);
		}

        $path = '';

		// Look in the shared directory
		if (file_exists(ROOTPATH.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$file.".php"))
		{
			$path = ROOTPATH.DIRECTORY_SEPARATOR."languages".DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$file.".php";
		}
        else
		{
			$this->data[$language][$file] = array();
			die("Language file <b>".$file.".php</b> does not exist in languages/".$language."/");
		}

		// Load the requested language file
		require($path);

		// Save it to the data array
		$this->data[$language][$file] = isset($lang) ? $lang : array();
	}

	public function setClientData($id, $file = 'general')
	{
		$this->clientData[$file][$id] = $this->get($id, $file);
	}

	/**
	 * Get the client side language strings as JSON
	 * @return String
	 */
	public function getClientData()
	{
		return json_encode($this->clientData);
	}
}