<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Store extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_STORE);
    }

    public function index()
    {
        $RealmID = isset($_GET['realm']) ? (int)$_GET['realm'] : '-1';

        // Validate realm id
        if ($RealmID != '-1')
        {
            $RealmID = ($this->realms->realmExists($RealmID)) ? $RealmID : '-1';
        }

        //Print the page
        $this->PrintPage('store/store', array(
            'RealmID' => $RealmID
        ));
    }

    public function add()
    {
        //Print the page
        $this->PrintPage('store/add');
    }

    public function submit_add()
    {
        //prepare multi errors
        $this->errors->NewInstance('add_storeitem');

        //bind on success
        $this->errors->onSuccess('The item was successfully added.', '/admin/store');

        $entry = (isset($_POST['entry']) ? (int)$_POST['entry'] : false);
        $realms = (isset($_POST['realms']) ? $_POST['realms'] : false);
        $gold = (isset($_POST['gold']) ? (int)$_POST['gold'] : false);
        $silver = (isset($_POST['silver']) ? (int)$_POST['silver'] : false);
        $custom = (isset($_POST['custom']) ? (int)$_POST['custom'] == 1 : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $quality = (isset($_POST['quality']) ? (int)$_POST['quality'] : false);
        $class = (isset($_POST['class']) ? (int)$_POST['class'] : 0);
        $subclass = (isset($_POST['subclass']) ? (int)$_POST['subclass'] : 0);
        $itemlevel = (isset($_POST['itemlevel']) ? (int)$_POST['itemlevel'] : 0);
        $requiredlevel = (isset($_POST['requiredlevel']) ? (int)$_POST['requiredlevel'] : 0);

        if (!$entry)
        {
            $this->errors->Add("Please enter item entry.");
        }
        if (!$realms || empty($realms))
        {
            $this->errors->Add("Please select realms in which the item will be purchasable.");
        }
        if ($gold === false)
        {
            $this->errors->Add("Please enter item price in gold.");
        }
        if ($silver === false)
        {
            $this->errors->Add("Please enter item price in silver.");
        }

        if ($custom && !$name)
        {
            $this->errors->Add("Please enter item name.");
        }
        if ($custom && $quality === false)
        {
            $this->errors->Add("Please select select item quality.");
        }
        
        $this->errors->Check('/admin/store/add');

        // Get item info
        if (!$custom)
        {
            $realmId = (int)$realms[0];

            $itemInfo = $this->realms->getRealm($realmId)->getIteminfo()->getInfo($entry);

            if ($itemInfo !== false)
            {
                $name = $itemInfo['name'];
                $quality = (int)$itemInfo['quality'];
                $class = (int)$itemInfo['class'];
                $subclass = $itemInfo['subclass'];
                $itemlevel = (int)$itemInfo['itemlevel'];
                $requiredlevel = (int)$itemInfo['reqlevel'];
            }
        }

        // Realms string
        $realmsString = implode(',', $realms);

        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `store_items` (`entry`, `realm`, `name`, `gold`, `silver`, `class`, `subclass`, `ItemLevel`, `Quality`, `RequiredLevel`) VALUES (:entry, :realms, :name, :gold, :silver, :class, :subclass, :itemlevel, :quality, :requiredlevel);");
        $insert->bindParam(':entry', $entry, PDO::PARAM_INT);
        $insert->bindParam(':realms', $realmsString, PDO::PARAM_STR);
        $insert->bindParam(':name', $name, PDO::PARAM_STR);
        $insert->bindParam(':gold', $gold, PDO::PARAM_INT);
        $insert->bindParam(':silver', $silver, PDO::PARAM_INT);
        $insert->bindParam(':class', $class, PDO::PARAM_INT);
        $insert->bindParam(':subclass', $subclass, PDO::PARAM_INT);
        $insert->bindParam(':itemlevel', $itemlevel, PDO::PARAM_INT);
        $insert->bindParam(':quality', $quality, PDO::PARAM_INT);
        $insert->bindParam(':requiredlevel', $requiredlevel, PDO::PARAM_INT);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the store item record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/store/add');
        exit;
    }

    public function submit_edit()
    {
        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        $entry = (isset($_POST['entry']) ? (int)$_POST['entry'] : false);
        $name = (isset($_POST['name']) ? $_POST['name'] : false);
        $realms = (isset($_POST['realms']) ? $_POST['realms'] : false);
        $gold = (isset($_POST['gold']) ? (int)$_POST['gold'] : false);
        $silver = (isset($_POST['silver']) ? (int)$_POST['silver'] : false);

        if (!$id)
        {
            $this->JsonError("The item id is missing.");
        }
        if (!$entry)
        {
            $this->JsonError("Please enter item entry.");
        }
        if (!$name)
        {
            $this->JsonError("Please enter armor set title.");
        }
        if (!$realms || empty($realms))
        {
            $this->JsonError("Please enter item realms.");
        }
        if ($gold === false)
        {
            $this->JsonError("Please enter price in gold.");
        }
        if ($silver === false)
        {
            $this->JsonError("Please enter price in silver.");
        }

        //verify the item
        $res = $this->db->prepare("SELECT `id` FROM `store_items` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->JsonError("The selected item is invalid or missing.");
        }
        unset($res);

        // Realms string
        $realmsString = implode(',', $realms);

        //insert the news record
        $update = $this->db->prepare("UPDATE `store_items` SET `entry` = :entry, `name` = :name, `realm` = :realm, `gold` = :gold, `silver` = :silver WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':name', $name, PDO::PARAM_STR);
        $update->bindParam(':entry', $entry, PDO::PARAM_INT);
        $update->bindParam(':realm', $realmsString, PDO::PARAM_STR);
        $update->bindParam(':gold', $gold, PDO::PARAM_INT);
        $update->bindParam(':silver', $silver, PDO::PARAM_INT);
        $update->bindParam(':id', $id, PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() == 0)
        {
            $this->JsonError("The website failed to update the item record.");
        }

        $this->Json([ 'response' => true, 'message' => 'The item was successfully updated.' ]);
    }

    public function item_data()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($id)
        {
            $res = $this->db->prepare("SELECT * FROM `store_items` WHERE `id` = :id LIMIT 1;");
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

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        if (!$id)
        {
            $this->JsonError('Please select an item first!');
        }

        //Validate the item
        $res = $this->db->prepare("SELECT * FROM `store_items` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->JsonError('The selected item is invalid or missing.');
        }
        unset($res);

        $delete = $this->db->prepare("DELETE FROM `store_items` WHERE `id` = :id LIMIT 1;");
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();

        if ($delete->rowCount() == 0)
        {
            $this->JsonError('The website was unable to delete the item.');
        }

        $this->Json([ 'response' => true ]);
    }
}