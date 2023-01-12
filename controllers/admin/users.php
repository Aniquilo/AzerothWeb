<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Users extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->CheckPermission(PERMISSION_PREV_USERS);

        //Print the page
        $this->PrintPage('users/users');
    }

    public function view()
    {
        $this->CheckPermission(PERMISSION_PREV_USERS);

        $account = isset($_GET['uid']) ? (int)$_GET['uid'] : false;

        //Print the page
        $this->PrintPage('users/view', array(
            'account' => $account
        ));
    }

    public function info()
    {
        $this->CheckPermissionSilent(PERMISSION_PREV_USERS);

        $account = isset($_GET['uid']) ? (int)$_GET['uid'] : false;

        header('Content-Type: application/json');

        if ($account === false)
        {
            echo json_encode(array('error' => 'No account specified.'));
            die;
        }

        //Find the user records
        $webRes = $this->db->prepare("SELECT `id`, `displayName`, `silver`, `gold`, `rank`, `status` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
        $webRes->bindParam(':acc', $account, PDO::PARAM_INT);
        $webRes->execute();
        
        //Verify the user
        if ($webRes->rowCount() == 0)
        {
            echo json_encode(array('error' => 'Account not found.'));
            die;
        }

        //fetch the webrecord
        $row = $webRes->fetch();

        echo json_encode($row);
        die;
    }

    public function change_rank()
    {
        $this->CheckPermission(PERMISSION_MAN_USERS);

        //prepare multi errors
        $this->errors->NewInstance('user_modify');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $rank = (isset($_POST['rank']) ? (int)$_POST['rank'] : false);

        if (!$id)
        {
            $this->errors->Add("The user id is missing.");
        }
        if ($rank === false)
        {
            $this->errors->Add("Please select any of the listed ranks.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $id);

        //check if the user record exists
        $res = $this->db->prepare("SELECT `id` FROM `account_data` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The user record is invalid or missing.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $id);

        // Get the user record
        $row = $res->fetch();

        //bind on success
        $this->errors->onSuccess('The user rank was successfully updated.', '/admin/users/view?uid=' . $id);
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `account_data` SET `rank` = :rank WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':rank', $rank, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the user's record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/users/view?uid=' . $id);
        exit;
    }

    public function set_roles()
    {
        $this->CheckPermission(PERMISSION_MAN_USER_ROLES);

        $uid = isset($_POST['uid']) ? (int)$_POST['uid'] : false;

        //prepare multi errors
        $this->errors->NewInstance('grant_permissions');

        if (!$uid)
        {
            $this->errors->Add("The user id is missing.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $uid);

        //Verify the user
        $res = $this->db->prepare("SELECT `id` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
        $res->bindParam(':acc', $uid, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The user seems to be invalid.");
        }
        unset($res);

        $this->errors->Check('/admin/users/view?uid=' . $uid);

        // Get the user roles
        $res = $this->db->prepare("SELECT * FROM `rbac_user_role` WHERE `user_id` = :user_id ORDER BY `role_id` ASC;");
        $res->execute(array('user_id' => $uid));
        $userRoles = array();

        if ($res->rowCount() > 0)
        {
            while ($ur = $res->fetch())
            {
                $userRoles[$ur['role_id']] = true;
            }
        }
        else
        {
            // Give player role if none set
            $userRoles[2] = true;
        }
        
        // Only owner can modify owner's roles
        if (isset($userRoles[7]) && !$this->user->hasRole(7))
        {
            $this->errors->Add("Only the owner can change owner's roles.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $uid);

        // Collect the roles we need to set
        $setRoles = array();

        foreach ($_POST['role'] as $roleId => $v)
        {
            $setRoles[] = (int)$roleId;
        }

        // Remove roles
        $sth = $this->db->prepare("DELETE FROM `rbac_user_role` WHERE `user_id` = :user_id;");
        $sth->execute(array('user_id' => $uid));

        // Add roles
        if (!empty($setRoles))
        {
            // Add roles
            foreach ($setRoles as $roleId)
            {
                $sth = $this->db->prepare("INSERT INTO `rbac_user_role` (`user_id`, `role_id`) VALUES (:user_id, :role_id);");
                $sth->execute(array('user_id' => $uid, 'role_id' => $roleId));
            }
        }

        //bind on success
        $this->errors->onSuccess('The user\'s access roles have been updated.', '/admin/users/view?uid=' . $uid);
        $this->errors->triggerSuccess();
    }

    public function change_displayname()
    {
        $this->CheckPermission(PERMISSION_MAN_USERS);

        //prepare multi errors
        $this->errors->NewInstance('user_modify');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $displayName = (isset($_POST['displayName']) ? $_POST['displayName'] : false);

        if (!$id)
        {
            $this->errors->Add("The user id is missing.");
        }
        if ($displayName === false || strlen($displayName) == 0)
        {
            $this->errors->Add("Please enter display name.");
        }
        else if (strlen($displayName) < 3)
        {
            $this->errors->Add("Display name too short.");
        }

        //check if the user record exists
        $res = $this->db->prepare("SELECT `id` FROM `account_data` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The user record is invalid or missing.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $id);

        // Get the user record
        $row = $res->fetch();

        //bind on success
        $this->errors->onSuccess('The user Display Name was successfully updated.', '/admin/users/view?uid=' . $id);
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `account_data` SET `displayName` = :displayName WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':displayName', $displayName, PDO::PARAM_STR);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the user's record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/users/view?uid=' . $id);
        exit;
    }

    public function change_currency()
    {
        $this->CheckPermission(PERMISSION_MAN_USERS_CURRENCY);

        //prepare multi errors
        $this->errors->NewInstance('user_modify');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $silver = (isset($_POST['silver']) ? (int)$_POST['silver'] : 0);
        $gold = (isset($_POST['gold']) ? (int)$_POST['gold'] : 0);

        if (!$id)
        {
            $this->errors->Add("The user id is missing.");
        }

        //check if the user record exists
        $res = $this->db->prepare("SELECT `id`, `silver`, `gold` FROM `account_data` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The user record is invalid or missing.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $id);

        // Get the user record
        $row = $res->fetch();

        //bind on success
        $this->errors->onSuccess('The user currencies ware successfully updated.', '/admin/users/view?uid=' . $id);
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `account_data` SET `silver` = :silver, `gold` = :gold WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':silver', $silver, PDO::PARAM_INT);
        $update->bindParam(':gold', $gold, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the user's record.");
        }
        else
        {
            $this->loadLibrary('coin.activity');

            // If silver has changed
            if ((int)$row['silver'] != $silver)
            {
                $diff = (int)$row['silver'] - $silver;

                // log into coin activity
                $ca = new CoinActivity($row['id']);
                $ca->set_SourceType(CA_SOURCE_TYPE_NONE);
                $ca->set_SourceString('Administration');
                $ca->set_CoinsType(CA_COIN_TYPE_SILVER);
                $ca->set_ExchangeType((int)$row['silver'] > $silver ? CA_EXCHANGE_TYPE_MINUS : CA_EXCHANGE_TYPE_PLUS);
                $ca->set_Amount(abs($diff));
                $ca->execute();
                unset($ca);
            }

            // If gold has changed
            if ((int)$row['gold'] != $gold)
            {
                $diff = (int)$row['gold'] - $gold;

                // log into coin activity
                $ca = new CoinActivity($row['id']);
                $ca->set_SourceType(CA_SOURCE_TYPE_NONE);
                $ca->set_SourceString('Administration');
                $ca->set_CoinsType(CA_COIN_TYPE_GOLD);
                $ca->set_ExchangeType((int)$row['gold'] > $gold ? CA_EXCHANGE_TYPE_MINUS : CA_EXCHANGE_TYPE_PLUS);
                $ca->set_Amount(abs($diff));
                $ca->execute();
                unset($ca);
            }

            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/users/view?uid=' . $id);
        exit;
    }

    public function change_status()
    {
        $this->CheckPermission(PERMISSION_MAN_USERS);

        //prepare multi errors
        $this->errors->NewInstance('user_modify');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $status = (isset($_POST['status']) ? $_POST['status'] : false);

        if (!$id)
        {
            $this->errors->Add("The user id is missing.");
        }
        if ($status === false || !in_array($status, [ 'pending', 'active', 'disabled' ]))
        {
            $this->errors->Add("The selected status is invalid.");
        }

        //check if the user record exists
        $res = $this->db->prepare("SELECT `id` FROM `account_data` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The user record is invalid or missing.");
        }

        $this->errors->Check('/admin/users/view?uid=' . $id);

        // Get the user record
        $row = $res->fetch();

        //bind on success
        $this->errors->onSuccess('The user Status was successfully updated.', '/admin/users/view?uid=' . $id);
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `account_data` SET `status` = :status WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':status', $status, PDO::PARAM_STR);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the user's record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/users/view?uid=' . $id);
        exit;
    }
}