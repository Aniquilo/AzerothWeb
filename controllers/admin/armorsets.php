<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Armorsets extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_PSTORE);
    }

    public function index()
    {
        //get the armor set categories
        $categories = array();

        $res = $this->db->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");

        if ($res->rowCount() > 0)
        {
            while ($arr = $res->fetch())
            {
                $categories[$arr['id']] = $arr['name'];
            }
        }
        unset($res);

        //Print the page
        $this->PrintPage('armorsets/armorsets', array(
            'categories' => $categories
        ));
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('pstore_armorsets_add');

        //bind on success
        $this->errors->onSuccess('The armor set was successfully added.', '/admin/armorsets');

        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $realm = (isset($_POST['realm']) ? $_POST['realm'] : false);
        $category = (isset($_POST['category']) ? (int)$_POST['category'] : false);
        $price = (isset($_POST['price']) ? (int)$_POST['price'] : false);
        $class = (isset($_POST['class']) ? (int)$_POST['class'] : false);
        $type = (isset($_POST['type']) ? $_POST['type'] : false);
        $tier = (isset($_POST['tier']) ? $_POST['tier'] : false);
        $items = (isset($_POST['items']) ? $_POST['items'] : false);

        if (!$name)
        {
            $this->errors->Add("Please enter armor set title.");
        }
        if (!$category or $category == 0)
        {
            $this->errors->Add("Please select armor set category.");
        }
        if (!$price)
        {
            $this->errors->Add("Please price for the armor set.");
        }
        if (!$items or $items == '')
        {
            $this->errors->Add("Please place at least one item for the armor set.");
        }

        $this->errors->Check('/admin/armorsets');

        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `armorsets` (`name`, `realm`, `category`, `price`, `tier`, `class`, `type`, `items`) VALUES (:title, :realm, :cat, :price, :tier, :class, :type, :items);");
        $insert->bindParam(':title', $name, PDO::PARAM_STR);
        $insert->bindParam(':realm', $realm, PDO::PARAM_STR);
        $insert->bindParam(':cat', $category, PDO::PARAM_INT);
        $insert->bindParam(':price', $price, PDO::PARAM_INT);
        $insert->bindParam(':tier', $tier, PDO::PARAM_STR);
        $insert->bindParam(':class', $class, PDO::PARAM_INT);
        $insert->bindParam(':type', $type, PDO::PARAM_STR);
        $insert->bindParam(':items', $items, PDO::PARAM_STR);
        $insert->execute();

        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the armor set record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/armorsets');
        exit;
    }

    public function submit_edit()
    {
        //prepare multi errors
        $this->errors->NewInstance('pstore_armorsets_edit');

        //bind on success
        $this->errors->onSuccess('The armor set was successfully edited.', '/admin/armorsets');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $realm = (isset($_POST['realm']) ? $_POST['realm'] : false);
        $category = (isset($_POST['category']) ? (int)$_POST['category'] : false);
        $price = (isset($_POST['price']) ? (int)$_POST['price'] : false);
        $class = (isset($_POST['class']) ? (int)$_POST['class'] : false);
        $type = (isset($_POST['type']) ? $_POST['type'] : false);
        $tier = (isset($_POST['tier']) ? $_POST['tier'] : false);
        $items = (isset($_POST['items']) ? $_POST['items'] : false);

        if (!$id)
        {
            $this->errors->Add("The armor set id is missing.");
        }
        if (!$name)
        {
            $this->errors->Add("Please enter armor set title.");
        }
        if (!$category or $category == 0)
        {
            $this->errors->Add("Please select armor set category.");
        }
        if (!$price)
        {
            $this->errors->Add("Please price for the armor set.");
        }
        if (!$items or $items == '')
        {
            $this->errors->Add("Please place at least one item for the armor set.");
        }

        $this->errors->Check('/admin/armorsets');

        //insert the news record
        $update = $this->db->prepare("UPDATE `armorsets` SET `name` = :title, `realm` = :realm, `category` = :cat, `price` = :price, `tier` = :tier, `class` = :class, `type` = :type, `items` = :items WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':title', $name, PDO::PARAM_STR);
        $update->bindParam(':realm', $realm, PDO::PARAM_STR);
        $update->bindParam(':cat', $category, PDO::PARAM_INT);
        $update->bindParam(':price', $price, PDO::PARAM_INT);
        $update->bindParam(':tier', $tier, PDO::PARAM_STR);
        $update->bindParam(':class', $class, PDO::PARAM_INT);
        $update->bindParam(':type', $type, PDO::PARAM_STR);
        $update->bindParam(':items', $items, PDO::PARAM_STR);
        $update->bindParam(':id', $id, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the armor set record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/armorsets');
        exit;
    }
    
    public function get_data()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($id)
        {
            $res = $this->db->prepare("SELECT * FROM `armorsets` WHERE `id` = :id LIMIT 1;");
            $res->bindParam(':id', $id, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $data = $res->fetch();
            }
            else
            {
                $this->JsonError('No record was found.');
            }
            unset($res);
        }
        else
        {
            $this->JsonError('Missing id.');
        }

        $this->Json($data);
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('pstore_armorsets_del');

        //bind on success
        $this->errors->onSuccess('The armor set was successfully deleted.', '/admin/armorsets');

        if (!$id)
        {
            $this->errors->Add("The armor set id is missing.");
        }

        $this->errors->Check('/admin/armorsets');

        $delete = $this->db->prepare('DELETE FROM `armorsets` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();

        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the armor set record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/armorsets');
        exit;
    }

    public function categories()
    {
        //Print the page
        $this->PrintPage('armorsets/categories');
    }

    public function submit_category()
    {
        //prepare multi errors
        $this->errors->NewInstance('pstore_armorsets_addcat');

        //bind on success
        $this->errors->onSuccess('The category was successfully added.', '/admin/armorsets/categories');

        $name = (isset($_POST['name']) ? $_POST['name'] : false);

        if (!$name)
        {
            $this->errors->Add("Please enter category title.");
        }

        $this->errors->Check('/admin/armorsets/categories');

        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `armorset_categories` (`name`) VALUES (:title);");
        $insert->bindParam(':title', $name, PDO::PARAM_STR);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the category record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/armorsets/categories');
        exit;
    }

    public function submit_category_edit()
    {
        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);

        if (!$id)
        {
            $this->JsonError('Category id is missing.');
        }

        if (!$name)
        {
            $this->JsonError('Please enter category title.');
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, name FROM `armorset_categories` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->JsonError('The category record is missing.');
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        //insert the news record
        $update = $this->db->prepare("UPDATE `armorset_categories` SET `name` = :name WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':name', $name, PDO::PARAM_STR);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->JsonError('The website failed to update the category record.');
        }
        
        $this->Json([ 'response' => true ]);
    }

    public function delete_category()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('pstore_armorsets_delcat');

        //bind on success
        $this->errors->onSuccess('The category was successfully deleted.', '/admin/armorsets/categories');
        
        if (!$id)
        {
            $this->errors->Add("The category id is missing.");
        }

        $this->errors->Check('/admin/armorsets/categories');
        
        $delete = $this->db->prepare('DELETE FROM `armorset_categories` WHERE `id` = :id LIMIT 1;');
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
        
        $this->errors->Check('/admin/armorsets/categories');
        exit;
    }
}