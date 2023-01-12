<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Template
{
    private $core;
    private $templateDir;
    
	/* 
        Css Resources to be loaded with the header
        Two arrays containing diferent priority of load
	*/
	private $CssFiles = array(
        RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
    );
	
	/* 
		Javascript Resources to be loaded with the header
		Two arrays containing diferent priority of load
	*/
	private $HeaderJsFiles = array(
		RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
	);
	
	/* 
		Javascript Resources to be loaded with the footer
		Two arrays containing diferent priority of load
	*/
	private $FooterJsFiles = array(
		RESOURCE_LOAD_PRIO_HIGH => array(),
		RESOURCE_LOAD_PRIO_LOW	=> array()
	);
	
	/*
		Default Template Parameters
	*/
	private $Parameters = array(
        'title'				=> '',
        'subtitle'          => '',
		'slider'			=> false,
		'topbar'			=> false
	);
	
	public function __construct()
	{
        $this->core =& get_instance();
        $this->templateDir = 'template';
	}
    
    public function SetTemplateDirectory($path)
    {
        $this->templateDir = $path;
    }

	public function SetParameters($parameters)
	{
		if (is_array($parameters) && !empty($parameters))
		{
			foreach ($parameters as $key => $param)
			{
				$this->Parameters[$key] = $param;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function GetParameters()
	{
		return $this->Parameters;
	}
	
	public function SetParameter($key, $param)
	{
		$this->Parameters[$key] = $param;
	}
	
	public function GetParameter($key)
	{
		if (isset($this->Parameters[$key]))
			return $this->Parameters[$key];
		
		return false;
	}
	
	/*
		A simple function to set page title
	*/
	public function SetTitle($title)
	{
		$this->Parameters['title'] = $title;
    }
    
    /*
		A simple function to set page subtitle
	*/
	public function SetSubtitle($title)
	{
		$this->Parameters['subtitle'] = $title;
	}
	
	public function AddCss($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->CssFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function AddHeaderJs($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->HeaderJsFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function AddFooterJs($file, $remote = false, $priority = RESOURCE_LOAD_PRIO_LOW)
	{
		$this->FooterJsFiles[$priority][] = array('file' => $file, 'remote' => $remote);
	}
	
	public function PrintCSS()
	{
		if (!empty($this->CssFiles[RESOURCE_LOAD_PRIO_LOW]) || !empty($this->CssFiles[RESOURCE_LOAD_PRIO_HIGH]))
		{
			//merge to load under single resource
			$combineFiles = array();

			$useMinifier = $this->core->configItem('UseMinifier');

            $files = array_merge($this->CssFiles[RESOURCE_LOAD_PRIO_HIGH], $this->CssFiles[RESOURCE_LOAD_PRIO_LOW]);

			//Let's print the CSS Files
			//Note there are remote loads
			foreach ($files as $css)
			{
				//handle remote load
				if ($css['remote'] || !$useMinifier)
				{
					//print right away
					echo '<link rel="stylesheet" href="', base_url(), '/', $css['file'], '" />';
				}
				else
				{
                    if (!isset($combineFiles[dirname($css['file'])]))
                    {
                        $combineFiles[dirname($css['file'])] = array();
                    }

                    $combineFiles[dirname($css['file'])][] = $css['file'];
				}
			}
			unset($css);
			
			if (!empty($combineFiles))
			{
                foreach ($combineFiles as $dir => $files)
                {
                    //remove the last ","
                    $string = implode(',', $files);
                    $combineDir = ROOTPATH.'/'.$dir.'/';
                    $fileName = 'combined-' . sha1($string) . '.css';

                    // Check if the combined file already exists
                    if (!file_exists($combineDir . $fileName))
                    {
                        foreach ($files as $file)
                        {
                            $fName = preg_replace("/\?v=[0-9]?/", "", $file);
                            $fileContent = file_get_contents(ROOTPATH . '/' . $fName);
                            
                            $fileContent = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $fileContent);
                            $fileContent = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $fileContent);
                            
                            file_put_contents($combineDir . $fileName, $fileContent, FILE_APPEND);
                        }
                        unset($fileContent);
                    }

                    echo '<link rel="stylesheet" href="', base_url(), '/', $dir, '/', $fileName, '" />';
				}
			}
			
            unset($string, $combineFiles, $combineDir, $fileName);
            
            // Clear the css array
            $this->CssFiles = array();
		}
	}
	
	public function PrintJavascripts($array)
	{
		if (is_array($array) && (!empty($array[RESOURCE_LOAD_PRIO_HIGH]) || !empty($array[RESOURCE_LOAD_PRIO_LOW])))
		{
			//merge to load under single resource
			$string = '';
			$combineFiles = array();

			$useMinifier = $this->core->configItem('UseMinifier');
			
			//Let's print the Js Files
			//Note there are remote loads
			//Note there are two levels of priority
			
			//Starting with high priority
			foreach ($array[RESOURCE_LOAD_PRIO_HIGH] as $js)
			{
				//handle remote load
				if ($js['remote'] || !$useMinifier)
				{
					//print right away
					echo '<script type="text/javascript" src="', base_url(), '/', $js['file'], '"></script>';
				}
				else
				{
					$string .= $js['file'].',';
					$combineFiles[] = $js['file'];
				}
			}
			unset($js);
			
			//Now low priority
			foreach ($array[RESOURCE_LOAD_PRIO_LOW] as $js)
			{
				//handle remote load
				if ($js['remote'] || !$useMinifier)
				{
					//print right away
					echo '<script type="text/javascript" src="', base_url(), '/', $js['file'], '"></script>';
				}
				else
				{
					$string .= $js['file'].',';
					$combineFiles[] = $js['file'];
				}
			}
			unset($js);
			
			if (!empty($combineFiles))
			{
				//remove the last ","
				$string = substr($string, 0, strlen($string) - 1);
				$combineDir = ROOTPATH . '/'.$this->templateDir.'/js/';
				$fileName = 'combined-' . sha1($string) . '.js';

				// Check if the combined file already exists
				if (!file_exists($combineDir . $fileName))
				{
					foreach ($combineFiles as $file)
					{
                        $fName = preg_replace("/\?v=[0-9]?/", "", $file);
						$fileContent = file_get_contents(ROOTPATH . '/' . $fName);
						$fileContent .= "\r\n";
						
						file_put_contents($combineDir . $fileName, $fileContent, FILE_APPEND);
					}

					unset($fileContent);
				}
				
				echo '<script type="text/javascript" src="', base_url(), '/'.$this->templateDir.'/js/', $fileName, '"></script>';
			}
			
			unset($string, $combineFiles, $combineDir, $fileName);
		}
	}
	
	public function PrintHeaderJavascripts()
	{
		$this->PrintJavascripts($this->HeaderJsFiles);
	}
	
	public function PrintFooterJavascripts()
	{
		$this->PrintJavascripts($this->FooterJsFiles);
    }
    
	public function LoadHeader()
	{
		$HeaderTitle = $this->core->configItem('SiteName') . ($this->Parameters['title'] != '' ? ' - ' . $this->Parameters['title'] : '');
		
		$this->LoadView('template/header', array(
            'HeaderTitle' => $HeaderTitle,
            'subtitle' => $this->Parameters['subtitle']
        ));
	}
	
	public function LoadFooter()
	{
		$this->LoadView('template/footer');
	}
    
    public function LoadView($name, $variables = array(), $print = true)
    {
        global $config;

        // Variables used within view files, it is not necessary to define them here but im too lazy to replace them with $CORE->var :]
        $CORE =& get_instance();
        $DB = $CORE->db;
        $ERRORS = $CORE->errors;
        $CACHE = $CORE->cache;

        $filePath = ROOTPATH . '/'.$this->templateDir.'/views/' . $name . '.php';
        $output = NULL;
        
        if (file_exists($filePath))
        {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $filePath;

            // End buffering and return its contents
            $output = ob_get_clean();

            if ($print)
            {
                echo $output;
            }
        }
        else
        {
            $this->SetSubtitle('Error');
            $this->LoadView('general/message', array('title' => 'Error', 'headline' => 'An error occured!', 'message' => 'Unabled to load view: ' . $name));
        }

        return $output;
    }

    public function Message($title, $headline, $message)
	{
        $this->SetSubtitle($title);
		$this->LoadHeader($title);
		$this->LoadView('general/message', array('title' => $title, 'headline' => $headline, 'message' => $message));
		$this->LoadFooter();
		die;
    }
    
	public function UnderConstruction($title)
	{
        $this->SetSubtitle($title);
		$this->LoadHeader('Under Construction');
		$this->LoadView('general/construction', array('title' => $title));
		$this->LoadFooter();
		die;
	}
	
	public function BufferFlush()
	{
		echo "\n\n<!-- Deal with browser-related buffering by sending some incompressible strings -->\n\n";

    	while (ob_get_level())
        	ob_end_flush();

    	if (ob_get_length())
		{
			@ob_flush();
			@flush();
			@ob_end_flush();
        }
        
    	@ob_start();
	}
	
	public function __destrruct()
	{
		unset($this->CssFiles, $this->HeaderJsFiles, $this->FooterJsFiles, $this->Parameters);
	}
}