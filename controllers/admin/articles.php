<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Articles extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_ARTICLES);
    }

    public function index()
    {
        //Print the page
        $this->PrintPage('articles/articles');
    }

    public function post()
    {
        //Print the page
        $this->PrintPage('articles/post');
    }

    public function submit_post()
    {
        //prepare multi errors
        $this->errors->NewInstance('addArticle');

        //bind on success
        $this->errors->onSuccess('The article was successfully added.', '/admin/articles');

        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $shortText = (isset($_POST['short_text']) ? $_POST['short_text'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);
        $comments = (isset($_POST['comments']) ? 1 : 0);
        $image = (isset($_POST['image']) ? $_POST['image'] : NULL);

        if (!$title)
        {
            $this->errors->Add("Please enter article headline.");
        }
        else if (strlen($title) > 250)
        {
            $this->errors->Add("The article headline is too long, 250 characters max.");
        }

        if (!$text)
        {
            $this->errors->Add("Please enter article content.");
        }

        $this->errors->Check('/admin/articles/post');

        //check if we got icon uploaded
        if ($image && $image != NULL)
        {
            //try moving the icon
            $tempFolder = ROOTPATH . '/uploads/temp';
            $moveFolder = ROOTPATH . '/uploads/articles';

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
        
        //Get the time
        $time = $this->getTime();
        $userId = $this->user->get('id');

        //insert the news record
        $insert = $this->db->prepare("INSERT INTO `articles` (`title`, `short_text`, `text`, `comments`, `added`, `author`, `image`) VALUES (:title, :short_text, :text, :comments, :time, :acc, :image);");
        $insert->bindParam(':title', $title, PDO::PARAM_STR);
        $insert->bindParam(':short_text', $shortText, PDO::PARAM_STR);
        $insert->bindParam(':text', $text, PDO::PARAM_STR);
        $insert->bindParam(':comments', $comments, PDO::PARAM_INT);
        $insert->bindParam(':time', $time, PDO::PARAM_STR);
        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
        $insert->bindParam(':image', $image, PDO::PARAM_STR);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the article record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/articles/post');
        exit;
    }

    public function edit()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Verify the ID
        if (!$id)
        {
            $this->ErrorBox('Missing article id.');
        }

        //Try getting the record
        $res = $this->db->prepare("SELECT * FROM `articles` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->ErrorBox('Invalid article id.');
        }

        //fetch
        $row = $res->fetch();

        //Print the page
        $this->PrintPage('articles/edit', array(
            'id' => $id,
            'row' => $row
        ));
    }

    public function submit_edit()
    {
        //prepare multi errors
        $this->errors->NewInstance('editArticle');

        //bind on success
        $this->errors->onSuccess('The article was successfully edited.', '/admin/articles');

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $shortText = (isset($_POST['short_text']) ? $_POST['short_text'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);
        $image = (isset($_POST['image']) ? $_POST['image'] : NULL);
        $comments = (isset($_POST['comments']) ? '1' : '0');

        if (!$id)
        {
            $this->errors->Add("The article id is missing.");
        }

        if (!$title)
        {
            $this->errors->Add("Please enter article headline.");
        }

        if (!$shortText)
        {
            $this->errors->Add("Please enter article short text.");
        }

        if (!$text)
        {
            $this->errors->Add("Please enter article content.");
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, image FROM `articles` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The article record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/articles/edit?id='.$id.'');

        //check if we got icon uploaded
        if (!$image || $image == '' || $image == NULL)
        {
            $image = $row['image'];
        }
        else if ($row['image'] != $image)
        {
            //try moving the icon
            $tempFolder = ROOTPATH . '/uploads/temp';
            $moveFolder = ROOTPATH . '/uploads/articles';
            
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
        $update = $this->db->prepare("UPDATE `articles` SET `title` = :title, `short_text` = :short, `text` = :text, `image` = :image, `comments` = :comments WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':title', $title, PDO::PARAM_STR);
        $update->bindParam(':short', $shortText, PDO::PARAM_STR);
        $update->bindParam(':text', $text, PDO::PARAM_STR);
        $update->bindParam(':image', $image, PDO::PARAM_STR);
        $update->bindParam(':comments', $comments, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() < 1)
        {
            $this->errors->Add("The website failed to update the article record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/articles/edit?id='.$id.'');
        exit;
    }

    public function delete()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('del_article');

        //bind on success
        $this->errors->onSuccess('The article was successfully deleted.', '/admin/articles');
        
        if (!$id)
        {
            $this->errors->Add("The news id is missing.");
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT id, image FROM `articles` WHERE `id` = :id LIMIT 1;");
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

        $this->errors->Check('/admin/articles');
        
        //delete the article image
        $folder = ROOTPATH . '/uploads/articles';

        //Chmod the folder
        //$this->ChmodWritable($folder);

        //delete the image if it's not default
        if (file_exists($folder . '/'. $row['image']) and $row['image'] != '')
        {
            unlink($folder . '/'. $row['image']);
        }

        //Chmod the folder back to normal
        //$this->ChmodReadonly($folder);
        
        $delete = $this->db->prepare('DELETE FROM `articles` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the article record.");
        }
        else
        {
            unset($delete);
            
            //Delete the comments aswell
            $del = $this->db->prepare("DELETE FROM `article_comments` WHERE `article` = :id;");
            $del->bindParam(':id', $id, PDO::PARAM_INT);
            $del->execute();
            
            //redirect
            $this->errors->triggerSuccess();
        }
        unset($delete);
        
        $this->errors->Check('/admin/articles');
        exit;
    }
}