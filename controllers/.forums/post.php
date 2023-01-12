<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/forums_controller.php';

class Post extends Forums_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function write()
    {
        $this->loggedInOrReturn();

        $topicId = isset($_GET['topic']) ? (int)$_GET['topic'] : false;
        $quote = isset($_GET['quote']) ? (int)$_GET['quote'] : false;

        if (!$topicId)
        {
            if (!($topicId = WCF::getLastViewedTopic()))
            {
                WCF::SetupNotification('Please make sure you are in a valid topic before posting.');
                header("Location: ".base_url()."/forums");
                die;
            }
        }

        // Get the topic info
        $topic = WCF::getTopicInfo($topicId);
        
        if (!$topic)
        {
            WCF::SetupNotification('Please make sure you are in a valid topic before posting.');
            header("Location: ".base_url()."/forums");
            die;
        }

        // Get the forum info
        $forum = WCF::getForumInfo($topic['forum']);
        $forum['category_name'] = 'Unknown';

        if ($forum)
        {
            if ($catName = WCF::getCategoryName($forum['category']))
            {
                $forum['category_name'] = $catName;
            }
            unset($catName);

            // Check the post roles
            $post_roles = explode(',', $forum['post_roles']);
            if (!in_array(0, $post_roles) && !$this->user->hasAnyOfRoles($post_roles))
            {
                WCF::SetupNotification('You cannot post in this section of the forums.');
                header("Location: ".base_url()."/forums/topic?id=".$topicId);
                die;
            }
        }

        if ((int)$topic['locked'] == 1)
        {
            WCF::SetupNotification('The topic is locked.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //Set the title
        $this->tpl->SetTitle('Reply to Topic');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/post_write', array(
            'quote' => $quote,
            'topic' => $topic,
            'forum' => $forum,
        ));

        $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.js');
	    $this->tpl->AddFooterJs('template/js/sceditor/jquery.sceditor.bbcode.js');
	    $this->tpl->AddFooterJs('template/js/forms.js');
        $this->tpl->LoadFooter();
    }

    public function submit()
    {
        $this->loggedInOrReturn();

        //setup new instance of multiple errors
        $this->errors->NewInstance('post_reply');

        //Define the variables
        $topicId = isset($_POST['topic']) ? (int)$_POST['topic'] : false;
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $text = isset($_POST['text']) ? $_POST['text'] : false;
        $staffPost = isset($_POST['staff_post']) ? true : false;

        if (!$topicId)
        {
            //We have no forum id
            $this->errors->Add('An unexpected error occurred, missing topic id.');
        }
        else if (!WCF::verifyTopicId($topicId))
        {
            //Veirfy the forum
            $this->errors->Add('An unexpected error occurred, the selected topic is invalid.');
        }

        if ($title && strlen($title) > 150)
        {
            $this->errors->Add('The reply title is too long, maximum 150 characters.');
        }
        
        if (!$text)
        {
            $this->errors->Add('Please enter text for the reply.');
        }
            
        //Check for errors
        $this->errors->Check('/forums/post/write?topic='.$topicId);

        //Resolve the forum name and id
        $res = $this->db->prepare("SELECT `name`, `forum`, `locked` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $topicId, PDO::PARAM_INT);
        $res->execute();
        
        if ($res->rowCount() == 0)
        {
            $this->errors->Add('The topic you are replying on might have been removed. Cannot continue.');
        }
        
        // Fetch the topic record
        $topic = $res->fetch();
        
        if ((int)$topic['locked'] == 1)
        {
            $this->errors->Add('The topic you are replying on is locked.');
        }

        if ($forum = WCF::getForumInfo($topic['forum']))
        {
            // Check the post roles
            $post_roles = explode(',', $forum['post_roles']);
            if (!in_array(0, $post_roles) && !$this->user->hasAnyOfRoles($post_roles))
            {
                $this->errors->Add('You cannot post in this section of the forums.');
            }
        }

        //Check for errors
        $this->errors->Check('/forums/post/write?topic='.$topicId);

        $forumId = $topic['forum'];
        
        //Post title if missing
        if (!$title)
        {
            $title = 'Re: ' . $topic['name'];
        }
        
        //Post Flags
        $postFlags = 0;
        
        //Should we enable staff post
        if ($staffPost && $this->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF))
        {
            $this->setFlag($postFlags, WCF_FLAGS_STAFF_POST);
        }
        
        $userId = $this->user->get('id');
        $time = $this->getTime();

        //Insert the first post
        $insert2 = $this->db->prepare("INSERT INTO `wcf_posts` (`topic`, `title`, `text`, `added`, `author`, `flags`) VALUES (:topic, :title, :text, :time, :author, :flags);");
        $insert2->bindParam(':topic', $topicId, PDO::PARAM_INT);
        $insert2->bindParam(':title', $title, PDO::PARAM_STR);
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
            
            ######################################
            ########## Redirect ##################
            $PostPage = WCF::calculatePostPage($postId);
            
            //bind the onsuccess message
            $this->errors->onSuccess('Success.', '/forums/topic?id=' . $topicId . '&p='.$PostPage.'#post-' . $postId);

            //Trigger it
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to submit your post. Please contact the administration.');
        }
            
        $this->errors->Check('/forums/post/write?topic='.$topicId);
        exit;
    }

    public function edit()
    {
        $this->loggedInOrReturn();

        $PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        //Validate the post
        if ($PostId === false)
        {
            WCF::SetupNotification('The selected reply is invalid.');
            header("Location: ".base_url()."/forums");
            die;
        }

        $res = $this->db->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $PostId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            WCF::SetupNotification('The selected reply is invalid.');
            header("Location: ".base_url()."/forums");
            die;
        }

        //Fetch the post record
        $Post = $res->fetch();

        //Free mem
        unset($res);

        //Verify that we have permissions to edit
        //Start by checking if we own that post
        if ($this->user->get('id') != $Post['author'])
        {
            //Since we dont own the post
            //Check if we have the permission
            if (!$this->user->hasPermission(PERMISSION_FORUMS_EDIT_POSTS))
            {
                WCF::SetupNotification('You do not meet the requirements to edit this post.');
                header("Location: ".base_url()."/forums");
                die;
            }
            else
            {
                //We have the permission
                //now check if the authoer is lower rank
                //If the author is not resolved we assume he is lower rank
                if ($userInfo = WCF::getAuthorInfo($Post['author']))
                {
                    //Get the poster rank
                    $userRank = new UserRank($userInfo['rank']);
                    
                    //The author has equal or geater rank, we cant delete his post
                    if ($this->user->getRank()->int() <= $userRank->int())
                    {
                        WCF::SetupNotification('You do not meet the requirements to edit this post.');
                        header("Location: ".base_url()."/forums");
                        die;
                    }
                }
            }
        }

        if ($topic = WCF::getTopicInfo($Post['topic']))
        {
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
        }

        //Set the title
        $this->tpl->SetTitle('Edit Reply');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/post_edit', array(
            'Post' => $Post,
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
        $this->errors->NewInstance('edit_reply');

        //Define the variables
        $PostId = isset($_POST['post']) ? (int)$_POST['post'] : false;
        $title = isset($_POST['title']) ? $_POST['title'] : false;
        $text = isset($_POST['text']) ? $_POST['text'] : false;
        $staffPost = isset($_POST['staff_post']) ? true : false;

        if (!$PostId)
        {
            //We have no forum id
            $this->errors->Add('An unexpected error occurred, missing reply id.');
        }
        else if (!WCF::verifyPostId($PostId))
        {
            //Veirfy the forum
            $this->errors->Add('An unexpected error occurred, the selected post is invalid.');
        }
        
        if (!$title)
        {
            $this->errors->Add('Please enter reply title.');
        }
        else if (strlen($title) > 150)
        {
            $this->errors->Add('The reply title is too long, maximum 150 characters.');
        }
        
        if (!$text)
        {
            $this->errors->Add('Please enter reply text.');
        }
            
        //Check for errors
        $this->errors->Check('/forums/post/edit?id=' . $PostId);

        //We need to pull the post flags
        $res = $this->db->prepare("SELECT `id`, `flags`, `topic`, `author` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $PostId, PDO::PARAM_INT);
        $res->execute();

        $Post = $res->fetch();

        //Verify that we have permissions to edit
        //Start by checking if we own that post
        if ($this->user->get('id') != $Post['author'])
        {
            //Since we dont own the post
            //Check if we have the permission
            if (!$this->user->hasPermission(PERMISSION_FORUMS_EDIT_POSTS))
            {
                $this->errors->Add('You do not meet the requirements to edit this post.');
            }
            else
            {
                //We have the permission
                //now check if the authoer is lower rank
                //If the author is not resolved we assume he is lower rank
                if ($userInfo = WCF::getAuthorInfo($Post['author']))
                {
                    //Get the poster rank
                    $userRank = new UserRank($userInfo['rank']);
                    
                    //The author has equal or geater rank, we cant delete his post
                    if ($this->user->getRank()->int() <= $userRank->int())
                    {
                        $this->errors->Add('You do not meet the requirements to edit this post.');
                    }
                }
            }
        }

        //Check for errors
        $this->errors->Check('/forums/post/edit?id=' . $PostId);

        //Post Flags
        $postFlags = (int)$Post['flags'];

        //Should we enable staff post
        if ($staffPost && $this->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF))
        {
            if (!$this->hasFlag($postFlags, WCF_FLAGS_STAFF_POST))
                $this->setFlag($postFlags, WCF_FLAGS_STAFF_POST);
        }
        else
        {
            $this->removeFlag($postFlags, WCF_FLAGS_STAFF_POST);
        }

        $userId = $this->user->get('id');
        $time = $this->getTime();

        //Update the topic with the post id
        $update = $this->db->prepare("UPDATE `wcf_posts` SET `title` = :title, `text` = :text, `flags` = :flags, `lastedit_by` = :editor, `lastedit_time` = :time WHERE `id` = :post LIMIT 1;");
        $update->bindParam(':title', $title, PDO::PARAM_STR);
        $update->bindParam(':text', $text, PDO::PARAM_STR);
        $update->bindParam(':flags', $postFlags, PDO::PARAM_INT);
        $update->bindParam(':post', $PostId, PDO::PARAM_INT);
        $update->bindParam(':editor', $userId, PDO::PARAM_INT);
        $update->bindParam(':time', $time, PDO::PARAM_STR);
        $update->execute();

        if ($update->rowCount() > 0)
        {
            //We've got to clear the cache
            $this->cache->clear('forums/posts/post_' . $PostId);

            //Get the post page
            $page = WCF::calculatePostPage($PostId);

            //bind the onsuccess message
            $this->errors->onSuccess('Success.', '/forums/topic?id=' . $Post['topic'] . '&p='.$page.'#post-' . $PostId);

            //Trigger it
            $this->errors->triggerSuccess();
        }
        else
        {
            $this->errors->Add('The website failed to update you\'re reply. Please contact the administration.');
        }

        //Check for errors
        $this->errors->Check('/forums/post/edit?id=' . $PostId);
        exit;
    }

    public function delete()
    {
        if (!$this->user->isOnline())
        {
            echo 'You must be logged in!';
            die;
        }

        $PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($PostId === false)
        {
            echo 'No post selected.';
            die;
        }

        //Validate the post
        $res = $this->db->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $PostId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo 'The selected post is invalid.';
            die;
        }

        //Fetch the post data
        $Post = $res->fetch();

        unset($res);

        //Verify that we have permissions to delete
        //Start by checking if we own that post
        if ($this->user->get('id') != $Post['author'])
        {
            //Since we dont own the post
            //Check if we have the permission
            if (!$this->user->hasPermission(PERMISSION_FORUMS_DELETE_POSTS))
            {
                echo 'You do not meet the requirements to delete this post.';
                die;
            }
            else
            {
                //We have the permission
                //now check if the authoer is lower rank
                //If the author is not resolved we assume he is lower rank
                if ($userInfo = WCF::getAuthorInfo($Post['author']))
                {
                    //Get the poster rank
                    $userRank = new UserRank($userInfo['rank']);
                    
                    //The author has equal or geater rank, we cant delete his post
                    if ($this->user->getRank()->int() <= $userRank->int())
                    {
                        echo 'You do not meet the requirements to delete this post.';
                        die;
                    }
                }
            }
        }

        $userId = $this->user->get('id');
        $time = $this->getTime();

        //Posts dont actually get delete, but only disabled os we gonna do a little update
        $update = $this->db->prepare("UPDATE `wcf_posts` SET `deleted_by` = :user, `deleted_time` = :time WHERE `id` = :post LIMIT 1;");
        $update->bindParam(':user', $userId, PDO::PARAM_INT);
        $update->bindParam(':time', $time, PDO::PARAM_STR);
        $update->bindParam(':post', $PostId, PDO::PARAM_INT);
        $update->execute();

        if ($update->rowCount() == 0)
        {
            echo 'The website failed to delete the post. Please contact the administration.';
            die;
        }

        echo 'OK';
        exit;
    }

    public function quoteInfo()
    {
        //We dont have an error
        $error = false;

        //Set the json headers
        header('Content-type: application/json');

        if (!$this->user->isOnline())
        {
            $error = 'You must be logged in!';
        }

        $PostId = isset($_GET['id']) ? (int)$_GET['id'] : false;

        if ($PostId === false)
        {
            $error = 'Invalid post id.';
        }

        //Validate the post
        $res = $this->db->prepare("SELECT * FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $PostId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            $error = 'Invalid post id.';
        }

        //check for errors
        if (!$error)
        {
            //Fetch the post data
            $Post = $res->fetch();
            
            if ($author = WCF::getAuthorById($Post['author']))
            {
                $Post['author_str'] = $author;
            }
            else
            {
                $Post['author_str'] = 'Unknown';
            }
            unset($author);
            
            $data = array(
                'text' 		=> $Post['text'],
                'author'	=> $Post['author_str'],
            );
            
            echo json_encode($data);
        }
        else
        {
            echo json_encode(array('error' => $error));
        }

        unset($res);
        exit;
    }
}