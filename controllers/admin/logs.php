<?php 
if (!defined('init_engine'))
{
    header('HTTP/1.0 404 not found');
    exit;
}

require_once ROOTPATH . '/engine/admin_controller.php';

class Logs extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->CheckPermission(PERMISSION_LOGS);
    }

    public function index()
    {
        //Print the page
        $this->PrintPage('logs/paypal');
    }

    public function paymentwall()
    {
        //Print the page
        $this->PrintPage('logs/paymentwall');
    }

    public function store()
    {
        $vars = array(
            'type' => 'store',
            'title' => 'Store Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function armorsets()
    {
        $vars = array(
            'type' => 'armorsets',
            'title' => 'Armor Sets Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function levels()
    {
        $vars = array(
            'type' => 'levels',
            'title' => 'Character Level Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function racechange()
    {
        $vars = array(
            'type' => 'racechange',
            'title' => 'Race Change Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function factionchange()
    {
        $vars = array(
            'type' => 'factionchange',
            'title' => 'Faction Change Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function customization()
    {
        $vars = array(
            'type' => 'customization',
            'title' => 'Character Customization Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function gamegold()
    {
        $vars = array(
            'type' => 'gamegold',
            'title' => 'In-Game Gold Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }

    public function boosts()
    {
        $vars = array(
            'type' => 'boosts',
            'title' => 'Boosts Purchase Logs'
        );

        //Print the page
        $this->PrintPage('logs/logs', $vars);
    }
}