<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class purchaseLog
{
    private $core;
	private $lastLogId = NULL;
    private $data;
    
	public function __construct()
	{
        $this->core =& get_instance();
		$this->data = array();
	}
	
	public function add($source, $message, $data = array(), $status = 'pending')
	{
        $this->data = array(
            'start_silver' => (int)$this->core->user->get('silver'),
            'start_gold' => (int)$this->core->user->get('gold')
        );
        $this->data = array_merge($this->data, $data);

		$account = $this->core->user->get('id');
        $time = $this->core->getTime();
        $dataJson = json_encode($this->data);
        $ip = $this->core->security->getip();

		$insert = $this->core->db->prepare("INSERT INTO `purchase_log` (`account`, `source`, `text`, `time`, `status`, `data`, `ip_address`) VALUES (:account, :source, :text, :time, :status, :data, :ip);");
		$insert->bindParam(':account', $account, PDO::PARAM_INT);
		$insert->bindParam(':source', $source, PDO::PARAM_STR);
		$insert->bindParam(':text', $message, PDO::PARAM_STR);
		$insert->bindParam(':time', $time, PDO::PARAM_STR);
        $insert->bindParam(':status', $status, PDO::PARAM_STR);
        $insert->bindParam(':data', $dataJson, PDO::PARAM_STR);
        $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
		$insert->execute();
		
		//check if the record was inserted
		if ($insert->rowCount() > 0)
		{
			//update the last log id var
			$this->lastLogId = $this->core->db->lastInsertId();

			return true;
        }
        unset($insert);
        
		return false;
	}
	
	public function update($message, $status = false)
	{
		$update = $this->core->db->prepare("UPDATE `purchase_log` SET `text` = CONCAT(`text`, ' | Update: ', :text) ".($status ? ", `status` = :status" : "")." WHERE `id` = :logId LIMIT 1;");
		$update->bindParam(':text', $message, PDO::PARAM_STR);
		if ($status)
		{
			$update->bindParam(':status', $status, PDO::PARAM_STR);
		}
		$update->bindParam(':logId', $this->lastLogId, PDO::PARAM_INT);
		$update->execute();
		
		//check if the record was inserted
		if ($update->rowCount() > 0)
		{
            unset($update);
            
            if ($status == 'ok')
            {
                $this->updateData(array(
                    'end_silver' => (int)$this->core->user->get('silver'),
                    'end_gold' => (int)$this->core->user->get('gold')
                ));
            }

			return true;
        }
        
		return false;
    }
    
    public function updateData($data)
	{
		$this->data = array_merge($this->data, $data);
        $dataJson = json_encode($this->data);
        
		$update = $this->core->db->prepare("UPDATE `purchase_log` SET `data` = :data WHERE `id` = :logId LIMIT 1;");
        $update->bindParam(':data', $dataJson, PDO::PARAM_STR);
        $update->bindParam(':logId', $this->lastLogId, PDO::PARAM_INT);
		$update->execute();
		
		//check if the record was inserted
		if ($update->rowCount() > 0)
		{
			unset($update);
			return true;
        }
        
		return false;
	}
}