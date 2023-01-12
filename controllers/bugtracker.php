<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Bugtracker extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $statusApproved = BT_APP_STATUS_APPROVED;

        //define changesets per pare
        $PerPage = 10;

        //count the user's reports that are not approved
        $res = $this->db->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `approval` != :status;");
        $res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
        $res->execute();
        $count = $res->fetch(PDO::FETCH_NUM);
        $countNotApproved = $count[0];
        unset($res, $count);

        //count the user's reports that are approved
        $res = $this->db->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `approval` = :status;");
        $res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
        $res->execute();
        $count = $res->fetch(PDO::FETCH_NUM);
        $countApproved = $count[0];
        unset($res, $count);

        //total
        $total = $countNotApproved + $countApproved;

        // User counts
        $userCounts = array(
            'total' => 0,
            'approved' => 0,
            'notApproved' => 0,
        );

        //check if we have current user
        if ($this->user->isOnline())
        {
            $userId = $this->user->get('id');

            //count the user's reports that are not approved
            $res = $this->db->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `account` = :acc AND `approval` != :status;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $userCounts['notApproved'] = $count[0];
            unset($res, $count);

            //count the user's reports that are approved
            $res = $this->db->prepare("SELECT COUNT(*) FROM `bugtracker` WHERE `account` = :acc AND `approval` = :status;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->bindParam(':status', $statusApproved, PDO::PARAM_INT);
            $res->execute();
            $count = $res->fetch(PDO::FETCH_NUM);
            $userCounts['approved'] = $count[0];
            unset($res, $count);
            
            //total count
            $userCounts['total'] = $userCounts['approved'] + $userCounts['notApproved'];
        }

        $this->tpl->SetTitle('Bug Tracker');
        $this->tpl->SetSubtitle('Bug Tracker');
        $this->tpl->AddCSS('template/style/page-bugtracker-all.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('bugtracker/bugtracker', array(
            'PerPage' => $PerPage,
            'total' => $total,
            'countApproved' => $countApproved,
            'countNotApproved' => $countNotApproved,
            'userCounts' => $userCounts
        ));

        $this->tpl->AddFooterJs('template/js/page.bugtracker.js');
        $this->tpl->LoadFooter();
    }

    public function search()
    {
        $this->tpl->SetTitle('Bug Tracker - Search');
        $this->tpl->SetSubtitle('Bug Tracker');
        $this->tpl->AddCSS('template/style/page-bugtracker-all.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('bugtracker/search');

        $this->tpl->LoadFooter();
    }

    public function get_reports()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : false;
        $perPage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : false;

        if ($page && $perPage)
        {
            //calculate the offset
            $offsetStart = ($page - 1) * $perPage;

            if ($this->user->isOnline())
            {
                $userId = $this->user->get('id');

                $res = $this->db->prepare("SELECT * FROM `bugtracker` WHERE `account` = :acc ORDER BY id DESC LIMIT ".$offsetStart.",".$perPage);
                $res->bindParam(':acc', $userId, PDO::PARAM_STR);
                $res->execute();
            
                //get the count
                $count = $res->rowCount();
                        
                //save the count to the data
                $data['count'] = $count;
                
                if ($count > 0)
                {
                    //get the categories
                    $CategoryStore = new BTCategories();
                    
                    //setup empty array
                    $data['issues'] = array();
                    
                    //loop the issues found
                    while ($arr = $res->fetch())
                    {
                        //Translate the status
                        switch ($arr['status'])
                        {
                            case BT_STATUS_NEW:
                                $status = 'New';
                                break;
                            case BT_STATUS_OPEN:
                                $status = 'Open';
                                break;
                            case BT_STATUS_ONHOLD:
                                $status = 'On hold';
                                break;
                            case BT_STATUS_DUPLICATE:
                                $status = 'Duplicate';
                                break;
                            case BT_STATUS_INVALID:
                                $status = 'Invalid';
                                break;
                            case BT_STATUS_WONTFIX:
                                $status = '';
                                break;
                            case BT_STATUS_RESOLVED:
                                $status = 'Resolved';
                                break;
                            default:
                                $status = 'Unknown';
                                break;
                        }

                        //translate the approval
                        switch ($arr['approval'])
                        {
                            case BT_APP_STATUS_APPROVED:
                                $approval = 'approved';
                                break;
                            case BT_APP_STATUS_DECLINED:
                                $approval = 'declined';
                                break;
                            default:
                                $approval = 'pending';
                                break;
                        }
                        
                        //translate the priority
                        switch ($arr['priority'])
                        {
                            case BT_PRIORITY_LOW:
                                $priority = 'Low';
                                break;
                            case BT_PRIORITY_NORMAL:
                                $priority = 'Normal';
                                break;
                            case BT_PRIORITY_HIGH:
                                $priority = 'High';
                                break;
                            default:
                                $priority = 'Abnormal';
                                break;
                        }
                        
                        //get the main category
                        $MainCategory = $CategoryStore->getMainCategory($arr['maincategory']);

                        switch ($arr['maincategory'])
                        {
                            case BT_CAT_WEBSITE:
                                $MainCategoryName = 'Website';
                                break;
                            case BT_CAT_WOTLK_CORE:
                                $MainCategoryName = 'Game Server';
                                break;
                            default:
                                $MainCategoryName = 'Unknown';
                                break;
                        }
                        
                        //get the category
                        $Category = $MainCategory->getCategory($arr['category']);
                        
                        if ($Category === false)
                        {
                            $CategoryName = 'Unknown';
                        }
                        else
                        {
                            $CategoryName = $Category->getName();
                        }
                        
                        $SubCategoryName = false;
                        //check for sub category
                        if ($Category->hasSubCategories())
                        {
                            $SubCategoryName = $Category->getSubCategoryName($arr['subcategory']);
                        }
                        
                        //free memory
                        unset($MainCategory, $Category);
                        
                        //put the category string together
                        $category = $CategoryName;
                        if ($SubCategoryName)
                        {
                            $category .= ' - '.$SubCategoryName;
                        }
                        
                        //free memory
                        unset($CategoryName, $SubCategoryName);
                        
                        //save the issue data
                        $data['issues'][] = array(
                            'title' 		=> htmlspecialchars(stripslashes($arr['title'])),
                            'approval'		=> $approval,
                            'status'		=> $status,
                            'priority'		=> $priority,
                            'category'		=> $category,
                            'maincategory'	=> $MainCategoryName,
                        );
                    }
                    unset($arr, $status, $category, $approval, $priority, $MainCategoryName);
                }
                unset($count, $res, $CategoryStore);
            }
            else
            {
                $data = array(
                    'error' => 'The user is not logged in.',
                );
            }
        }
        else
        {
            $data = array(
                'error' => 'Missing variables.',
            );
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function new_report()
    {
        $this->loggedInOrReturn();

        $this->tpl->SetTitle('Submit new Bug Report');
        $this->tpl->SetSubtitle('Bug Tracker');
        $this->tpl->AddCSS('template/style/page-bugtracker-all.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('bugtracker/new_report');

        $this->tpl->AddFooterJs('template/js/page.bugtracker.submit.js');
        $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function submit_report()
    {
        $this->loggedInOrReturn();

        //prepare multi errors
        $this->errors->NewInstance('submit_bug');

        //bind the onsuccess message
        $this->errors->onSuccess('Your bug report has been successfully submitted.', '/bugtracker');

        $title = (isset($_POST['title']) ? $_POST['title'] : false);
        $text = (isset($_POST['text']) ? $_POST['text'] : false);

        $priority = (isset($_POST['prio']) ? (int)$_POST['prio'] : 1);

        $mainCategory = (isset($_POST['mainCategory']) ? (int)$_POST['mainCategory'] : false);
        $category = (isset($_POST['category']) ? (int)$_POST['category'] : false);
        $subcategory = (isset($_POST['subcategory']) ? (int)$_POST['subcategory'] : false);

        //define the valid categories
        $validMainCategories = array(BT_CAT_WEBSITE, BT_CAT_WOTLK_CORE);

        if (!$title)
        {
            $this->errors->Add("Please enter report title.");
        }
        else if (strlen($title) > 250)
        {
            $this->errors->Add("The title is too long. 250 characters maximum.");	
        }
        else if (str_word_count($title) < 2)
        {
            $this->errors->Add("The title is too short. 2 words minimum.");	
        }

        if (!$text)
        {
            $this->errors->Add("Please describe the bug as much detail as possible.");
        }

        if (!$category)
        {
            $this->errors->Add("Please select a category.");
        }
        else
        //validate the category
        if (!in_array($mainCategory, $validMainCategories))
        {
            $this->errors->Add("Please select valid category.");
        }

        $this->errors->Check('/bugtracker/new_report');

        ####################################################################
        ## The actual unstuck script begins here
            
            $CategoryStore = new BTCategories();
            $CategoryData = $CategoryStore->getMainCategory($mainCategory)->getCategory($category);
            //free memory
            unset($CategoryStore);
                
            //Do more checks
            if ($CategoryData === false)
            {
                $this->errors->Add("Please select valid sub-category.");
            }
            else if ($CategoryData->hasSubCategories() and !$subcategory)
            {
                $this->errors->Add("Please select specifics.");
            }
            else if ($subcategory)
            {
                //try getting the sub-category name
                if (!$SubCategoryName = $CategoryData->getSubCategoryName($subcategory))
                {
                    $this->errors->Add("Please select valid specifics.");
                }
                unset($SubCategoryName);
            }
            //free some memory
            unset($CategoryData);
            
            //check for errors
            $this->errors->Check('/bugtracker/new_report');
            
            //approval status
            $approval = BT_APP_STATUS_PENDING;
            $status = BT_STATUS_NEW;
            $userId = $this->user->get('id');
            $added = $this->getTime();
            
            //Insert our issue link
            $insert = $this->db->prepare("INSERT INTO `bugtracker` (`account`, `title`, `content`, `maincategory`, `category`, `subcategory`, `added`, `status`, `priority`, `approval`) VALUES (:acc, :title, :content, :maincat, :cat, :subcat, :added, :status, :priority, :approval);");
            $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
            $insert->bindParam(':title', $title, PDO::PARAM_STR);
            $insert->bindParam(':content', $text, PDO::PARAM_STR);
            $insert->bindParam(':maincat', $mainCategory, PDO::PARAM_INT);
            $insert->bindParam(':cat', $category, PDO::PARAM_INT);
            $insert->bindParam(':subcat', $subcategory, PDO::PARAM_INT);
            $insert->bindParam(':added', $added, PDO::PARAM_STR);
            $insert->bindParam(':status', $status, PDO::PARAM_INT);
            $insert->bindParam(':priority', $priority, PDO::PARAM_INT);
            $insert->bindParam(':approval', $approval, PDO::PARAM_INT);
            $insert->execute();
            unset($insert);
            
            $this->errors->triggerSuccess();
            
        ####################################################################

        $this->errors->Check('/bugtracker/new_report');
        exit;
    }

    public function getCategoryData()
    {
        $category = isset($_GET['category']) ? (int)$_GET['category'] : false;

        if ($category)
        {
            $data = new BTCategories();
            $catData = $data->getMainCategory($category)->data;
            unset($data);
            
            $encoded = json_encode($catData);
            unset($catData);
            
            header('Content-Type: application/json');
            echo $encoded;
            exit;
        }
        else
        {
            header('HTTP/1.0 404 not found');
            exit;
        }
    }
}