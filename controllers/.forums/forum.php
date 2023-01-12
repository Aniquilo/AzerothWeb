<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/forums_controller.php';

class Forum extends Forums_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loadLibrary('pagination.forum');

        $forumId = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        //Let's setup our pagination
        $pagies = new Pagination();
        $pagies->addToLink('?id='.$forumId);

        $perPage = $this->config['FORUM']['Topics_Limit'];

        //make sure we have the forum id
        if (!$forumId)
        {
            WCF::SetupNotification('Please make sure you have selected a valid forum.');
            header("Location: ".base_url()."/forums");
            die;
        }

        $res = $this->db->prepare("SELECT * FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $forumId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            WCF::SetupNotification('The selected forum does not exist or was deleted.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //save the last viewd forum
        WCF::setLastViewedForum($forumId);

        //Fetch the forum record
        $row = $res->fetch();

        // Check the view roles
        $view_roles = explode(',', $row['view_roles']);
                                
        if (!in_array(0, $view_roles) && !$this->user->hasAnyOfRoles($view_roles))
        {
            WCF::SetupNotification('You cannot view this section of the forums.');
            header("Location: ".base_url()."/forums");
            die;
        }

        // Get the category info
        $category = WCF::getCategoryInfo($row['category']);

        if ($category)
        {
            $row['category_name'] = $category['name'];

            if (strlen($row['name']) == 0 && (int)$category['flags'] & WCF_FLAGS_CLASSES_LAYOUT)
                $row['name'] = $this->realms->getClassString($row['class']);
        }
        else
        {
            $row['category_name'] = 'Unknown';
        }
        unset($catName);

        //Set the title
        $this->tpl->SetTitle(WCF::parseTitle($row['name']));
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //count the total topics
        $totalCount = WCF::getTopicsCount($row['id'], false);

        //Prepare pagination
        $pagination = false;

        //calculate the pages
        $pagination = $pagies->calculate_pages($totalCount, $perPage, $page);

        $topics_res = $this->db->prepare("SELECT * FROM `wcf_topics` WHERE `forum` = :forum AND `sticky` = 0 ORDER BY `lastpost_time` DESC LIMIT ".$pagination['limit'].";");
        $topics_res->bindParam(':forum', $row['id'], PDO::PARAM_INT);
        $topics_res->execute();
        
        //Get the topics on this page
        $countOnPage = $topics_res->rowCount();

        //Get the topics array
        $topics = $topics_res->fetchAll();

        unset($topics_res);

        //Get sticky topics
        $sticky_res = $this->db->prepare("SELECT * FROM `wcf_topics` WHERE `forum` = :forum AND `sticky` = 1 ORDER BY `lastpost_time` DESC;");
        $sticky_res->bindParam(':forum', $row['id'], PDO::PARAM_INT);
        $sticky_res->execute();

        //Get the topics array
        if ($sticky_res->rowCount())
        {
            $sticky_topics = $sticky_res->fetchAll();
            
            // Merge topics
            $topics = array_merge($sticky_topics, $topics);
            $totalCount += count($sticky_topics);
            
            unset($sticky_topics);
        }

        unset($sticky_res);

        // Check if the current user can start a topic
        $canStartTopic = $this->user->isOnline();

        $topic_roles = explode(',', $row['topic_roles']);
        if (!in_array(0, $topic_roles) && !$this->user->hasAnyOfRoles($topic_roles))
            $canStartTopic = false;

        //Print the page view
        $this->tpl->LoadView('forums/forum', array(
            'forum' => $row,
            'topics' => $topics,
            'canStartTopic' => $canStartTopic,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'countOnPage' => $countOnPage,
            'pagination' => $pagination
        ));

        $this->tpl->LoadFooter();
    }
}