<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Pcodes extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_PROMO_CODES);
    }

    public function index()
    {
        //Print the page
        $this->PrintPage('pcodes');
    }

    public function submit()
    {
        //prepare multi errors
        $this->errors->NewInstance('pcode_add');

        $format = (isset($_POST['format']) ? $_POST['format'] : 'XXXX-XXXX-XXXX');
        $usage = (isset($_POST['usage']) ? (int)$_POST['usage'] : 0);
        $reward_type = (isset($_POST['reward_type']) ? (int)$_POST['reward_type'] : false);
        $reward_value = (isset($_POST['reward_value']) ? (int)$_POST['reward_value'] : false);

        if (strlen($format) == 0)
        {
            $this->errors->Add("Please enter format.");
        }

        if ($reward_type === false || $reward_type == 0)
        {
            $this->errors->Add("Please select code reward type.");
        }
        else
        {
            //Validate the reward type
            $types = array(PCODE_REWARD_CURRENCY_S, PCODE_REWARD_CURRENCY_G, PCODE_REWARD_ITEM);
            
            if (!in_array($reward_type, $types))
            {
                $this->errors->Add('The selected reward type is invalid.');
            }
        }

        if ($reward_value === false || $reward_value == 0)
        {
            $this->errors->Add("Please enter code reward value.");
        }


        $this->errors->Check('/admin/pcodes');

        //Load the Tokens lib
        $this->loadLibrary('promo.codes');

        //Setup new Promo Code Generator
        $PCodeGen = new PromoCodeGen();

        //Setup the reward
        $PCodeGen->setRewardType($reward_type)->setRewardValue($reward_value);

        //Register the key and format it
        if ($key = $PCodeGen->setUsage($usage)->format($format)->Generate()->get())
        {
            //bind on success
            $this->errors->onSuccess('The promo code "'.$key.'" was successfully added.', '/admin/pcodes');
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add($PCodeGen->getLastError());
        }

        $this->errors->Check('/admin/pcodes');
        exit;
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('pcode_delete');
        
        //bind on success
        $this->errors->onSuccess('The code was successfully deleted.', '/admin/pcodes');
        
        if (!$id)
        {
            $this->errors->Add("The promo code id is missing.");
        }

        $this->errors->Check('/admin/pcodes');
        
        $delete = $this->db->prepare('DELETE FROM `promo_codes` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the promo code.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/pcodes');
        exit;
    }
}