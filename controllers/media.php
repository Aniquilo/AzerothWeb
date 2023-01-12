<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Media extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->tpl->SetTitle('Media');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/media');

        $this->tpl->AddFooterJs('template/js/shadowbox.js');
	    $this->tpl->AddFooterJs('template/js/init.custom.shadowbox.js');
        $this->tpl->LoadFooter();
    }

    public function videos()
    {
        $this->tpl->SetTitle('Videos');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/videos');

        $this->tpl->LoadFooter();
    }

    public function video()
    {
        $videoId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if (!$videoId)
        {
            $this->tpl->Message('Media', 'An error occured!', 'No video id specified.');
        }

        $res = $this->db->prepare("SELECT * FROM `videos` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $videoId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->tpl->Message('Media', 'An error occured!', 'Video not found.');
        }

        //fetch
        $row = $res->fetch();
                    
        $this->tpl->SetTitle('View Video');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/video', array(
            'videoId' => $videoId,
            'row' => $row
        ));

        $this->tpl->LoadFooter();
    }

    public function wallpapers()
    {
        $this->tpl->SetTitle('Wallpapers');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/wallpapers');

        $this->tpl->LoadFooter();
    }

    public function screenshots()
    {
        $this->tpl->SetTitle('Screenshots');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/screenshots');

        $this->tpl->AddFooterJs('template/js/shadowbox.js');
        $this->tpl->AddFooterJs('template/js/init.custom.shadowbox.js');
        $this->tpl->LoadFooter();
    }

    public function upload_screanshot()
    {
        $this->loggedInOrReturn();

        $this->tpl->SetTitle('Upload Screenshot');
        $this->tpl->SetSubtitle('Media');
        $this->tpl->AddCSS('template/style/page-media.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('media/upload_screenshot');

        $this->tpl->AddFooterJs('template/js/jQuery.fileinput.js');
        $this->tpl->LoadFooter();
    }

    public function upload_submit()
    {
        $this->loggedInOrReturn();

        $this->loadLibrary('img.manipulation');

        //prepare multi errors
        $this->errors->NewInstance('screenshots');

        //bind the onsuccess message
        $this->errors->onSuccess('The screenshot was successfully uploaded.', '/media/upload_screanshot');

        //information
        $file_path = ROOTPATH . '/uploads/media/screenshots';
        $thumb_folder = '/thumbs';

        //thumb size
        $thumb_width = 200;
        $thumb_height = 114;

        //post data
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $descr = isset($_POST['descr']) ? $_POST['descr'] : false;

        //allowed types
        $allowedTypes = array('image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png', 'image/gif');

        //do we have an title
        if (!$title)
        {
            $this->errors->Add("Please fill in the title field.");
        }

        //check if we've got any image at all
        if (!isset($_FILES['file']))
        {
            $this->errors->Add("Please select an screenshot to upload.");
        }

        //do we have description
        if (!$descr)
        {
            $this->errors->Add("Please fill in the description field.");
        }
            
        //temp file
        $tempFile = $_FILES['file']['tmp_name'];	

        //get the image type
        $imageType = exif_imagetype($tempFile);

        //get the mime type
        $mime = image_type_to_mime_type($imageType);

        //if mime type is not allowed, return error
        if (!in_array($mime, $allowedTypes))
        {
            $this->errors->Add("File Type not allowed.");
        }
        else if (!in_array($_FILES['file']['type'], $allowedTypes))
        {
            $this->errors->Add("File Type not allowed.");
        }

        //check for errors
        $this->errors->Check('/media/upload_screanshot');

        //get some info about the file name
        $fileInfo = pathinfo($_FILES['file']['name']);

        //get the image name
        $imageName = $fileInfo['filename'];

        //replace white spaces
        $imageName = str_replace(' ', '_', $imageName);

        //replace any PHP extension
        $imageName = str_replace(array('php', 'php3', 'php4', 'php5', 'phtml'), '', $imageName);
        
        //add timestamp to the image name
        $imageName = $imageName . '_' . time();

        //appply the extension of the image
        switch ($mime)
        {
            case 'image/jpeg':
                $imageName .= '.jpg';
                break;
            case 'image/pjpeg':
                $imageName .= '.jpg';
                break;
            case 'image/jpg':
                $imageName .= '.jpg';
                break;
            case 'image/png':
                $imageName .= '.png';
                break;
            case 'image/gif':
                $imageName .= '.gif';
                break;
            default:
                $imageName .= '.jpg';
                break;
        }
            
        //apply the file path
        $file_src_new = $file_path . '/' . $imageName;

        //thumb
        $file_src_new_thumb = $file_path . $thumb_folder . '/' . $imageName;

        //Chmod the folder
        //$this->ChmodWritable($file_path);
        //$this->ChmodWritable($file_path . $thumb_folder);

        //handle the upload
        if (move_uploaded_file($tempFile, $file_src_new))
        {
            //try deleting the temp file
            @unlink($tempFile);
        }
        else
        {
            $this->errors->Add("The website failed to upload your screenshot. If this problem presists please contact the administration.");
        }

        //resample the image
        $objImage = new ImageManipulation($file_src_new);

        if ($objImage->imageok)
        {
            //resample the image
            $objImage->setJpegQuality(100);
            $objImage->save($file_src_new);
            //make a thumb
            $objImage->resizeProper($thumb_width, $thumb_height);
            $objImage->save($file_src_new_thumb);
            
            //get the time
            $time = $this->getTime();
            $type = TYPE_SCREENSHOT;
            $status = SCREENSHOT_STATUS_PENDING;
            $userId = $this->user->get('id');

            //insert into the database
            $insert = $this->db->prepare("INSERT INTO `images` (`name`, `descr`, `added`, `account`, `image`, `type`, `status`) VALUES (:name, :descr, :added, :account, :image, :type, :status);");
            $insert->bindParam(':name', $title, PDO::PARAM_STR);
            $insert->bindParam(':descr', $descr, PDO::PARAM_STR);
            $insert->bindParam(':added', $time, PDO::PARAM_STR);
            $insert->bindParam(':account', $userId, PDO::PARAM_INT);
            $insert->bindParam(':image', $imageName, PDO::PARAM_STR);
            $insert->bindParam(':type', $type, PDO::PARAM_INT);
            $insert->bindParam(':status', $status, PDO::PARAM_INT);
            $insert->execute();
            
            if ($insert->rowCount() == 0)
            {
                $this->errors->Add("The website failed to save your screenshot. If this problem presists please contact the administration.");
            }
            
            unset($insert);
            unset($objImage);
        }
        else
        {
            $this->errors->Add("The image file is invalid. If this problem presists please contact the administration.");
        }

        //Chmod the folder back to normal
        //$this->ChmodReadonly($file_path);
        //$this->ChmodReadonly($file_path . $thumb_folder);

        //check for errors
        $this->errors->Check('/upload_screanshot');

        //screenshot upload successfull
        $this->errors->triggerSuccess();
        exit;
    }
}