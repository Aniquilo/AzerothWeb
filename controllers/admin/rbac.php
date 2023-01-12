<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Rbac extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_MAN_RBAC);
    }

    public function index()
    {
        //Print the page
        $this->PrintPage('rbac/rbac');
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Check if we have an ID
        if (!$id)
        {
            $this->ErrorBox('The role id is missing.');
        }

        //lookup the record
        $res = $this->db->prepare("SELECT * FROM `rbac_roles` WHERE `role_id` = :id LIMIT 1");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        //verify that we found the record
        if ($res->rowCount() == 0)
        {
            $this->ErrorBox('The role is invalid or missing.');
        }

        $row = $res->fetch();

        //Print the page
        $this->PrintPage('rbac/edit', array(
            'id' => $id,
            'row' => $row
        ));
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('rbac');

        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $desc = (isset($_POST['desc']) ? $_POST['desc'] : '');

        if (!$name)
        {
            $this->errors->Add("Please enter name for the role.");
        }

        $this->errors->Check('/admin/rbac');

        $sth = $this->db->prepare("INSERT INTO `rbac_roles` (`role_name`, `role_desc`) VALUES (:name, :desc);");
        $sth->execute(array('name' => $name, 'desc' => $desc));

        if ($sth->rowCount() > 0)
        {
            // Set permissions
            if (isset($_POST['permissions']) && !empty($_POST['permissions']))
            {
                $role_id = $this->db->lastInsertId();

                foreach ($_POST['permissions'] as $permId => $v)
                {
                    $sth = $this->db->prepare("INSERT INTO `rbac_role_perm` (`role_id`, `perm_id`) VALUES (:role_id, :perm_id);");
                    $sth->execute(array('role_id' => $role_id, 'perm_id' => $permId));
                }
            }

            //bind on success
            $this->errors->onSuccess('The role was successfully created.', '/admin/rbac');
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('Failed to create the role record.');
        }

        $this->errors->Check('/admin/rbac');
        exit;
    }

    public function edit_submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('rbac');

        $id = isset($_POST['id']) ? (int)$_POST['id'] : false;
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $desc = (isset($_POST['desc']) ? $_POST['desc'] : '');

        //Check if we have an ID
        if (!$id)
        {
            $this->ErrorBox('The role id is missing.');
        }

        $this->errors->Check('/admin/rbac');

        //lookup the record
        $res = $this->db->prepare("SELECT * FROM `rbac_roles` WHERE `role_id` = :id LIMIT 1");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        //verify that we found the record
        if ($res->rowCount() == 0)
        {
            $this->ErrorBox('The role is invalid or missing.');
        }

        $row = $res->fetch();

        if (!$name)
        {
            $this->errors->Add("Please enter name for the role.");
        }

        $this->errors->Check('/admin/rbac/edit?id='.$id);

        // Update the role record
        $sth = $this->db->prepare("UPDATE `rbac_roles` SET `role_name` = :role_name, `role_desc` = :role_desc WHERE `role_id` = :role_id LIMIT 1;");
        $sth->execute(array('role_name' => $name, 'role_desc' => $desc, 'role_id' => $id));

        // Delete all role permissions
        $sth = $this->db->prepare("DELETE FROM `rbac_role_perm` WHERE `role_id` = :role_id;");
        $sth->execute(array('role_id' => $id));

        // Set permissions
        if (isset($_POST['permissions']) && !empty($_POST['permissions']))
        {
            foreach ($_POST['permissions'] as $permId => $v)
            {
                $sth = $this->db->prepare("INSERT INTO `rbac_role_perm` (`role_id`, `perm_id`) VALUES (:role_id, :perm_id);");
                $sth->execute(array('role_id' => $id, 'perm_id' => $permId));
            }
        }

        //bind on success
        $this->errors->onSuccess('The role was successfully updated.', '/admin/rbac');
        $this->errors->triggerSuccess();
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('rbac');
        
        //bind on success
        $this->errors->onSuccess('The role was successfully deleted.', '/admin/rbac');
        
        if (!$id)
        {
            $this->errors->Add("The role id is missing.");
        }

        // Dont allow deletion of Guest, Player and Owner
        if ($id == 7 || $id == 1 || $id == 2)
        {
            $this->errors->Add("The roles Guest, Player and Owner cannot be deleted.");
        }

        $this->errors->Check('/admin/rbac');
        
        $delete = $this->db->prepare('DELETE FROM `rbac_roles` WHERE `role_id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("Failed to delete the role.");
        }
        else
        {
            $delete = $this->db->prepare('DELETE FROM `rbac_role_perm` WHERE `role_id` = :id;');
            $delete->bindParam(':id', $id, PDO::PARAM_INT);
            $delete->execute();

            $delete = $this->db->prepare('DELETE FROM `rbac_user_role` WHERE `role_id` = :id;');
            $delete->bindParam(':id', $id, PDO::PARAM_INT);
            $delete->execute();

            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/rbac');
        exit;
    }
}