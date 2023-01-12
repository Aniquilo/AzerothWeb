<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Articles extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }

    public function index()
    {
        $p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

        $this->loadLibrary('articles.base');
        //load the pagination lib
        $this->loadLibrary('paginationType2');

        //Let's setup our pagination
        $pagination = new Pagination();
        $pages = false;
        $perPage = 8;
        $results = false;

        //count the total records
        $count = ArticlesLib::getArticlesCount();
                    
        unset($count_row);
        unset($res);

        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($count, $perPage, $p);
            
            //get the activity records
            $res = $this->db->prepare("SELECT * FROM `articles` ORDER BY `id` DESC LIMIT ".$pages['limit'].";");
            $res->execute();
            
            $results = $res->fetchAll();

            if ($count <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle('Articles');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->AddCSS('template/style/page-articles.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('articles/articles', array(
            'count' => $count,
            'pages' => $pages,
            'results' => $results
        ));

        $this->tpl->LoadFooter();
    }

    public function view()
    {
        //load the pagination lib
        $this->loadLibrary('articles.base');
        $this->loadLibrary('paginationType2');

        $ArticleID = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $page = (isset($_GET['p']) ? (int)$_GET['p'] : 1);
        
        //Make sure we have article id
        if (!$ArticleID)
        {
            $this->tpl->Message('Articles', 'An error has occured!', 'The selected article seems to be invalid.');
        }
        
        //Try to find the article record
        $res = $this->db->prepare("SELECT * FROM `articles` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $ArticleID, PDO::PARAM_INT);
        $res->execute();
        
        //Verify the record is found
        if ($res->rowCount() == 0)
        {
            $this->tpl->Message('Articles', 'An error has occured!', 'The selected article seems to be invalid.');
        }
        
        //Fetch the record
        $row = $res->fetch();
        
        //format the title
        $row['title'] = ArticlesLib::parseTitle($row['title']);
        
        // Check if this session has viewed this article
        if (!isset($_SESSION['articleview'][$row['id']]))
        {
            //Register a view of the article
            ArticlesLib::RegisterView($row['id']);

            //Update runtime views
            $row['views']++;

            //Set as viewed
            $_SESSION['articleview'][$row['id']] = true;
        }

        // Prepare comments
        $pagination = new Pagination();
        $pagination->addToLink('?id='.$ArticleID);

        $pages = false;
        $perPage = 10;
        $comments = false;
        $commentsCount = ArticlesLib::getCommentsCount($ArticleID);
        
        //Pull the comments
        if ($commentsCount > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($commentsCount, $perPage, $page);
            
            //get the activity records
            $res = $this->db->prepare("SELECT 
                                    `article_comments`.`id`, 
                                    `article_comments`.`added`, 
                                    `article_comments`.`author`, 
                                    `article_comments`.`article`, 
                                    `article_comments`.`text`, 
                                    `account_data`.`displayName` AS `author_str` 
                                FROM `article_comments` 
                                LEFT JOIN `account_data` ON `account_data`.`id` = `article_comments`.`author` 
                                WHERE `article_comments`.`article` = :id 
                                ORDER BY `article_comments`.`id` DESC 
                                LIMIT ".$pages['limit'].";");
            $res->bindParam(':id', $ArticleID, PDO::PARAM_INT);
            $res->execute();

            $comments = $res->fetchAll();

            if ($commentsCount <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle($row['title']);
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->AddCSS('template/style/page-articles.css?v=1');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('articles/article', array(
            'ArticleID' => $ArticleID, 
            'row' => $row,
            'commentsCount' => $commentsCount,
            'comments' => $comments, 
            'perPage' => $perPage,
            'pages' => $pages,
        ));

        $this->tpl->AddFooterJs('template/js/humanized.time.js');
        $this->tpl->AddFooterJs('template/js/page.article.js?v=2');
        $this->tpl->LoadFooter();
    }

    public function post_comment()
    {
        header('Content-type: text/json');

        if (!$this->user->isOnline())
        {
            echo '{"error": "You must be logged in to comment."}';
            die;
        }

        //Get the text var
        $text = ((isset($_POST['text'])) ? $_POST['text'] : false);
        
        //Get the article id
        $article = (isset($_POST['article']) ? (int)$_POST['article'] : false);

        if (!$text)
        {
            echo '{"error": "Please enter comment text."}';
            die;
        }
        
        if (!$article)
        {
            echo '{"error": "Invalid or missing article."}';
            die;
        }

        //Validate the article record
        $res = $this->db->prepare("SELECT `comments` FROM `articles` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $article, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo '{"error": "Invalid or missing article."}';
            die;
        }

        //Fetch the article record
        $row = $res->fetch();

        //free mem
        unset($res);

        //Check if the article has comments enabled
        if ($row['comments'] == '0')
        {
            echo '{"error": "The comments on this article have been disabled."}';
            die;
        }

        //Get the time
        $time = $this->getTime();
        $userId = $this->user->get('id');

        //Let's insert the comment
        $insert = $this->db->prepare("INSERT INTO `article_comments` (`text`, `added`, `author`, `article`) VALUES (:text, :added, :acc, :article);");
        $insert->bindParam(':text', $text, PDO::PARAM_STR);
        $insert->bindParam(':added', $time, PDO::PARAM_STR);
        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
        $insert->bindParam(':article', $article, PDO::PARAM_INT);
        $insert->execute();

        if ($insert->rowCount() > 0)
        {
            echo json_encode(array(
                'id'			=> $this->db->lastInsertId(),
                'text' 			=> strip_tags(stripslashes($text)),
                'added' 		=> $time,
                'author' 		=> $userId,
                'author_str'	=> $this->user->get('displayName'),
                'article'		=> $article
            ));
        }
        else
        {
            echo '{"error": "The website failed to insert your comment."}';
        }
        exit;
    }

    public function get_comments()
    {
        header('Content-type: text/json');

        $perPage = 10;

        //Get the article id
        $article = (isset($_GET['article']) ? (int)$_GET['article'] : false);
        $lastComment = (isset($_GET['last_comment']) ? (int)$_GET['last_comment'] : 0);

        if (!$article)
        {
            echo '{"error": "Invalid or missing article."}';
            die;
        }

        //Make new Array
        $data = array(
            'count'		=> 0,
            'comments'	=> array()
        );

        //Pull the records since the last id
        $res = $this->db->prepare("SELECT
                                `article_comments`.`id`, 
                                `article_comments`.`added`, 
                                `article_comments`.`author`, 
                                `article_comments`.`article`, 
                                `article_comments`.`text`, 
                                `account_data`.`displayName` AS `author_str` 
                            FROM `article_comments` 
                            LEFT JOIN `account_data` ON `account_data`.`id` = `article_comments`.`author` 
                            WHERE `article_comments`.`article` = :article AND `article_comments`.`id` > :last ORDER BY `article_comments`.`id` ASC LIMIT :limit;");
        $res->bindParam(':article', $article, PDO::PARAM_INT);
        $res->bindParam(':last', $lastComment, PDO::PARAM_INT);
        $res->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $res->execute();

        //save the count
        $data['count'] = $res->rowCount();

        //save the comments
        while ($arr = $res->fetch())
        {
            $data['comments'][] = $arr;
        }
        unset($arr, $res);

        echo json_encode($data);
        exit;
    }

    public function delete_comment()
    {
        header('Content-type: text/json');

        if (!$this->user->isOnline())
        {
            echo '{"error": "You must be logged in."}';
            die;
        }

        // Get the comment id
        $commentId = (isset($_POST['id']) ? (int)$_POST['id'] : false);

        if (!$commentId)
        {
            echo '{"error": "No comment id."}';
            die;
        }

        // Get comment record
        $res = $this->db->prepare("SELECT * FROM `article_comments` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $commentId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo '{"error": "Invalid or missing comment id."}';
            die;
        }

        //Fetch the article record
        $row = $res->fetch();

        //free mem
        unset($res);

        // Check for comment owner or permission
        if ((int)$this->user->get('id') != (int)$row['author'] && !$this->user->hasPermission(PERMISSION_ARTICLE_COMMENT_DELETE))
        {
            echo '{"error": "You cannot delete this comment."}';
            die;
        }

        //Delete the comment
        $delete = $this->db->prepare("DELETE FROM `article_comments` WHERE `id` = :id;");
        $delete->bindParam(':id', $commentId, PDO::PARAM_INT);
        $delete->execute();

        if ($delete->rowCount() > 0)
        {
            echo '{"success": true}';
        }
        else
        {
            echo '{"error": "The website failed to delete the comment."}';
        }
        exit;
    }

    public function update_comment()
    {
        header('Content-type: text/json');

        if (!$this->user->isOnline())
        {
            echo '{"error": "You must be logged in."}';
            die;
        }

        // Get the comment id
        $commentId = (isset($_POST['id']) ? (int)$_POST['id'] : false);
        
        if (!$commentId)
        {
            echo '{"error": "No comment id."}';
            die;
        }

        //Get the text var
        $text = ((isset($_POST['text'])) ? $_POST['text'] : false);

        if (!$text)
        {
            echo '{"error": "Please enter comment text."}';
            die;
        }

        // Get comment record
        $res = $this->db->prepare("SELECT * FROM `article_comments` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $commentId, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
        {
            echo '{"error": "Invalid or missing comment id."}';
            die;
        }

        //Fetch the article record
        $row = $res->fetch();

        //free mem
        unset($res);

        // Check for comment owner or permission
        if ((int)$this->user->get('id') != (int)$row['author'])
        {
            echo '{"error": "You cannot edit this comment."}';
            die;
        }

        //Let's update the comment
        $update = $this->db->prepare("UPDATE `article_comments` SET `text` = :text WHERE `id` = :id LIMIT 1;");
        $update->bindParam(':text', $text, PDO::PARAM_STR);
        $update->bindParam(':id', $commentId, PDO::PARAM_INT);
        $update->execute();

        echo json_encode(array(
            'text' => strip_tags(stripslashes($text)),
        ));
        exit;
    }
}