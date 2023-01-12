<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Ajax extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function test_soap()
    {
        //get the realm
        $realm = $this->realms->getFirstRealm();

        //prepare commands class
        $command = $realm->getCommands();

        //test soap
        $status = $command->CheckConnection();

        var_dump($status);
    }

    public function imageUpload()
    {
        header('Content-Type: application/json');

        $this->loadLibrary('bulletproof.upload');

        $uplaoder = new Bulletproof\Image($_FILES);

        // define allowed mime types to upload
        $uplaoder->setMime(array('jpeg', 'jpg', 'png', 'gif'));

        // pass name (and optional chmod) to create folder for storage
        $uplaoder->setLocation(ROOTPATH . '/uploads/temp');  
        
        // define the min/max image upload size (size in bytes) 
        $uplaoder->setSize(1, 10485760); // 1 byte to 10 megabytes

        if ($uplaoder["file"])
        {
            $upload = $uplaoder->upload(); 
        
            if ($upload)
            {
                echo json_encode(array('fileName' => $upload->getName().'.'.$upload->getMime(), 'dir' => $upload->getLocation(), 'filePath' => $upload->getFullPath()));
                exit;
            }
            else
            {
                echo json_encode(array('error' => $uplaoder->getError()));
                die;
            }
        }
        else
        {
            echo json_encode(array('error' => 'No file to upload.'));
            die;
        }
    }

    public function imageCrop()
    {
        $this->loadLibrary('img.manipulation');
        
        header('Content-Type: application/json');

        //information
        $folder = isset($_POST['path']) ? ROOTPATH . $_POST['path'] : false;
        $img_name = isset($_POST['imgName']) ? $_POST['imgName'] : false;
        $resize = isset($_POST['resize']) ? (int)$_POST['resize'] : false;

        if (!$folder)
        {
            echo json_encode(array('error' => 'File path is missing.'));
            die;
        }

        if (!$img_name)
        {
            echo json_encode(array('error' => 'Image name is missing.'));
            die;
        }

        //temp src
        $file_src = $folder.'/'.$img_name;	

        //replace white spaces
        $file_name = str_replace(' ', '_', $img_name);
        //find where the image extension begins and remove it
        $file_name = substr($file_name, 0, strrpos($file_name, '.'));
        //add cropped str
        $file_name = $file_name . '_cropped';

        $ext = pathinfo($file_src, PATHINFO_EXTENSION);

        //if mime type is not allowed, return error
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
        {
            echo json_encode(array('error' => 'File Type not allowed.'));
            die;
        }

        //appply the extension of the image
        $file_name = $file_name . '.' . $ext;

        //apply the file path
        $file_src_new = $folder . '/' . $file_name;

        $imageInfo = getimagesize($file_src);

        //Chmod the folder
        //$this->ChmodWritable($folder);

        //we've got no error
        $error = false;

        $objImage = new ImageManipulation($file_src);

        if ($objImage->imageok)
        {
            $objImage->setJpegQuality(100);
            $objImage->setCrop($_POST['x'], $_POST['y'], $_POST['w'], $_POST['h']);
            if ($resize)
            {
                if ($imageInfo[0] > $resize or $imageInfo[0] < $resize)
                {
                    $objImage->resize($resize);
                }
            }
            $objImage->save($file_src_new);
            
            @unlink($file_src);
        }
        else
        {
            $error = 'Epic Fail.';
        }

        //check for website failure
        if ($error)
        {
            echo json_encode(array('error' => $error));
            die;
        }

        //Chmod the folder back to normal
        //$this->ChmodReadonly($folder);

        echo json_encode(array('fileName' => $file_name, 'dir' => $folder, 'filePath' => $file_src_new));
        exit;
    }
}