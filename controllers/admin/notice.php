<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Notice extends Admin_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        //Print the page view
        $this->PrintPage('notice');
    }

    public function save()
    {
        //check for permissions
        $this->CheckPermission(PERMISSION_NEWS);

        //prepare multi errors
        $this->errors->NewInstance('important_notice');

        //bind on success
        $this->errors->onSuccess('The notice configuration was successfully updated.', '/admin/notice');

        $enabled = (isset($_POST['enabled']) && (int)$_POST['enabled'] == 1 ? true : false);
        $message = (isset($_POST['message']) ? trim($_POST['message']) : false);

        if ($message === false)
        {
            $this->errors->Add("Please enter notice message.");
        }

        $this->errors->Check('/admin/notice');

        $this->loadLibrary('file.editor');

        $editor = new FileEditor(ROOTPATH . '/configuration/important_notice.php');

        if ($editor->GetError() !== false)
        {
            $this->errors->Add($editor->GetError());
        }
        else
        {
            $editor->changeConfig('IMPORTANT_NOTICE_ENABLE', $enabled);
            $editor->changeConfig('IMPORTANT_NOTICE_MESSAGE', $message);

            if ($editor->write())
            {
                $this->errors->triggerSuccess();
            }
            else
            {
                $this->errors->Add('Failed to write to file.');
            }
        }

        $this->errors->Check('/admin/notice');
        exit;
    }
}