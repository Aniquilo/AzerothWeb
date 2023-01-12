<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Media extends Admin_Controller
{
    const SCREENSHOT_SILVER_REWARD = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->CheckPermission(PERMISSION_MEDIA_VIDEOS);

        //Print the page
        $this->PrintPage('media/media');
    }

    public function add_video()
    {
        $this->CheckPermission(PERMISSION_MEDIA_VIDEOS);

        //Print the page
        $this->PrintPage('media/add_video');
    }

    public function submit_video()
    {
        $this->CheckPermission(PERMISSION_MEDIA_VIDEOS);

        $this->loadLibrary('img.manipulation');

        //prepare multi errors
        $this->errors->NewInstance('add_video');

        //bind on success
        $this->errors->onSuccess('The video was successfully added.', '/admin/media');

        $title = (isset($_POST['name']) ? $_POST['name'] : false);
        $youtube = (isset($_POST['youtube']) ? $_POST['youtube'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);
        $embed_code = (isset($_POST['embed_code']) ? $_POST['embed_code'] : false);
        $short_desc = (isset($_POST['short_desc']) ? $_POST['short_desc'] : false);
        $image = (isset($_POST['image']) ? $_POST['image'] : false);

        if (!$title)
        {
            $this->errors->Add("Please enter video title.");
        }

        if (!$youtube)
        {
            $this->errors->Add("Please enter the Youtube Link.");
        }
        
        if (!$text)
        {
            $this->errors->Add("Please enter video description.");
        }

        $this->errors->Check('/admin/media/add_video');

        // Resolve video id
        $videoId = false;

        if (preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $youtube, $matches))
        {
            $videoId = $matches[1];
        }

        if (!$videoId)
        {
            $this->errors->Add("Invalid Youtube Link.");
        }

        $this->errors->Check('/admin/media/add_video');

        if (!$embed_code && $videoId)
        {
            $embed_code = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$videoId.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }

        $uniqer = substr(md5(uniqid(rand(), 1)), 0, 5);
        //Define the temp upload folder
        $tempFolder = ROOTPATH . '/uploads/temp';
        
        // Download youtube thumbnail if required
        if (!$image || $image == '')
        {
            $content = file_get_contents('https://img.youtube.com/vi/'.$videoId.'/maxresdefault.jpg');

            if ($content === false)
            {
                $content = file_get_contents('https://img.youtube.com/vi/'.$videoId.'/hqdefault.jpg');
            }

            if ($content === false)
            {
                $content = file_get_contents('https://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg');
            }

            if ($content === false)
            {
                $content = file_get_contents('https://img.youtube.com/vi/'.$videoId.'/sddefault.jpg');
            }

            if ($content !== false)
            {
                $image = $uniqer . '.jpg';
                file_put_contents($tempFolder . '/' . $image, $content);
            }
        }

        //Let's start by creating a folder for the video
        $videoFolder = preg_replace('/[^A-Za-z0-9-_]/', '', $title) . '_' . $uniqer;
        $DirName = $videoFolder;
        
        //append the full path
        $videoFolder = ROOTPATH . '/uploads/media/videos/' . $videoFolder;
        
        //Create the video directory
        if (!mkdir($videoFolder, 0755, true))
        {
            $this->errors->Add("The website was not able to create new directory for the video.");
        }
        
        $ImageFolder = $videoFolder . '/thumbnails';
        //Create a folder for the images aswell
        mkdir($ImageFolder, 0755, true);
        
        //Let's start creating diferent size thumbs
        $objImage = new ImageManipulation($tempFolder . '/' . $image);
        
        //Verify the image
        if ($objImage->imageok)
        {
            $objImage->setJpegQuality(100);
            
            //Start by making the default size image, no resize
            $objImage->save($ImageFolder . '/' . $image);
            
            //Index image 401x227
            $objImage->resize(401);
            $objImage->save($ImageFolder . '/index_' . $image);
            
            //Medium image 255x145
            $objImage->resize(255);
            $objImage->save($ImageFolder . '/medium_' . $image);
            
            //Small image 200x113
            $objImage->resize(200);
            $objImage->save($ImageFolder . '/small_' . $image);
            
            //delete the temp
            @unlink($tempFolder . '/' . $image);
        }
        else
        {
            $this->errors->Add("The uploaded thumbnail seems to be invalid.");
        }
        unset($objImage);
        
        $this->errors->Check('/admin/media/add_video');

        $time = $this->getTime();
        $userId = $this->user->get('id');

        //insert the video record
        $insert = $this->db->prepare("INSERT INTO `videos` (`name`, `descr`, `short_desc`, `embed_code`, `added`, `account`, `dirname`, `image`, `youtube`, `status`) VALUES (:title, :text, :short_desc, :embed_code, :added, :acc, :dirname, :image, :youtube, '1');");
        $insert->bindParam(':title', $title, PDO::PARAM_STR);
        $insert->bindParam(':text', $text, PDO::PARAM_STR);
        $insert->bindParam(':short_desc', $short_desc, PDO::PARAM_STR);
        $insert->bindParam(':embed_code', $embed_code, PDO::PARAM_STR);
        $insert->bindParam(':added', $time, PDO::PARAM_STR);
        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
        $insert->bindParam(':dirname', $DirName, PDO::PARAM_STR);
        $insert->bindParam(':image', $image, PDO::PARAM_STR);
        $insert->bindParam(':youtube', $youtube, PDO::PARAM_STR);
        $insert->execute();
        
        if ($insert->rowCount() < 1)
        {
            $this->errors->Add("The website failed to insert the video record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/media/add_video');
        exit;
    }

    public function delete_video()
    {
        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //check for permissions
        $this->CheckPermission(PERMISSION_MEDIA_VIDEOS);
        
        //prepare multi errors
        $this->errors->NewInstance('delete_video');

        //bind on success
        $this->errors->onSuccess('The video was successfully deleted.', '/admin/media');
        
        if (!$id)
        {
            $this->errors->Add("The video id is missing.");
        }
        
        //check if the news record exists
        $res = $this->db->prepare("SELECT `id`, `dirname` FROM `videos` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The video record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);
        
        $this->errors->Check('/admin/media');
        
        //delete the whole video folder
        $folder = ROOTPATH . '/uploads/media/videos/' . $row['dirname'];
        
        //Delete the record
        $delete = $this->db->prepare('DELETE FROM `videos` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the video record.");
        }
        else
        {
            //delete the folder
            $this->RecursiveRemoveDirectory($folder);
            
            //redirect
            $this->errors->triggerSuccess();
        }
        
        $this->errors->Check('/admin/media');
        exit;
    }

    public function screenshots()
    {
        $this->CheckPermission(PERMISSION_MEDIA_SREENSHOTS);

        //Print the page
        $this->PrintPage('media/screenshots');
    }

    public function screenshot_approve()
    {
        $this->CheckPermission(PERMISSION_MEDIA_SREENSHOTS);

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        if (!$id)
        {
            echo 'Screenshot id is missing.';
            die;
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT * FROM `images` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo 'The screenshot record is missing.';
            die;
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        //check if the screenshot is already approved
        if ($row['status'] == SCREENSHOT_STATUS_APPROVED)
        {
            echo 'This screenshot is already approved.';
            die;
        }

        //define the approve type
        $status = SCREENSHOT_STATUS_APPROVED;
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `images` SET `status` = :status WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':status', $status, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() == 0)
        {
            echo 'The website failed to update the screenshot record.';
            die;
        }
        else
        {
            $silverReward = self::SCREENSHOT_SILVER_REWARD;

            //reward the user for approved screenshot
            $accUpdate = $this->db->prepare("UPDATE `account_data` SET `silver` = silver + :reward WHERE `id` = :id LIMIT 1;");
            $accUpdate->bindParam(':reward', $silverReward, PDO::PARAM_INT);
            $accUpdate->bindParam(':id', $row['account'], PDO::PARAM_INT);
            $accUpdate->execute();
            
            //check if the reward was delivered
            if ($accUpdate->rowCount() > 0)
            {
                $this->loadLibrary('coin.activity');

                //log into coin activity
                $ca = new CoinActivity($row['account']);
                $ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
                $ca->set_SourceString('Approved Screenshot');
                $ca->set_CoinsType(CA_COIN_TYPE_SILVER);
                $ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
                $ca->set_Amount($silverReward);
                $ca->execute();
                unset($ca);
                
                //success
                echo 'OK';
            }
            else
            {
                echo 'The website failed to deliver the reward to the user.';
            }
        }
        exit;
    }

    public function screenshot_deny()
    {
        $this->CheckPermission(PERMISSION_MEDIA_SREENSHOTS);

        $id = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        if (!$id)
        {
            echo 'Screenshot id is missing.';
            die;
        }

        //check if the news record exists
        $res = $this->db->prepare("SELECT * FROM `images` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo 'The screenshot record is missing.';
            die;
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        //check if the screenshot is already approved
        if ($row['status'] == SCREENSHOT_STATUS_DENIED)
        {
            echo 'This screenshot is already denied.';
            die;
        }
  
        //define the approve type
        $status = SCREENSHOT_STATUS_DENIED;
        
        //insert the news record
        $update = $this->db->prepare("UPDATE `images` SET `status` = :status WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':status', $status, PDO::PARAM_INT);
        $update->bindParam(':id', $row['id'], PDO::PARAM_INT);
        $update->execute();
        
        if ($update->rowCount() == 0)
        {
            echo 'The website failed to update the screenshot record.';
            die;
        }
        else
        {
            //success
            echo 'OK';
        }
        exit;
    }

    public function screenshot_delete()
    {
        $this->CheckPermission(PERMISSION_MEDIA_SREENSHOTS);

        $id = (isset($_GET['id']) ? (int)$_GET['id'] : false);

        //prepare multi errors
        $this->errors->NewInstance('deleteScreenshot');

        //bind on success
        $this->errors->onSuccess('The screenshot was successfully deleted.', '/admin/media/screenshots');
        
        if (!$id)
        {
            $this->errors->Add("The screenshot id is missing.");
        }
        //check if the news record exists
        $res = $this->db->prepare("SELECT id, image FROM `images` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            $this->errors->Add("The screenshot record is missing.");
        }
        else
        {
            $row = $res->fetch();
        }
        unset($res);

        $this->errors->Check('/admin/media/screenshots');
        
        //delete the news image
        $folder = ROOTPATH . '/uploads/media/screenshots';

        //Chmod the folder
        //$this->ChmodWritable($folder);

        //delete the image if it's not default
        if (file_exists($folder . '/'. $row['image']))
        {
            unlink($folder . '/'. $row['image']);
        }

        //Chmod the folder back to normal
        //$this->ChmodReadonly($folder);
        
        $delete = $this->db->prepare('DELETE FROM `images` WHERE `id` = :id LIMIT 1;');
        $delete->bindParam(':id', $id, PDO::PARAM_INT);
        $delete->execute();
        
        if ($delete->rowCount() < 1)
        {
            $this->errors->Add("The website failed to delete the screenshot record.");
        }
        else
        {
            $this->errors->triggerSuccess();
        }
            
        $this->errors->Check('/admin/media/screenshots');
        exit;
    }
}