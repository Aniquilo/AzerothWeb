<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class News extends Admin_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->CheckPermission(PERMISSION_NEWS);
    }
    
    public function index()
    {
        //Print the page
        $this->PrintPage('news/news');
    }

    public function post()
    {
        //Print the page
        $this->PrintPage('news/post');
    }

    public function submit_post()
    {
        //prepare multi errors
        $this->errors->NewInstance('addNews');

        //bind on success
        $this->errors->onSuccess('The news ware successfully posted.', '/admin/news');

        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $shortText = (isset($_POST['shortText']) ? $_POST['shortText'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);
        $image = (isset($_POST['image']) ? $_POST['image'] : false);

        if (!$title)
        {
            $this->errors->Add("Please enter news headline.");
        }

        if (!$shortText)
        {
            $this->errors->Add("Please enter news short text.");
        }

        if (!$text)
        {
            $this->errors->Add("Please enter news content.");
        }

        $this->errors->Check('/admin/news/post');

        //check if we got icon uploaded
        if (!$image or $image == '')
        {
            $image = NULL;
        }
        else
        {
            //try moving the icon
            $tempFolder = ROOTPATH . '/uploads/temp';
            $moveFolder = ROOTPATH . '/uploads/news/thumbs';
            //Chmod the folder
            //$this->ChmodWritable($moveFolder);
            //move the thumb image, if fail set default
            if (!rename($tempFolder. '/' .$image, $moveFolder. '/' .$image))
            {
                $image = NULL;
            }
            //Chmod the folder back to normal
            //$this->ChmodReadonly($moveFolder);
        }

        $addedTime = $this->getTime();
        $userId = $this->user->get('id');
        $userDisplayname = $this->user->get('displayName');

        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `news` (`title`, `shortText`, `text`, `image`, `added`, `author`, `authorStr`) VALUES (:title, :short, :text, :image, :added, :author, :authorStr);");
        $insert->bindParam(':title', $title, PDO::PARAM_STR);
        $insert->bindParam(':short', $shortText, PDO::PARAM_STR);
        $insert->bindParam(':text', $text, PDO::PARAM_STR);
        $insert->bindParam(':image', $image, PDO::PARAM_STR);
        $insert->bindParam(':added', $addedTime, PDO::PARAM_STR);
        $insert->bindParam(':author', $userId, PDO::PARAM_INT);
        $insert->bindParam(':authorStr', $userDisplayname, PDO::PARAM_STR);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the news record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/news/post');
        exit;
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Check if we have an ID
        if (!$id)
        {
            $this->ErrorBox('The news id is missing.');
        }

        //lookup the record
        $res = $this->db->prepare("SELECT * FROM `news` WHERE `id` = :id LIMIT 1");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        //verify that we found the record
        if ($res->rowCount() == 0)
        {
            $this->ErrorBox('The news record is invalid or missing.');
        }

        $row = $res->fetch();

        //Print the page
        $this->PrintPage('news/edit', array(
            'id' => $id,
            'row' => $row
        ));
    }

    public function submit_edit()
    {
        //prepare multi errors
        $this->errors->NewInstance('editNews');

        //bind on success
        $this->errors->onSuccess('The news ware successfully edited.', '/admin/news');

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $shortText = (isset($_POST['shortText']) ? $_POST['shortText'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);
        $image = (isset($_POST['image']) ? $_POST['image'] : false);

        if (!$id)
        {
            $this->errors->Add("The news id is missing.");
        }

        if (!$title)
        {
            $this->errors->Add("Please enter news headline.");
        }

        if (!$shortText)
        {
            $this->errors->Add("Please enter news short text.");
        }

        if (!$text)
        {
            $this->errors->Add("Please enter news content.");
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, image FROM `news` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The news record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/news/edit?id='.$id);

        //check if we got icon uploaded
        if (!$image or $image == '')
        {
            $image = $row['image'];
        }
        else if ($row['image'] != $image)
        {
            //try moving the icon
            $tempFolder = ROOTPATH . '/uploads/temp';
            $moveFolder = ROOTPATH . '/uploads/news/thumbs';
            
            //Chmod the folder
            //$this->ChmodWritable($moveFolder);
            //move the thumb image, if fail set default
            if (!rename($tempFolder. '/' .$image, $moveFolder. '/' .$image))
            {
                $image = NULL;
            }
            //Chmod the folder back to normal
            //$this->ChmodReadonly($moveFolder);
        }
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `news` SET `title` = :title, `shortText` = :short, `text` = :text, `image` = :image WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':title', $title, PDO::PARAM_STR);
        $update->bindParam(':short', $shortText, PDO::PARAM_STR);
        $update->bindParam(':text', $text, PDO::PARAM_STR);
        $update->bindParam(':image', $image, PDO::PARAM_STR);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the news record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        unset($insert);
        
        $this->errors->Check('/admin/news/edit?id='.$id);
        exit;
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('deleteNews');

        //bind on success
        $this->errors->onSuccess('The news ware successfully deleted.', '/admin/news');
        
        if (!$id)
        {
            $this->errors->Add("The news id is missing.");
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, image FROM `news` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The news record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/news');
	
		//delete the news image
        $folder = ROOTPATH . '/uploads/news/thumbs';
        
		//Chmod the folder
        //$this->ChmodWritable($folder);
        
		//delete the image if it's not default
		if (file_exists($folder . '/'. $row['image']) and $row['image'] != 'default.jpg')
		{
			unlink($folder . '/'. $row['image']);
        }
        
		//Chmod the folder back to normal
		//$this->ChmodReadonly($folder);
		
		$delete = $this->db->prepare('DELETE FROM `news` WHERE `id` = :id LIMIT 1;');
		$delete->bindParam(':id', $id, PDO::PARAM_INT);
		$delete->execute();
		
		if ($delete->rowCount() < 1)
		{
			$this->errors->Add("The website failed to delete the news record.");
		}
		else
		{
			$this->errors->triggerSuccess();
		}
		
        $this->errors->Check('/admin/news');
        exit;
    }
}