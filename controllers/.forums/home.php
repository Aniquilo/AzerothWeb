<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/forums_controller.php';

class Home extends Forums_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $category = isset($_GET['category']) ? (int)$_GET['category'] : false;

        // If we are looking for a specific category
        if ($category)
        {
            $res = $this->db->prepare("SELECT * FROM `wcf_categories` WHERE `id` = :cat ORDER BY `position` ASC;");
            $res->bindParam(':cat', $category, PDO::PARAM_INT);
            $res->execute();
        }
        else
        {
            $res = $this->db->prepare("SELECT * FROM `wcf_categories` ORDER BY `position` ASC;");
            $res->execute();
        }

        unset($category);

        // Fet the categories
        $categories = false;

        if ($res->rowCount() > 0)
        {
            $categories = $res->fetchAll();

            // Get the forums of each category
            foreach ($categories as $i => $category)
            {
                $res2 = $this->db->prepare("SELECT * FROM `wcf_forums` WHERE `category` = :id ORDER BY `position` ASC;");
                $res2->bindParam(':id', $category['id'], PDO::PARAM_INT);
                $res2->execute();

                $categories[$i]['forums'] = false;

                //Check if we have any forums in this category
                if ($res2->rowCount() > 0)
                {
                    $categories[$i]['forums'] = array();

                    while ($forum = $res2->fetch())
                    {
                        // Check the view roles
                        $view_roles = explode(',', $forum['view_roles']);
                        
                        if (!in_array(0, $view_roles) && !$this->user->hasAnyOfRoles($view_roles))
                            continue;
                        
                        $categories[$i]['forums'][] = $forum;
                    }
                }
            }

            unset($category);
        }

        unset($res);

        //Set the title
        $this->tpl->SetTitle('Forums');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/home', array(
            'categories' => $categories
        ));

        $this->tpl->LoadFooter();
    }
}