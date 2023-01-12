<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Forums extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->CheckPermission(PERMISSION_FORUMS);

        // Get available roles
        $res = $this->db->query("SELECT `role_id`, `role_name` FROM `rbac_roles` ORDER BY `role_id` ASC;");
        $roles = $res->fetchAll();

        // Add a role for all roles
        $roles = array_merge([ 0 => [ 'role_id' => 0, 'role_name' => 'All Roles' ] ], $roles);

        unset($res);

        //Print the page
        $this->PrintPage('forums/forums', array(
            'roles' => $roles
        ));
    }

    public function edit()
    {
        $this->CheckPermission(PERMISSION_FORUMS);

        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Verify the ID
        if (!$id)
        {
            $this->ErrorBox('Missing forum id.');
        }

        //Try getting the record
        $res = $this->db->prepare("SELECT * FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->ErrorBox('Invalid forum id.');
        }

        //fetch
        $forum = $res->fetch();

        // Get categories
        $res = $this->db->query("SELECT * FROM `wcf_categories` ORDER BY `id` ASC;");
        $categories = $res->fetchAll();

        // Get available roles
        $res = $this->db->query("SELECT `role_id`, `role_name` FROM `rbac_roles` ORDER BY `role_id` ASC;");
        $roles = $res->fetchAll();

        // Add a role for all roles
        $roles = array_merge([ 0 => [ 'role_id' => 0, 'role_name' => 'All Roles' ] ], $roles);

        unset($res);

        // Is the selected category a class category
        $isCategoryClass = false;

        foreach ($categories as $cat)
        {
            if ((int)$forum['category'] == (int)$cat['id'])
            {
                $isCategoryClass = ((int)$cat['flags'] & WCF_FLAGS_CLASSES_LAYOUT);
                break;
            }
        }

        //Print the page
        $this->PrintPage('forums/forum_edit', array(
            'id' => $id,
            'forum' => $forum,
            'categories' => $categories,
            'isCategoryClass' => $isCategoryClass,
            'roles' => $roles
        ));
    }

    public function submit_create()
    {
        $this->CheckPermission(PERMISSION_FORUMS);

        //prepare multi errors
        $this->errors->NewInstance('addForum');

        //bind on success
        $this->errors->onSuccess('The forum was successfully added.', '/admin/forums');

        $name = (isset($_POST['name']) ? $_POST['name'] : '');
        $description = (isset($_POST['description']) ? $_POST['description'] : '');
        $category = (isset($_POST['category']) ? (int)$_POST['category'] : 0);
        $class = (isset($_POST['class']) ? (int)$_POST['class'] : 0);
        $flags = 0;

        $this->errors->Check('/admin/forums');

        $view_roles = (isset($_POST['view_roles']) ? $_POST['view_roles'] : 0);
        $topic_roles = (isset($_POST['topic_roles']) ? $_POST['topic_roles'] : 0);
        $post_roles = (isset($_POST['post_roles']) ? $_POST['post_roles'] : 0);

        if (is_array($view_roles)) $view_roles = implode(',', array_keys($view_roles));
        if (is_array($topic_roles)) $topic_roles = implode(',', array_keys($topic_roles));
        if (is_array($post_roles)) $post_roles = implode(',', array_keys($post_roles));

        //Determine the position we have to place this cat
        $res = $this->db->prepare("SELECT `position` FROM `wcf_forums` WHERE `category` = ? ORDER BY `position` DESC LIMIT 1;");
        $res->execute([$category]);
        
        if ($res->rowCount() > 0)
        {
            $row = $res->fetch();
            $position = $row['position'] + 1;
            unset($row);
        }
        else
        {
            $position = 0;
        }
        unset($res);
         
        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `wcf_forums` (`name`, `description`, `category`, `class`, `flags`, `position`, `view_roles`, `topic_roles`, `post_roles`) VALUES (:title, :descr, :cat, :class, :flags, :pos, :view_roles, :topic_roles, :post_roles);");
        $insert->bindParam(':title', $name, PDO::PARAM_STR);
        $insert->bindParam(':descr', $description, PDO::PARAM_STR);
        $insert->bindParam(':cat', $category, PDO::PARAM_INT);
        $insert->bindParam(':class', $class, PDO::PARAM_INT);
        $insert->bindParam(':flags', $flags, PDO::PARAM_INT);
        $insert->bindParam(':pos', $position, PDO::PARAM_INT);
        $insert->bindParam(':view_roles', $view_roles, PDO::PARAM_STR);
        $insert->bindParam(':topic_roles', $topic_roles, PDO::PARAM_STR);
        $insert->bindParam(':post_roles', $post_roles, PDO::PARAM_STR);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the forum record.");
        }
        else
        {
            unset($insert);
            $this->errors->triggerSuccess();
        }
        unset($insert);
            
        $this->errors->Check('/admin/forums');
        exit;
    }

    public function submit_edit()
    {
        $this->CheckPermission(PERMISSION_FORUMS);

        //prepare multi errors
        $this->errors->NewInstance('editForum');

        //bind on success
        $this->errors->onSuccess('The forum was successfully edited.', '/admin/forums');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $description = (isset($_POST['description']) ? $_POST['description'] : false);
        $category = (isset($_POST['category']) ? (int)$_POST['category'] : 0);
        $class = (isset($_POST['class']) ? (int)$_POST['class'] : 0);
        $flags = 0;

        if (!$id)
        {
            $this->errors->Add("Forum id is missing.");
        }

        $this->errors->Check('/admin/forums');

        $view_roles = (isset($_POST['view_roles']) ? $_POST['view_roles'] : 0);
        $topic_roles = (isset($_POST['topic_roles']) ? $_POST['topic_roles'] : 0);
        $post_roles = (isset($_POST['post_roles']) ? $_POST['post_roles'] : 0);

        if (is_array($view_roles)) $view_roles = implode(',', array_keys($view_roles));
        if (is_array($topic_roles)) $topic_roles = implode(',', array_keys($topic_roles));
        if (is_array($post_roles)) $post_roles = implode(',', array_keys($post_roles));

        //check if the forum record exists
        $res = $this->db->prepare("SELECT * FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The forum record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/forums/edit?id='.$id);

        //insert the news record
        $update = $this->db->prepare("UPDATE `wcf_forums` 
            SET `name` = :name, `description` = :descr, `category` = :cat, `class` = :class, `view_roles` = :view_roles, `topic_roles` = :topic_roles, `post_roles` = :post_roles 
            WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->bindParam(':name', $name, PDO::PARAM_STR);
        $update->bindParam(':descr', $description, PDO::PARAM_STR);
        $update->bindParam(':cat', $category, PDO::PARAM_INT);
        $update->bindParam(':class', $class, PDO::PARAM_INT);
        $update->bindParam(':view_roles', $view_roles, PDO::PARAM_STR);
        $update->bindParam(':topic_roles', $topic_roles, PDO::PARAM_STR);
        $update->bindParam(':post_roles', $post_roles, PDO::PARAM_STR);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the forum record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/forums/edit?id='.$id);
        exit;
    }

    public function submit_order()
    {
        $this->CheckPermissionSilent(PERMISSION_FORUMS);

        $order = (isset($_POST['order']) ? $_POST['order'] : false);

        if (!$order)
        {
            echo 'The order list is missing.';
            die;
        }

        foreach ($order as $position => $id)
        {
            //insert the news record
            $update = $this->db->prepare("UPDATE `wcf_forums` SET `position` = :pos WHERE `id` = :id LIMIT 1;");
            $update->bindParam(':pos', $position, PDO::PARAM_INT);
            $update->bindParam(':id', $id, PDO::PARAM_INT);
            $update->execute();
            unset($update);
        }

        echo 'OK';
        exit;
    }

    public function delete()
    {
        $this->CheckPermission(PERMISSION_FORUMS);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('deleteForum');
        
        //bind on success
        $this->errors->onSuccess('The forum was successfully deleted.', '/admin/forums');
        
        if (!$id)
        {
            $this->errors->Add("The forum id is missing.");
        }

        $this->errors->Check('/admin/forums');
        
        $delete = $this->db->prepare('DELETE FROM `wcf_forums` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the forum record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/forums');
        exit;
    }

    public function categories()
    {
        $this->CheckPermission(PERMISSION_FORUM_CATS);

        //Print the page
        $this->PrintPage('forums/categories');
    }

    public function submit_create_category()
    {
        $this->CheckPermission(PERMISSION_FORUM_CATS);

        //prepare multi errors
        $this->errors->NewInstance('forums_addcat');

        //bind on success
        $this->errors->onSuccess('The category was successfully added.', '/admin/forums/categories');

        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $style = (isset($_POST['style']) ? (int)$_POST['style'] : false);

        if (!$name)
        {
            $this->errors->Add("Please enter category title.");
        }

        $this->errors->Check('/admin/forums/categories');

        //Determine the position we have to place this cat
        $res = $this->db->prepare("SELECT `position` FROM `wcf_categories` ORDER BY `position` DESC LIMIT 1;");
        $res->execute();
        
        if ($res->rowCount() > 0)
        {
            $row = $res->fetch();
            
            $position = $row['position'] + 1;
            
            unset($row);
        }
        else
        {
            $position = 0;
        }
        unset($res);
        
        $flags = 0;
        
        if ($style)
        {
            $flags |= WCF_FLAGS_CLASSES_LAYOUT;
        }
        
        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `wcf_categories` (`name`, `flags`, `position`) VALUES (:title, :flags, :pos);");
        $insert->bindParam(':title', $name, PDO::PARAM_STR);
        $insert->bindParam(':flags', $flags, PDO::PARAM_INT);
        $insert->bindParam(':pos', $position, PDO::PARAM_INT);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the category record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/forums/categories');
        exit;
    }

    public function submit_edit_category()
    {
        $this->CheckPermissionSilent(PERMISSION_FORUM_CATS);

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $style = (isset($_POST['style']) ? (int)$_POST['style'] : false);

        if (!$id)
        {
            echo 'Category id is missing.';
            die;
        }

        if (!$name)
        {
            echo 'Please enter category title.';
            die;
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT `id`, `name`, `flags` FROM `wcf_categories` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo 'The category record is missing.';
            die;
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        //force int
        $newFlags = $row['flags'] = intval($row['flags']);
        
        if (!$style)
        {
            //remove the classes style flags
            if ($row['flags'] & WCF_FLAGS_CLASSES_LAYOUT)
                $newFlags &= ~WCF_FLAGS_CLASSES_LAYOUT;
        }
        else
        {
            if (!($row['flags'] & WCF_FLAGS_CLASSES_LAYOUT))
                $newFlags |= WCF_FLAGS_CLASSES_LAYOUT;
        }
        
        //Check if we need an update
        if ($name == $row['name'] && $row['flags'] == $newFlags)
        {
            echo 'SKIP';
            die;
        }
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `wcf_categories` SET `name` = :name, `flags` = :flags WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':name', $name, PDO::PARAM_STR);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->bindParam(':flags', $newFlags, PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            echo 'The website failed to update the category record.';
            die;
        }
        else
        {
            echo 'OK';
        }
        exit;
    }

    public function delete_category()
    {
        $this->CheckPermission(PERMISSION_FORUM_CATS);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);
        
        //prepare multi errors
        $this->errors->NewInstance('forums_delcat');
        
        //bind on success
        $this->errors->onSuccess('The category was successfully deleted.', '/admin/forums/categories');
        
        if (!$id)
        {
            $this->errors->Add("The category id is missing.");
        }

        $this->errors->Check('/admin/forums/categories');
        
        $delete = $this->db->prepare('DELETE FROM `wcf_categories` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the category record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/forums/categories');
        exit;
    }

    public function submit_category_order()
    {
        $this->CheckPermissionSilent(PERMISSION_FORUM_CATS);

        $order = (isset($_POST['order']) ? $_POST['order'] : false);

        if (!$order)
        {
            echo 'The order list is missing.';
            die;
        }

        foreach ($order as $position => $id)
        {
            //insert the news record
            $update = $this->db->prepare("UPDATE `wcf_categories` SET `position` = :pos WHERE `id` = :id LIMIT 1;");
            $update->bindParam(':pos', $position, PDO::PARAM_INT);
            $update->bindParam(':id', $id, PDO::PARAM_INT);
            $update->execute();
            unset($update);
        }

        echo 'OK';
        exit;
    }
}