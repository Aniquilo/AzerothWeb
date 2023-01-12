<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class News extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

        //load the pagination lib
        $this->loadLibrary('paginationType2');

        //Let's setup our pagination
        $pagination = new Pagination();
        $pages = false;
        $perPage = 8;
        $results = false;

        //count the total records
        $res = $this->db->prepare("SELECT COUNT(*) FROM `news`;");
        $res->execute();

        $count_row = $res->fetch(PDO::FETCH_NUM);
        $count = $count_row[0];
                    
        unset($count_row);
        unset($res);

        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagination->calculate_pages($count, $perPage, $p);
            
            //get the activity records
            $res = $this->db->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT ".$pages['limit'].";");
            $res->execute();
            
            $results = $res->fetchAll();

            if ($count <= $perPage) $pages = false;
        }

        $this->tpl->SetTitle('News');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->AddCSS('template/style/page-articles.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('news/news', array(
            'count' => $count,
            'pages' => $pages,
            'results' => $results
        ));

        $this->tpl->LoadFooter();
    }

    public function view()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;
        
        //Make sure we have news id
        if (!$id)
        {
            $this->tpl->Message('News', 'An error has occured!', 'The selected news article seems to be invalid.');
        }
        
        //Try to find the article record
        $res = $this->db->prepare("SELECT * FROM `news` WHERE `id` = :id LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();
        
        //Verify the record is found
        if ($res->rowCount() == 0)
        {
            $this->tpl->Message('News', 'An error has occured!', 'The selected news article seems to be invalid.');
        }
        
        //Fetch the record
        $row = $res->fetch();
        
        //format the title
        $row['title'] = htmlspecialchars(stripslashes($row['title']));
        
        //Get the next news record
        $res = $this->db->prepare("SELECT `id` FROM `news` WHERE `id` > :id ORDER BY `id` ASC;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
		$res->execute();
        $next = ($res->rowCount() > 0 ? $res->fetch() : false);
        
        $this->tpl->SetTitle($row['title']);
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->AddCSS('template/style/page-articles.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('news/view', array(
            'id' => $id, 
            'row' => $row,
            'next' => $next
        ));

        $this->tpl->AddFooterJs('template/js/humanized.time.js');
        $this->tpl->LoadFooter();
    }
}