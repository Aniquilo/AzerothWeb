<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Home extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
		$RealmId = $this->user->GetRealmId();
		        //Find the active boosts for this account/realm
        if ($RealmDb = $this->realms->getRealm($RealmId)->getCharactersConnection())
        {
        

            $userId = $this->user->get('id');
            
            //locate the records for this account if any
            $res = $RealmDb->prepare("SELECT * FROM `player_boosts` WHERE `account` = :acc AND `active` = '1' ORDER BY `unsetdate` ASC;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                while ($arr = $res->fetch())
                {
                    //verify that this boost is really active
                    $time = $this->getTime(true);
					$testeur = $arr['boosts'];
                    
                    if ($time->getTimestamp() > (int)$arr['unsetdate'])
                    {
			if($testeur == 5){
				
			
			//Give the rank VIP
            $update = $this->db->prepare("UPDATE `account_data` SET `rank` = '1' WHERE `id` = :acc LIMIT 1;");
            $update->bindParam(':acc', $userId, PDO::PARAM_INT);
            $update->execute();
			}
                        //already expired
                        continue;
                    }
                    unset($time);
                    
                    //push to the active boosts
                    $ActiveBoosts[] = $arr;
                }
                unset($arr);
            }
            unset($res);
        }
        unset($RealmDb);
        //Set template parameters
        $this->tpl->SetParameters(array(
            'title'		=> lang('title', 'home'),
            'slider'	=> true,
            'topbar'	=> true
        ));

        //Print the header
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('home');

        //Add some javascripts to the loader
        $this->tpl->AddFooterJs('template/js/page.homepage.js');
        $this->tpl->AddFooterJs('template/js/shadowbox.js');
        $this->tpl->AddFooterJs('template/js/init.custom.shadowbox.js');

        //print the footer
        $this->tpl->LoadFooter();
    }
}