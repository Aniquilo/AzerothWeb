<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class azerothcore_Commands implements emulator_Commands
{
    private $core;
    private $realmId;

	public function __construct($realmId)
	{
        $this->core =& get_instance();
        $this->realmId = $realmId;
	}

    public function ExecuteCommand($command)
	{
        $realmConfig = $this->core->getRealmConfig($this->realmId);
        
        if ($realmConfig)
        {
            try //Try to execute function
            {
                $cliente = new SoapClient(NULL,
                    array(
                        "location" 	=> "".$realmConfig['soap_protocol']."://".$realmConfig['soap_address'].":".$realmConfig['soap_port']."/",
                        "uri"   	=> 'urn:AC',
                        "style" 	=> SOAP_RPC,
                        "login" 	=> $realmConfig['soap_user'],
                        "password" 	=> $realmConfig['soap_pass']
                    )
                );
        
                $result = $cliente->executeCommand(new SoapParam($command, "command"));
            
            }
            catch(Exception $e)
            {
                return array('sent' => false, 'message' => $e->getMessage());
            }
        }
        else
        {
            return array('sent' => false, 'message' => 'Invalid realm id!');
        }
		 
	    return array('sent' => true, 'message' => $result);
    }
    
	public function CheckConnection()
	{
		//try to send the items
		$soapMsg = $this->ExecuteCommand('.help');
				
		//check if the mail was sent
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
		
	public function sendItems($charName, $itemsString, $subject)
	{
        $body = 'Thank you for your contribution and loyalty. Here are the items that you ordered. We wish you a good day and Don\'t forget to vote! Regards,
        '.$this->core->configItem('SiteName');
        
        $errors = array();
        $item_commands = $this->getSendItemsCommands($charName, $subject, $body, $itemsString);

        // Send all the queued mails
		foreach ($item_commands as $command)
		{
			// .send items
            $soapMsg = $this->ExecuteCommand($command);
            
            //check if the mail was sent
            if ($soapMsg['sent'] !== true)
            {
                $errors[] = $soapMsg['message'];
		    }
        }
        
        if (!empty($errors))
        {
            return $errors;
        }

        return true;
    }
    
    public function getSendItemsCommands($charName, $subject, $body, $itemsString)
	{
        $items = explode(' ', $itemsString);
		$item_commands = array();
		$mail_id = 0;
        $item_count = 1;
        
		// Loop through all items
		foreach ($items as $item)
		{
            // Limit to 8 items per mail
            if ($item_count > 8)
            {
                // Reset item count
                $item_count = 0;

                // Queue a new mail
                $mail_id++;
            }

            // Increase the item count
            $item_count++;

            if (!isset($item_commands[$mail_id]))
            {
                $item_commands[$mail_id] = ".send items ".$charName." \"".$subject."\" \"".$body."\"";
            }

            // Append the command
            $item_commands[$mail_id] .= " ".$item;
		}
		
		return $item_commands;
	}
	
	public function sendMoney($charName, $money, $subject)
	{
		//try to send the money
		$soapMsg = $this->ExecuteCommand('.send money '.$charName.' "'.$subject.'" "Thank you for your contribution and loyalty. Here is the gold that you ordered. We wish you a good day and Don\'t forget to vote! Regards,
		'.$this->core->configItem('SiteName').'" '.$money);
				
		//check if the mail was sent
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}

	public function levelTo($charName, $level)
	{
		$soapMsg = $this->ExecuteCommand('.character level '.$charName.' '.$level);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function FactionChange($charName)
	{
		$soapMsg = $this->ExecuteCommand('.character changefaction '.$charName);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function RaceChange($charName)
	{
		$soapMsg = $this->ExecuteCommand('.character changerace '.$charName);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function Customize($charName)
	{
		$soapMsg = $this->ExecuteCommand('.character customize '.$charName);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}

	public function Revive($charName)
	{
		$soapMsg = $this->ExecuteCommand('.revive '.$charName);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function Teleport($charName, $x, $y, $z, $mapId)
	{
		$soapMsg = $this->ExecuteCommand('.pteleport '.$charName.' '.$x.' '.$y.' '.$z.' '.$mapId);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
	
	public function RefundItem($entry, $charName)
	{
		$soapMsg = $this->ExecuteCommand('.refunditem '.$charName.' '.$entry);
		
		if ($soapMsg['sent'] === true)
		{
			return true;
		}
		else
		{
			return $soapMsg['message'];
		}
	}
}
