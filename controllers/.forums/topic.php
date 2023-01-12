<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/forums_controller.php';

class Topic extends Forums_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loadLibrary('forums.parser');
        $this->loadLibrary('pagination.forum');

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        //Let's setup our pagination
        $pagies = new Pagination();
        $pagies->addToLink('?id='.$topicId);

        $perPage = (int)$this->config['FORUM']['Posts_Limit'];

        //make sure we have the forum id
        if (!$topicId)
        {
            WCF::SetupNotification('Please make sure you have selected a valid topic.');
            header("Location: ".base_url()."/forums");
            die;
        }
            
        $res = $this->db->prepare("SELECT * FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $topicId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            WCF::SetupNotification('The selected topic does not exist or was deleted.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //save the last viewed topic
        WCF::setLastViewedTopic($topicId);

        //Fetch the post record
        $row = $res->fetch();
                
        //format the time
        $row['added'] = date('D M j, Y, h:i A', strtotime($row['added']));

        //prepare some forum info
        $forumRow = array();

        if ($forumRow = WCF::getForumInfo($row['forum']))
        {
            // Get the category info
            $category = WCF::getCategoryInfo($forumRow['category']);

            if ($category)
            {
                $forumRow['category_name'] = $category['name'];

                if (strlen($forumRow['name']) == 0 && (int)$category['flags'] & WCF_FLAGS_CLASSES_LAYOUT)
                    $forumRow['name'] = $this->realms->getClassString($forumRow['class']);
            }
            else
            {
                $forumRow['category_name'] = 'Unknown';
            }
            unset($catName);
        }
        else
        {
            $forumRow['id'] = 0;
            $forumRow['name'] = 'Unknown';
            $forumRow['category'] = 0;
            $forumRow['category_name'] = 'Unknown';
        }

        // Check the view roles
        $view_roles = explode(',', $forumRow['view_roles']);
                                        
        if (!in_array(0, $view_roles) && !$this->user->hasAnyOfRoles($view_roles))
        {
            WCF::SetupNotification('You cannot view this section of the forums.');
            header("Location: ".base_url()."/forums");
            die;
        }

        // Check the post roles
        $post_roles = explode(',', $forumRow['post_roles']);
        $userCanPost = $this->user->isOnline();

        if (!in_array(0, $post_roles) && !$this->user->hasAnyOfRoles($post_roles))
            $userCanPost = false;

        //Staff memebers should be able to see deleted posts
        $IncludeDeleted = (($this->user->isOnline() && $this->user->hasPermission(PERMISSION_FORUMS_VIEW_DELETED_POSTS)) ? true : false);

        //count the total topics
        $totalCount = WCF::getPostsCount($row['id'], $IncludeDeleted);
        
        //calculate the pages
        $pagination = $pagies->calculate_pages($totalCount, $perPage, $page);
        
        $posts_res = $this->db->prepare("SELECT * FROM `wcf_posts` WHERE `topic` = :topic ".($IncludeDeleted ? '' : "AND `deleted_by` = '0'")." ORDER BY `id` ASC LIMIT ".$pagination['limit'].";");
        $posts_res->bindParam(':topic', $row['id'], PDO::PARAM_INT);
        $posts_res->execute();
        
        $countOnPage = $posts_res->rowCount();

        //Set the title
        $this->tpl->SetTitle(WCF::parseTitle($row['name']));
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/topic', array(
            'topic' => $row,
            'forum' => $forumRow,
            'posts_res' => $posts_res,
            'userCanPost' => $userCanPost,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'countOnPage' => $countOnPage,
            'pagination' => $pagination
        ));

        $this->tpl->LoadFooter();
    }

    public function create()
    {
        $this->loggedInOrReturn();

        if (!($forumId = WCF::getLastViewedForum()))
        {
            WCF::SetupNotification('Please make sure you are in a valid forum before posting.');
            header("Location: ".base_url()."/forums");
            die;
        }

        if ($forum = WCF::getForumInfo($forumId))
        {
            if ($catName = WCF::getCategoryName($forum['category']))
            {
                $forum['category_name'] = $catName;
            }
            else
            {
                $forum['category_name'] = 'Unknown';
            }
            unset($catName);
        }

        // Check if the current user can start a topic
        $topic_roles = explode(',', $forum['topic_roles']);
        if (!in_array(0, $topic_roles) && !$this->user->hasAnyOfRoles($topic_roles))
        {
            WCF::SetupNotification('You cannot start a new topic in this section of the forums.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //Set the title
        $this->tpl->SetTitle('Create new topic');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/topic_create', array(
            'forum' => $forum
        ));

        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.js');
        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.bbcode.js');
        $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function submit_create()
    {
        $this->loggedInOrReturn();

        //setup new instance of multiple errors
        $this->errors->NewInstance('post_topic');

        //Define the variables
        $forumId = isset($_POST['forum']) ? (int)$_POST['forum'] : false;
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $text = isset($_POST['text']) ? $_POST['text'] : false;
        $staffPost = isset($_POST['staff_post']) ? true : false;

        if (!$forumId)
        {
            //We have no forum id
            $this->errors->Add('An unexpected error occurred, missing forum id.');
        }
        else if (!$forum = WCF::getForumInfo($forumId))
        {
            //Veirfy the forum
            $this->errors->Add('An unexpected error occurred, the selected forum is invalid.');
        }
        
        if (!$title)
        {
            $this->errors->Add('Please enter topic title.');
        }
        else if (strlen($title) > 150)
        {
            $this->errors->Add('The topic title is too long, maximum 150 characters.');
        }
        
        if (!$text)
        {
            $this->errors->Add('Please enter text for the topic.');
        }
        
        // Check if the current user can start a topic
        $topic_roles = explode(',', $forum['topic_roles']);
        if (!in_array(0, $topic_roles) && !$this->user->hasAnyOfRoles($topic_roles))
        {
            $this->errors->Add('You cannot start a new topic in this section of the forums.');
        }

        //Check for errors
        $this->errors->Check('/forums/topic/create');

        //get the time
        $time = $this->getTime();

        //Topic Flags
        $flags = 0;
        
        $userId = $this->user->get('id');

        //Insert the topic record
        $insert = $this->db->prepare("INSERT INTO `wcf_topics` (`forum`, `name`, `added`, `author`, `flags`) VALUES (:forum, :name, :time, :author, :flags);");
        $insert->bindParam(':forum', $forumId, PDO::PARAM_INT);
        $insert->bindParam(':name', $title, PDO::PARAM_STR);
        $insert->bindParam(':time', $time, PDO::PARAM_STR);
        $insert->bindParam(':author', $userId, PDO::PARAM_INT);
        $insert->bindParam(':flags', $flags, PDO::PARAM_INT);
        $insert->execute();
            
        //check if the topic was inserted
        if ($insert->rowCount() > 0)
        {
            $topicId = $this->db->lastInsertId();
            
            //prepare the post title
            $postTitle = 'Re: ' . $title;
            
            //Post Flags
            $postFlags = 0;
            
            //Should we enable staff post
            if ($staffPost && $this->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF))
            {
                $this->setFlag($postFlags, WCF_FLAGS_STAFF_POST);
            }
            
            $userId = $this->user->get('id');

            //Insert the first post
            $insert2 = $this->db->prepare("INSERT INTO `wcf_posts` (`topic`, `title`, `text`, `added`, `author`, `flags`) VALUES (:topic, :title, :text, :time, :author, :flags);");
            $insert2->bindParam(':topic', $topicId, PDO::PARAM_INT);
            $insert2->bindParam(':title', $postTitle, PDO::PARAM_STR);
            $insert2->bindParam(':text', $text, PDO::PARAM_STR);
            $insert2->bindParam(':time', $time, PDO::PARAM_STR);
            $insert2->bindParam(':author', $userId, PDO::PARAM_INT);
            $insert2->bindParam(':flags', $postFlags, PDO::PARAM_INT);
            $insert2->execute();
            
            if ($insert2->rowCount() > 0)
            {
                $postId = $this->db->lastInsertId();
                
                //Update the topic with the post id
                $update = $this->db->prepare("UPDATE `wcf_topics` SET `lastpost_id` = :post, `lastpost_time` = :time WHERE `id` = :topic LIMIT 1;");
                $update->bindParam(':topic', $topicId, PDO::PARAM_INT);
                $update->bindParam(':post', $postId, PDO::PARAM_INT);
                $update->bindParam(':time', $time, PDO::PARAM_STR);
                $update->execute();
                
                //Update the forum
                $update = $this->db->prepare("UPDATE `wcf_forums` SET `lasttopic_id` = :topic WHERE `id` = :forum LIMIT 1;");
                $update->bindParam(':topic', $topicId, PDO::PARAM_INT);
                $update->bindParam(':forum', $forumId, PDO::PARAM_INT);
                $update->execute();
                
                //bind the onsuccess message
                $this->errors->onSuccess('Success.', '/forums/topic?id=' . $topicId);

                //Trigger it
                $this->errors->triggerSuccess();
            }
            else
            {
                $this->errors->Add('The website failed to insert part of your topic. Please contact the administration.');
            }
        }
        else
        {
            $this->errors->Add('The website failed to insert your topic. Please contact the administration.');
        }

        $this->errors->Check('/forums/topic/create');
        exit;
    }

    public function edit()
    {
        $this->loggedInOrReturn();

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Validate the post
        if ($topicId === false)
        {
            WCF::SetupNotification('Invalid topic id.');
            header("Location: ".base_url()."/forums");
            die;
        }

        $res = $this->db->prepare("SELECT * FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $topicId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            WCF::SetupNotification('The selected topic is invalid.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //Fetch the topic record
        $topic = $res->fetch();

        //Free mem
        unset($res);

        //Verify that we have permissions to edit
        //Start by checking if we own that post
        if ($this->user->get('id') != $topic['author'])
        {
            //Since we dont own the post
            //Check if we have the permission
            if (!$this->user->hasPermission(PERMISSION_FORUMS_EDIT_TOPICS))
            {
                WCF::SetupNotification('You do not meet the requirements to edit this topic.');
                header("Location: ".base_url()."/forums");
                die;
            }
        }

        if ($forum = WCF::getForumInfo($topic['forum']))
        {
            if ($catName = WCF::getCategoryName($forum['category']))
            {
                $forum['category_name'] = $catName;
            }
            else
            {
                $forum['category_name'] = 'Unknown';
            }
            unset($catName);
        }

        //Set the title
        $this->tpl->SetTitle('Edit Topic');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/topic_edit', array(
            'topic' => $topic,
            'forum' => $forum,
        ));

        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.js');
	    $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.bbcode.js');
	    $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function submit_edit()
    {
        $this->loggedInOrReturn();

        //setup new instance of multiple errors
        $this->errors->NewInstance('edit_topic');

        //Define the variables
        $topicId = isset($_POST['topic']) ? (int)$_POST['topic'] : false;
        $title = isset($_POST['title']) ? $_POST['title'] : false;

        if (!$topicId)
        {
            //We have no forum id
            $this->errors->Add('An unexpected error occurred, missing topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->errors->Add('An unexpected error occurred, the selected topic is invalid.');
        }
        
        if (!$title)
        {
            $this->errors->Add('Please enter topic title.');
        }
        else if (strlen($title) > 150)
        {
            $this->errors->Add('The topic title is too long, maximum 150 characters.');
        }
         
        //Check for errors
        $this->errors->Check('/forums/topic/edit?id=' . $topicId);

        $res = $this->db->prepare("SELECT * FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $topicId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $this->errors->Add('An unexpected error occurred, the selected topic is invalid.');
        }

        //Fetch the topic record
        $topic = $res->fetch();

        //Free mem
        unset($res);

        //Verify that we have permissions to edit
        //Start by checking if we own that post
        if ($this->user->get('id') != $topic['author'])
        {
            //Since we dont own the post
            //Check if we have the permission
            if (!$this->user->hasPermission(PERMISSION_FORUMS_EDIT_TOPICS))
            {
                $this->errors->Add('You do not meet the requirements to edit this topic.');
            }
        }

        //Check for errors
        $this->errors->Check('/forums/topic/edit?id=' . $topicId);

        //Update the topic with the post id
        $update = $this->db->prepare("UPDATE `wcf_topics` SET `name` = :title WHERE `id` = :topic LIMIT 1;");
        $update->bindParam(':title', $title, PDO::PARAM_STR);
        $update->bindParam(':topic', $topicId, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            //bind the onsuccess message
            $this->errors->onSuccess('Success.', '/forums/topic?id=' . $topicId);

            //Trigger it
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to update the topic. Please contact the administration.');
        }

        //Check for errors
        $this->errors->Check('/forums/topic/edit?id=' . $topicId);
        exit;
    }

    public function delete()
    {
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in!');
        }

        //Check if we have the permission
        if (!$this->user->hasPermission(PERMISSION_FORUMS_DELETE_TOPICS))
        {
            $this->JsonError('You do not meet the requirements to delete this topic.');
        }

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($topicId === false)
        {
            $this->JsonError('Invalid topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->JsonError('An unexpected error occurred, the selected topic is invalid.');
        }

        //Delete the topic record
        $delete = $this->db->prepare("DELETE FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
        $delete->bindParam(':id', $topicId, PDO::PARAM_INT);
        $delete->execute();

        if ($delete->rowCount() > 0)
        {
            $delete2 = $this->db->prepare("DELETE FROM `wcf_posts` WHERE `topic` = :id LIMIT 1;");
            $delete2->bindParam(':id', $topicId, PDO::PARAM_INT);
            $delete2->execute();
        }
        else
        {
            $this->JsonError('An unexpected error occurred, failed to delete the topic record.');
        }

        //Setup notification
		$this->notifications->SetTitle('Forums');
		$this->notifications->SetHeadline('Success!');
		$this->notifications->SetText('The topic was successfully deleted.');
		$this->notifications->SetTextAlign('center');
		$this->notifications->SetAutoContinue(true);
		$this->notifications->SetContinueDelay(4);
        $this->notifications->Apply();
        
        $this->Json([ 'response' => true ]);
    }

    public function lock()
    {
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in!');
        }

        //Check if we have the permission
        if (!$this->user->hasPermission(PERMISSION_FORUMS_LOCK_TOPICS))
        {
            $this->JsonError('You do not meet the requirements to lock this topic.');
        }

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($topicId === false)
        {
            $this->JsonError('Invalid topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->JsonError('An unexpected error occurred, the selected topic is invalid.');
        }

        //Update the topic record
        $update = $this->db->prepare("UPDATE `wcf_topics` SET `locked` = '1' WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':id', $topicId, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            $this->Json([ 'response' => true ]);
        }

        $this->JsonError('An unexpected error occurred, failed to update the topic record.');
    }

    public function unlock()
    {
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in!');
        }

        //Check if we have the permission
        if (!$this->user->hasPermission(PERMISSION_FORUMS_UNLOCK_TOPICS))
        {
            $this->JsonError('You do not meet the requirements to unlock this topic.');
        }

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($topicId === false)
        {
            $this->JsonError('Invalid topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->JsonError('An unexpected error occurred, the selected topic is invalid.');
        }

        //Update the topic record
        $update = $this->db->prepare("UPDATE `wcf_topics` SET `locked` = '0' WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':id', $topicId, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            $this->Json([ 'response' => true ]);
        }

        $this->JsonError('An unexpected error occurred, failed to update the topic record.');
    }

    public function set_sticky()
    {
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in!');
        }

        //Check if we have the permission
        if (!$this->user->hasPermission(PERMISSION_FORUMS_MAN_STICKY))
        {
            $this->JsonError('You do not meet the requirements to manage this topic.');
        }

        $topicId = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $state = isset($_GET['state']) ? (int)$_GET['state'] : false;

        if ($topicId === false)
        {
            $this->JsonError('Invalid topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->JsonError('An unexpected error occurred, the selected topic is invalid.');
        }

        //Check state
        if ($state === false)
        {
            $this->JsonError('Invalid state.');
        }

        //Update the topic record
        $update = $this->db->prepare("UPDATE `wcf_topics` SET `sticky` = :state WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':id', $topicId, PDO::PARAM_INT);
        $update->bindParam(':state', $state, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            $this->Json([ 'response' => true ]);
        }

        $this->JsonError('An unexpected error occurred, failed to update the topic record.');
    }

    public function move()
    {
        if (!$this->user->isOnline())
        {
            $this->JsonError('You must be logged in!');
        }

        //Check if we have the permission
        if (!$this->user->hasPermission(PERMISSION_FORUMS_MOVE_TOPICS))
        {
            $this->JsonError('You do not meet the requirements to manage this topic.');
        }

        $topicId = isset($_POST['id']) ? (int)$_POST['id'] : false;
        $moveTo = isset($_POST['moveTo']) ? (int)$_POST['moveTo'] : false;

        if ($topicId === false)
        {
            $this->JsonError('Invalid topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the topic
            $this->JsonError('An unexpected error occurred, the selected topic is invalid.');
        }

        //Check moveTo
        if ($moveTo === false)
        {
            $this->JsonError('Invalid destination forum.');
        }
        else if (!WCF::verifyForumId($moveTo))
        {
            $this->JsonError('Invalid destination forum.');
        }

        $topic = WCF::getTopicInfo($topicId);

        if ((int)$topic['forum'] == $moveTo)
        {
            $this->JsonError('Invalid destination forum.');
        }

        //Update the topic record
        $update = $this->db->prepare("UPDATE `wcf_topics` SET `forum` = :moveTo WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':id', $topicId, PDO::PARAM_INT);
        $update->bindParam(':moveTo', $moveTo, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            // Check if we just moved the last topic in the forum
            if ($forum = WCF::getForumInfo($topic['forum']))
            {
                if ((int)$forum['lasttopic_id'] == $topicId)
                {
                    $update2 = $this->db->prepare("UPDATE `wcf_forums` SET `lasttopic_id` = 0 WHERE `id` = :id LIMIT 1;");
                    $update2->bindParam(':id', $topic['forum'], PDO::PARAM_INT);
                    $update2->execute();
                }
            }

            $this->Json([ 'response' => true ]);
        }

        $this->JsonError('An unexpected error occurred, failed to update the topic record.');
    }
}