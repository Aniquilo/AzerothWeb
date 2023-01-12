<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class FileEditor
{
	var $filePath;
    var $fileArray;
    var $error;
	
    public function __construct($file)
    {
        $this->filePath = false;
        $this->fileArray = array();
        $this->error = false;

		try
        {
            $this->check_file($file);
		}
		catch (Exception $e)
        {
            $this->error = $e->getMessage();
            return;
        }

        $this->filePath = $file;

        try
        {
            $this->_lines2array();
		}
		catch (Exception $e)
        {
            $this->error = $e->getMessage();
            return;
        }
	}
	
    private function check_file($file)
    {
        if (!file_exists($file))
        {
			throw new Exception("FileEditor wasnt able to load file.<br><br> at file: ".__FILE__." line: ".__LINE__);
		}
		
        if (!is_writable($file))
        {
            if (!chmod($file, 0777))
            {
				throw new Exception("FileEditor wasnt able to set file read/write file permissions.");
			}
		}
    }
    
    private function _lines2array()
    {
        $handle = fopen($this->filePath, "r");
        
        if ($handle)
        {
            while (($buffer = fgets($handle)) !== false)
            {
                $this->fileArray[] = $buffer;
            }
		
            if (!feof($handle))
            {
                throw new Exception("Error: unexpected fgets() fail.");
            }
		
            fclose($handle);
        }
        else
        {
            throw new Exception('Error: FileEditor was not able to open the file: "'.$this->filePath.'".');
        }
    }

    public function GetError()
    {
        return $this->error;
    }
	
    public function array_print()
    {
		echo '<pre>';
		print_r($this->fileArray);
		echo '</pre>';
	}
	
    public function get_line($line)
    {
        if (isset($this->fileArray[$line]))
        {
		    $return = $this->fileArray[$line];
        }
        else
        {
			$return = false;
		}
	 
	    return $return;	
	}
	
    public function search($str, $sens = true)
    {					
        foreach ($this->fileArray as $key => $value)
        {
            if ($sens)
            {		
                if (strlen(strstr($value, $str)) > 0)
                {
				    $return = $key;
				    break;
			    }
				
            }
            else
            {
                if (strlen(stristr($value, $str)) > 0)
                {
				    $return = $key;
				    break;
			    }
			}
		}
		
        if (!isset($return))
        {
			$return = false;
		}
		
	    return $return;
	}
	
    public function str_replace_at_line($find, $replace, $line, $sens = true)
    {
        if (isset($this->fileArray[$line]))
        {
			$stack = $this->fileArray[$line];
        }
        else
        {
			$stack = false;
		}
		
        if ($stack)
        {
            if ($sens)
            {
			    $this->fileArray[$line] = str_ireplace($find, $replace, $stack);
            }
            else
            {
			    $this->fileArray[$line] = str_replace($find, $replace, $stack);
		    }
		}
	}
	
    public function change_line($line, $str)
    {
        if (isset($this->fileArray[$line]))
        {
			$this->fileArray[$line] = $str . "\n";
			$return = true;
        }
        else
        {
			$return = false;
		}
	 
	    return $return;	
	}
		
    public function push_line_after($line, $str)
    {
		$i = 0;
        foreach ($this->fileArray as $key => $value)
        {
			$this->fileArray[$i] = $value;
						
            if ($key == $line)
            {
				$i = $i + 1;
				$this->fileArray[$i] = $str . "\n";
			}
			
		    $i++;	
		}
	}
	
    public function delete_line($line)
    {
		unset($this->fileArray[$line]);
		
		$i = 0;
        foreach($this->fileArray as $key => $value)
        {
			unset($this->fileArray[$key]);
			
			$this->fileArray[$i] = $value;
			
		    $i++;
		}
    }
    
    public function append($str)
    {
		if (!is_array($this->fileArray))
		    $this->fileArray = array();
		
		$str = $str . "\n";
		
		//returns the new number
		$push = array_push($this->fileArray, $str);
		
	    return $push;
	}
	
    public function write()
    {
		$file_string = '';
		
		foreach ($this->fileArray as $key => $value){
						
			$file_string .= $value;
			
		}
        
		$write = file_put_contents($this->filePath, $file_string);
	    
		unset($file_string);
		
	    return $write;	
	}

    public function __destruct()
    {
		unset($this->filePath);
	    unset($this->fileArray);
	}
	
	//This function is special for array configs
    public function changeConfig($key, $value)
    {
		//Search for the line with our config
		$lineIndex = $this->search($key, false);
        $newLine = '';

		if ($lineIndex)
		{
            $line = $this->fileArray[$lineIndex];
            $inArray = (strpos($line, '=>') !== false);
     
            if ($inArray)
            {
                $line = substr($line, 0, strpos($line, '=>') + 2);
            }
            else
            {
                $line = substr($line, 0, strpos($line, '=') + 1);
            }

            if (is_int($value))
            {
                $newLine .= $line . ' '. $value . ($inArray ? ',' : ';');
            }
            else if (is_bool($value))
            {
                $newLine .= $line . ' '. ($value ? 'true' : 'false') . ($inArray ? ',' : ';');
            }
            else
            {
                $newLine .= $line . " '" . $value . "'" . ($inArray ? ',' : ';');
            }
        }
        else
        {
            $this->error = 'Could not file the line associate with key: "'.$key.'".';
            return false;
        }
        
	    //Returns true or false if the change was successfull
	    return $this->change_line($lineIndex, $newLine);
	}
}