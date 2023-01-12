<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class WCF
{
	static public function setLastViewedForum($id)
	{
		$_SESSION['FORUM']['LastViewedForum'] = (int)$id;
	}
	
	static public function setLastViewedTopic($id)
	{
		$_SESSION['FORUM']['LastViewedTopic'] = (int)$id;
	}
	
	static public function getLastViewedForum()
	{
		if (isset($_SESSION['FORUM']['LastViewedForum']))
		{
			return $_SESSION['FORUM']['LastViewedForum'];
		}
		
		return false;
	}
	
	static public function getLastViewedTopic()
	{
		if (isset($_SESSION['FORUM']['LastViewedTopic']))
		{
			return $_SESSION['FORUM']['LastViewedTopic'];
		}
		
		return false;
	}
	
	static public function parseTitle($str)
	{
		return htmlspecialchars(stripslashes($str));
	}
	
	###########################################
	###### POST FUNCTIONS #####################
	
	static public function verifyPostId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getPostsCount($topic, $IncludeDeleted = false)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_posts` WHERE `topic` = :topic ".($IncludeDeleted ? '' : "AND `deleted_by` = '0'").";");
		$res->bindParam(':topic', $topic, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
	}
	
	static public function getPostInfo($post)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `added`, `author`, `topic` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $post, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			//Calculate last page
			$row['page_number'] = self::calculatePostPage($post);

			return $row;
		}
		
		return false;
	}
	
	static public function getQuoteInfo($post)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `author`, `text` FROM `wcf_posts` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $post, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author'] = $author;
			}
			else
			{
				$row['author'] = 'Unknown';
			}
			unset($author);

			return $row;
		}
		
		return false;
	}
	
	public static function calculatePostPage($post)
	{
		global $DB, $config, $CORE;
		
		//Detect if we need to include deleted posts
		$IncludeDeleted = (($CORE->user->isOnline() && $CORE->user->hasPermission(PERMISSION_FORUMS_VIEW_DELETED_POSTS)) ? true : false);
		
		$res = $DB->prepare("SELECT COUNT(*) AS count FROM `wcf_posts` WHERE `topic` = (SELECT `topic` FROM `wcf_posts` WHERE `id` = :post LIMIT 1) AND `id` <= :post ".(!$IncludeDeleted ? " AND `deleted_by` = '0'" : '')." ORDER BY `id` ASC;");
		$res->bindParam(':post', $post, PDO::PARAM_INT);
		$res->execute();
		
		//fetch
		$row = $res->fetch();
		
		//re-variable
		$position = $row['count'];
		
		//free mem
		unset($res, $row);
		
		return ($position > $config['FORUM']['Posts_Limit'] ? ceil($position / $config['FORUM']['Posts_Limit']) : 0);
	}
	
	###########################################
	###### TOPIC FUNCTIONS ####################
	
	static public function verifyTopicId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getTopicsCount($forum, $includeSticky = true)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_topics` WHERE `forum` = :forum".(!$includeSticky ? " AND `sticky` = 0" : "").";");
		$res->bindParam(':forum', $forum, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
    }

    static public function getUserTopicsCount($userId)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_topics` WHERE `author` = :userId;");
		$res->bindParam(':userId', $userId, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
	}
	
	static public function getTopicInfo($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id`, `forum`, `name`, `added`, `author`, `locked` FROM `wcf_topics` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			return $row;
		}
		
		return false;
	}
	
	static public function getTopicLastPost($topic)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id`, `added`, `author` FROM `wcf_posts` WHERE `topic` = :id AND `deleted_by` = '0' ORDER BY `added` DESC LIMIT 1;");
		$res->bindParam(':id', $topic, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			//Post page number
			$row['page_number'] = self::calculatePostPage($row['id']);
			
			return $row;
		}
		
		return false;
    }
    
    static public function getForumPostsCount($forumId)
    {
        global $DB;

        $res = $DB->prepare("SELECT COUNT(p.`id`) AS posts
                            FROM `wcf_forums` AS f
                            LEFT JOIN `wcf_topics` AS t ON f.`id` = t.`forum` 
                            LEFT JOIN `wcf_posts` AS p ON t.`id` = p.`topic`
                            WHERE f.`id` = :id;");
        $res->bindParam(':id', $forumId, PDO::PARAM_INT);
		$res->execute();
		
        $count_row = $res->fetch(PDO::FETCH_NUM);
        
        return $count_row[0];
    }

    static public function getUserPostsCount($userId)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_posts` WHERE `author` = :id AND `deleted_by` = '0';");
		$res->bindParam(':id', $userId, PDO::PARAM_INT);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
    }
    
    static public function getUserLatestPosts($userId, $limit = 5)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id`, `added`, `topic`, `title`, `text` FROM `wcf_posts` WHERE `author` = :id AND `deleted_by` = '0' ORDER BY `added` DESC LIMIT ".$limit.";");
		$res->bindParam(':id', $userId, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			return $res->fetchAll();
		}
		
		return false;
    }
    
    ###########################################
    ###### SEARCH FUNCTIONS ###################
    
    static public function getExcludeTopicsBasedOnRoles($roles)
	{
		global $DB;
        
        $excludeForums = array();
        $checkRoles = array();

        // Collect role ids
        foreach ($roles as $role) $checkRoles[] = $role->getId();

        $res = $DB->prepare("SELECT `id`, `view_roles` FROM `wcf_forums`;");
        $res->bindParam(':id', $category['id'], PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() > 0)
        {
            while ($forum = $res->fetch())
            {
                // Check the view roles
                $view_roles = explode(',', $forum['view_roles']);
                
                // Check for any role
                if (in_array(0, $view_roles))
                    continue;

                $match = false;

                // Check if the forum matches any input role
                foreach ($view_roles as $roleId)
                {
                    if (in_array($roleId, $checkRoles))
                    {
                        $match = true;
                        break;
                    }
                }

                // If no roles was matched, then this forum is excluded
                if (!$match)
                    $excludeForums[] = (int)$forum['id'];
            }
        }

        $excludeTopics = array();

        if (!empty($excludeForums))
        {
            // Collect the exclude topic ids
            $forumIdsString = implode(',', $excludeForums);
            $res = $DB->query("SELECT `id` FROM `wcf_topics` WHERE `forum` IN (".$forumIdsString.");");    
            if ($res->rowCount() > 0)
            {  
                $topics = $res->fetchAll();
                foreach ($topics as $topic) $excludeTopics[] = $topic['id'];
            }
        }

        return $excludeTopics;
    }

    static public function getSearchPostsCount($string, $excludeTopics = 0)
	{
		global $DB;
        
        if (is_array($excludeTopics) && !empty($excludeTopics))
            $excludeTopics = implode(',', $excludeTopics);
        else if (is_array($excludeTopics) && empty($excludeTopics))
            $excludeTopics = 0;

		$res = $DB->prepare("SELECT COUNT(*) FROM `wcf_posts` WHERE `text` LIKE CONCAT('%', :string, '%') AND `topic` NOT IN (".$excludeTopics.");");
		$res->bindParam(':string', $string, PDO::PARAM_STR);
		$res->execute();
		
		$count_row = $res->fetch(PDO::FETCH_NUM);
		
		return $count_row[0];
    }

    static public function getSearchPosts($string, $limit, $excludeTopics = 0)
	{
		global $DB;
        
        if (is_array($excludeTopics) && !empty($excludeTopics))
            $excludeTopics = implode(',', $excludeTopics);
        else if (is_array($excludeTopics) && empty($excludeTopics))
            $excludeTopics = 0;
            
		$res = $DB->prepare("SELECT `wcf_posts`.*, `wcf_topics`.`name` AS `topic_name`, `wcf_topics`.`forum` AS `forum_id` 
                            FROM `wcf_posts` 
                            LEFT JOIN `wcf_topics` ON `wcf_topics`.`id` = `wcf_posts`.`topic` 
                            WHERE `wcf_posts`.`text` LIKE CONCAT('%', :string, '%') AND `wcf_posts`.`topic` NOT IN (".$excludeTopics.") 
                            ORDER BY `wcf_posts`.`id` DESC LIMIT ".$limit.";");
		$res->bindParam(':string', $string, PDO::PARAM_STR);
		$res->execute();
		
		return $res->fetchAll(PDO::FETCH_ASSOC);
    }
	
	###########################################
	###### FORUM FUNCTIONS ####################
	
	static public function getAuthorById($id)
	{
		global $DB;
		
		$author_res = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$author_res->bindParam(':acc', $id, PDO::PARAM_INT);
		$author_res->execute();
		
		if ($author_res->rowCount() > 0)
		{
			$author_row = $author_res->fetch();
			
			return $author_row['displayName'];
		}
		
		return false;
	}
	
	static public function verifyForumId($id)
	{
		global $DB;
		
		$res = $DB->prepare("SELECT `id` FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		return ($res->rowCount() > 0) ? true : false;
	}
	
	static public function getForumInfo($id)
	{
		global $DB;
		
		//Find the parent forum
		$forum_res = $DB->prepare("SELECT * FROM `wcf_forums` WHERE `id` = :id LIMIT 1;");
		$forum_res->bindParam(':id', $id, PDO::PARAM_INT);
		$forum_res->execute();
		
		if ($forum_res->rowCount() > 0)
		{
			return $forum_res->fetch();
		}
		
		return false;
	}
	
	static public function getForumLastTopic($forum)
	{
		global $DB, $config;
		
		$res = $DB->prepare("SELECT `id`, `name`, `added`, `author` FROM `wcf_topics` WHERE `forum` = :id ORDER BY `added` DESC LIMIT 1;");
		$res->bindParam(':id', $forum, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$row = $res->fetch();
			
			//Get the author string
			if ($author = self::getAuthorById($row['author']))
			{
				$row['author_str'] = $author;
			}
			else
			{
				$row['author_str'] = 'Unknown';
			}
			unset($author);
			
			//format the time
			$row['added'] = date('D M j, Y, h:i a', strtotime($row['added']));
			
			return $row;
		}
		
		return false;
	}
	
	###########################################
	###### MISC FUNCTIONS #####################
    
    static public function parsePostText($id, $text)
    {
        global $CORE, $CACHE;

        $cached = $CACHE->get('forums/posts/post_' . $id);

        if ($cached !== false)
        {
            return $cached;
        }

        $CORE->loadLibrary('forums.parser');

        // create the BBCode parser
        $parser = new SBBCodeParser_Document(true, false);

        //Strip slashes
        $text = stripslashes($text);

        //Parse
        $text = $parser->parse($text)->detect_links()->detect_emails()->detect_emoticons()->get_html(true);

        //fix multiple break lines
        $text = preg_replace("/<br\s*\/?>\s<br\s*\/?>\s+/", "<br/>", $text);
        
        //remove all break lines inside lists and such
        $search = array("/(?<=<\/ol>)<br[\s|\/|\>]+\>*?/is", "/(?<=<\/ul>)<br[\s|\/|\>]+\>*?/is");
        $text = preg_replace($search, "", $text);

        unset($parser);
        
        //Store the parsed post in the cache for a month
        $CACHE->store('forums/posts/post_' . $id, $text, 2592000);

        return $text;
    }

	static public function getAuthorInfo($id)
	{
		global $DB;
		
		$author_res = $DB->prepare("SELECT `id`, `displayName`, `rank`, `avatar`, `avatarType`, `gender`, `country` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$author_res->bindParam(':acc', $id, PDO::PARAM_INT);
		$author_res->execute();
		
		if ($author_res->rowCount() > 0)
		{
			return $author_res->fetch();
		}
		
		return false;
	}
	
	static public function getCategoryName($id)
	{
		global $DB;
		
		//Find the category name
		$res = $DB->prepare("SELECT `name` FROM `wcf_categories` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$catRow = $res->fetch();
			
			return $catRow['name'];
		}
		
		return false;
    }
    
    static public function getCategoryInfo($id)
	{
		global $DB;
		
		//Find the category name
		$res = $DB->prepare("SELECT `name`, `position`, `flags` FROM `wcf_categories` WHERE `id` = :id LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			return $res->fetch();
		}
		
		return false;
	}
	
	static public function SetupNotification($text)
	{
		global $CORE;
		
		//Setup our notification
		$CORE->notifications->SetTitle('Alert!');
		$CORE->notifications->SetHeadline('An error occured!');
		$CORE->notifications->SetText($text);
		$CORE->notifications->SetTextAlign('center');
		$CORE->notifications->SetAutoContinue(true);
		$CORE->notifications->SetContinueDelay(4);
		$CORE->notifications->Apply();
	}
}