<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Bugtracker extends Admin_Controller
{
    const APPROVED_REPORT_REWARD = 4;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->CheckPermission(PERMISSION_PREV_BUGTRACKER);
        
        $action = isset($_GET['action']) ? $_GET['action'] : 'browse';
        $filter = isset($_GET['filter']) ? ($_GET['filter'] != '-1' ? (int)$_GET['filter'] : false) : false;

        //Filters
        $where = "";
        
        if ($filter !== false)
        {
            $where = "WHERE `bugtracker`.`status` = :status";
        }
        
        $res = $this->db->prepare("SELECT `bugtracker`.`id`, 
                                    `bugtracker`.`status`, 
                                    `bugtracker`.`approval`, 
                                    `bugtracker`.`priority`, 
                                    `bugtracker`.`maincategory`, 
                                    `bugtracker`.`category`, 
                                    `bugtracker`.`subcategory`, 
                                    `bugtracker`.`title`, 
                                    `bugtracker`.`content`, 
                                    `bugtracker`.`added`, 
                                    `bugtracker`.`account`, 
                                    `account_data`.`displayName` 
                            FROM `bugtracker`
                            LEFT JOIN `account_data` 
                            ON `bugtracker`.`account` = `account_data`.`id`
                            ".$where."
                            ORDER BY `bugtracker`.`id` DESC;");
        if ($filter !== false)
        {
            $res->bindParam(':status', $filter, PDO::PARAM_INT);
        }
        $res->execute();
        
        //Print the page
        $this->PrintPage('bugtracker', array(
            'action' => $action,
            'filter' => $filter,
            'res' => $res
        ));
    }

    public function submit_edit()
    {
        //check for permissions
        $this->CheckPermission(PERMISSION_MAN_BUGTRACKER);

        //prepare multi errors
        $this->errors->NewInstance('edit_report');

        //bind on success
        $this->errors->onSuccess('The report was successfully updated.', '/admin/bugtracker');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $content = (isset($_POST['content']) ? $_POST['content'] : false);
        $priority = (isset($_POST['priority']) ? (int)$_POST['priority'] : false);
        $status = (isset($_POST['status']) ? (int)$_POST['status'] : false);

        if (!$id)
        {
            $this->errors->Add("Report id is missing.");
        }
        if (!$title)
        {
            $this->errors->Add("Please enter report title.");
        }
        if (!$content)
        {
            $this->errors->Add("Please enter report content.");
        }

        $this->errors->Check('/admin/bugtracker');

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, title, content, priority, status FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The report record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/bugtracker');

        //insert the news record
        $update = $this->db->prepare("UPDATE `bugtracker` SET `title` = :title, `content` = :content, `priority` = :priority, `status` = :status WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':title', $title, PDO::PARAM_STR);
        $update->bindParam(':content', $content, PDO::PARAM_STR);
        $update->bindParam(':priority', $priority, PDO::PARAM_INT);
        $update->bindParam(':status', $status, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the report.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/bugtracker');
        exit;
    }

    public function get_report()
    {
        $this->CheckPermissionSilent(PERMISSION_PREV_BUGTRACKER);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $data = array();

        if ($id)
        {
            $res = $this->db->prepare("SELECT * FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $id, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $data = $res->fetch();
            }
            else
            {
                $data = array('error' => 'No record was found.');
            }
            unset($res);
        }
        else
        {
            $data = array('error' => 'Missing id.');
        }

        header ("content-type: application/json");
        echo json_encode($data);
        exit;
    }

    public function approve()
    {
        $this->CheckPermissionSilent(PERMISSION_MAN_BUGTRACKER);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        if (!$id)
        {
            echo 'Report id is missing.';
            die;
        }
        
        //check if the news record exists
        $res = $this->db->prepare("SELECT * FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            echo 'The report record is missing.';
            die;
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);
        
        //check if the screenshot is already approved
        if ($row['approval'] != BT_APP_STATUS_PENDING)
        {
            echo 'The report must have pending approval status.';
            die;
        }

        //define the approve type
        $approval = BT_APP_STATUS_APPROVED;
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `bugtracker` SET `approval` = :approval WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':approval', $approval, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() == 0)
        {
            echo 'The website failed to update the report record.';
            die;
        }

        $ApprovedReportReward = self::APPROVED_REPORT_REWARD;

        //reward the user for approved screenshot
        $accUpdate = $this->db->prepare("UPDATE `account_data` SET `silver` = silver + :reward WHERE `id` = :id LIMIT 1;");
        $accUpdate->bindParam(':reward', $ApprovedReportReward, PDO::PARAM_INT);
        $accUpdate->bindParam(':id', $row['account'], PDO::PARAM_INT);
        $accUpdate->execute();
        
        //check if the reward was delivered
        if ($accUpdate->rowCount() > 0)
        {
            $this->loadLibrary('coin.activity');
            
            //log into coin activity
            $ca = new CoinActivity($row['account']);
            $ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
            $ca->set_SourceString('Approved Bug Report');
            $ca->set_CoinsType(CA_COIN_TYPE_SILVER);
            $ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
            $ca->set_Amount($ApprovedReportReward);
            $ca->execute();
            unset($ca);
            
            //success
            echo 'OK';
        }
        else
        {
            echo 'The website failed to deliver the reward to the user.';
            die;
        }
        exit;
    }

    public function disapprove()
    {
        $this->CheckPermissionSilent(PERMISSION_MAN_BUGTRACKER);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        if (!$id)
        {
            echo 'Report id is missing.';
            die;
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT * FROM `bugtracker` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            echo 'The report record is missing.';
            die;
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);
        
        //check if the screenshot is already approved
        if ($row['approval'] != BT_APP_STATUS_PENDING)
        {
            echo 'The report must have pending approval status.';
            die;
        }

        //define the approve type
        $approval = BT_APP_STATUS_DECLINED;
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `bugtracker` SET `approval` = :approval WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':approval', $approval, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() == 0)
        {
            echo 'The website failed to update the report record.';
            die;
        }
        else
        {			
            //success
            echo 'OK';
        }
        exit;
    }

    public function delete()
    {
        $this->CheckPermission(PERMISSION_MAN_BUGTRACKER);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('delete_report');
        
        //bind on success
        $this->errors->onSuccess('The report was successfully deleted.', '/admin/bugtracker');

        if (!$id)
        {
            $this->errors->Add("The report id is missing.");
        }
        
        $this->errors->Check('/admin/bugtracker');
        
        $delete = $this->db->prepare('DELETE FROM `bugtracker` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the report record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/bugtracker');
        exit;
    }
}