<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Cache
{
	private $repo;
	private $ext = 'cache';
	
	public function __construct($config)
	{
		$repo = ROOTPATH . '/cache';
		
		if (isset($config['repo']))
		{
			$repo = $config['repo'];
		}
			
		if (isset($config['ext']))
		{
			$this->ext = $config['ext'];
		}
		
		$repo = str_replace("\\", "/", $repo); 
		
		if (substr($repo, -1) != "/")
			$repo .= "/";
			
        $this->repo = $repo;
	}
	
	public function get($var)
	{
		$cache_str = @file_get_contents($this->repo . $var . '_' . $this->ext);
		
		if (empty($cache_str))
			return false;
			
		$cache = unserialize($cache_str);
		
		if (!isset($cache['expires']) || !isset($cache['data']))
			return false;
			
		if ($cache['expires'] !== false && $cache['expires'] < time())
		{
			@file_put_contents($this->repo . $var . '_' . $this->ext, '');
			return false;
		}
		
		return $cache['data'];
	}
	
	public function store($name, $val, $expires = 600, $group = '')
	{
		$cache = array('expires' => ($expires === false ? false : time() + (int)$expires), 'data' => $val);
		
        if ($group != '')
        {
			$cache['group'] = $group;
        }

        $dir = dirname($this->repo . $name);

        if (!is_dir($dir))
        {
            mkdir($dir, 0755, true);
        }

		@file_put_contents($this->repo . $name . '_' . $this->ext, serialize($cache));
	}
	
	public function clear($var)
	{
		if (!is_array($var))
			$var = array($var);
			
		foreach ($var as $v)
		{
			file_put_contents($this->repo . $v . '_' . $this->ext, '');
		}
	}
	
	public function clear_all()
	{
		$ext = '_' . $this->ext;
		
		if ($handle = opendir($this->repo)) 
		{
		    while (false !== ($file = readdir($handle)))
			{
    			if (substr($file, strlen($ext) * -1) === $ext && $file != '.' && $file != '..')
		    		file_put_contents($this->repo . $file, '');
		    }
		    closedir($handle);
		}
	}
	
	public function clear_group($group)
	{
		$ext = '_' . $this->ext;
		
		if ($handle = opendir($this->repo)) 
		{
		    while (false !== ($file = readdir($handle)))
			{
    			if (substr($file, strlen($ext) * -1) === $ext && $file != '.' && $file != '..')
    			{
    				$cache_str = file_get_contents($this->repo.$file);
    				$cache = unserialize($cache_str);
					
    				if (!isset($cache['expires']) || !isset($cache['data']) || !isset($cache['group']) || $cache['group'] != $group)
    					continue;
						
		    		file_put_contents($this->repo . $file, '');
    			}
		    }
		    closedir($handle);
		}
	}
}