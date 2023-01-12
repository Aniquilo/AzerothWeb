<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Home extends Admin_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $visitors = array();

        $today = strtotime(date('Y-m-d'));
        $day = strtotime('-10 days');
        while ($day <= $today)
        {
            $day = strtotime('+1 day', $day);
            $key = date('m-d', $day);
            $visitors[$key] = array(
                'visitors' => 0,
                'pageviews' => 0,
                'text' => $key . ' ' . date('F', $day)
            );
        }

        $res = $this->db->prepare("SELECT * FROM `site_visitors` WHERE `access_date` BETWEEN ? AND ?;");
        $res->execute(array(
            date('Y-m-d 00:00:01', strtotime('-10 days')),
            date('Y-m-d 23:59:59')
        ));

        if ($res->rowCount() > 0)
        {
            $results = $res->fetchAll();

            foreach ($results as $visitor)
            {
                $key = date('m-d', strtotime($visitor['access_date']));
                if (!isset($visitors[$key]))
                    continue;
                $visitors[$key]['visitors']++;
                $visitors[$key]['pageviews'] += (int)$visitor['page_views'];
            }
        }

        //Print the page view
        $this->PrintPage('home', array(
            'visitors' => $visitors
        ));
    }
}