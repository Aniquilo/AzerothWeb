<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Recruit extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loggedInOrReturn();

        $this->tpl->SetTitle('Recruit a Friend');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-recruit-a-friend.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('recruit');

        $this->tpl->AddFooterJs('template/js/page.recruit.a.friend.js');
        $this->tpl->LoadFooter();
    }

    public function update_links()
    {
        header('Content-type: text/json');

        if (!$this->user->isOnline())
        {
            echo json_encode(array('error' => 'Not logged in!'));
            die;
        }

        //load the raf lib
        $this->loadLibrary('raf');

        //setup the raf class
        $raf = new RAF();

        //cooldowns
        $cooldown = $this->user->getCooldown('RAF_REF_UP');
        $cooldownTime = '15 minutes';

        //check the cooldown, we dont want users to spamm our databases
        if (!$cooldown or time() > $cooldown)
        {
            if ($res = $raf->GetPendingLinks($this->user->get('id')))
            {
                while ($arr = $res->fetch())
                {
                    //define that we have not found a character yet
                    $found = false;

                    //define that we have not met the requirements for the status change
                    $requirementsMet = false;

                    //save the highest found character info
                    $highestLevel = 0;
                    $highestText = '';

                    //find the hightest level character in all the realms
                    //loop the realms
                    foreach ($this->getRealmsConfig() as $RealmId => $RealmData)
                    {
                        $realm = $this->realms->getRealm($RealmId);

                        //check if the characters database is reachable
                        if ($realm->checkCharactersConnection())
                        {
                            //now find it
                            if ($charRow = $realm->getCharacters()->FindHightestLevelCharacter($arr['account']))
                            {
                                $found = true;

                                //check if the character meets the requirements
                                if ($charRow['class'] == 6)
                                {
                                    //if the character is DK
                                    if ($charRow['level'] >= 80)
                                    {
                                        //the character meets the requirements
                                        $requirementsMet = true;
                                    }
                                }
                                else
                                {
                                    //any other class than DK
                                    if ($charRow['level'] >= 60)
                                    {
                                        //the character meets the requirements
                                        $requirementsMet = true;
                                    }
                                }
                                
                                //if the character meet's the requirements
                                if ($requirementsMet)
                                {
                                    //update the status and statusText
                                    $statusText = '<b>'.$charRow['name'].'</b> '.$this->realms->getClassString($charRow['class']).' Level '.$charRow['level'];
                                    $status = RAF_LINK_ACTIVE;
                                    $time = $this->getTime();

                                    //query
                                    $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text, `status` = :status, `cDate` = :time WHERE `id` = :id LIMIT 1;");
                                    $update->bindParam(':id', $arr['id'], PDO::PARAM_INT);
                                    $update->bindParam(':text', $statusText, PDO::PARAM_STR);
                                    $update->bindParam(':status', $status, PDO::PARAM_INT);
                                    $update->bindParam(':time', $time, PDO::PARAM_STR);
                                    $update->execute();
                                    unset($update);
                                    
                                    //break the realm loop for this referral
                                    break 1;
                                }
                                
                                if ($highestLevel < (int)$charRow['level'])
                                {
                                    $highestLevel = (int)$charRow['level'];
                                    $highestText = '<b>'.$charRow['name'].'</b> '.$this->realms->getClassString($charRow['class']).' Level '.$charRow['level'];
                                }
                            }
                            unset($charRow);
                        }
                    } //end of the realms loop
                    
                    //if we found a character but not high enough level
                    if ($found && !$requirementsMet)
                    {
                        $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text WHERE `id` = :id LIMIT 1;");
                        $update->bindParam(':id', $arr['id'], PDO::PARAM_INT);
                        $update->bindParam(':text', $highestText, PDO::PARAM_STR);
                        $update->execute();
                        unset($update);
                    }

                    //if we had no characters for this referral update the status text
                    if (!$found)
                    {
                        $statusText = 'No character was found';
                        $update = $this->db->prepare("UPDATE `raf_links` SET `statusText` = :text WHERE `id` = :id LIMIT 1;");
                        $update->bindParam(':id', $arr['id'], PDO::PARAM_INT);
                        $update->bindParam(':text', $statusText, PDO::PARAM_STR);
                        $update->execute();
                        unset($update);
                    }
                    unset($found);
                    unset($requirementsMet);
                }
                
                //set a cooldown on this update
                $this->user->setCooldown('RAF_REF_UP', strtotime('+'.$cooldownTime));
                
                // End of the script
                echo json_encode(array('status' => 'OK'));
                die;
            }
            else
            {
                echo json_encode(array('error' => 'You have no pending referrals.'));
                die;
            }
        }
        else
        {
            echo json_encode(array('error' => 'This function is on cooldown, please try again later.'));
            die;
        }
        exit;
    }
}