<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Vote extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();

        $this->loggedInOrReturn();

        $this->loadLibrary('accounts.activity');
    }
    
    public function index()
    {
        //Set the title
        $this->tpl->SetTitle('Vote for us');
        $this->tpl->SetSubtitle(lang('title', 'account'));
        $this->tpl->AddCSS('template/style/page-vote.css');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('vote');

        $this->tpl->LoadFooter();
    }

    public function process()
    {
        $this->loadLibrary('coin.activity');

        $siteid = (isset($_GET['site']) ? (int)$_GET['site'] : false);

        //get the cooldown on this website
        $cooldown = $this->user->getCooldown('votingsite'.$siteid);
        $cooldownTime = $this->config['VOTE']['Cooldown'];

        //points per vote ?
        $pointsPerVote = $this->config['VOTE']['PPV'];

        //points reward on each 5 referral votes
        $rafPointsReward = $this->config['VOTE']['RAF_PR'];

        //ip check?
        $ipCheck = $this->config['VOTE']['IP_CHECK'];

        //vote sites data
        $VoteData = new VoteSitesData();

        //prepare multi errors
        $this->errors->NewInstance('vote');

        //bind the onsuccess message
        $this->errors->onSuccess('Congratulation, you have recieved '.$pointsPerVote.' Silver coins.', '/vote');

        if (!$siteid)
        {
            $this->errors->Add("Please select a valid voting website.");
        }
        if (!$voteSitesData = $VoteData->get($siteid))
        {
            $this->errors->Add("Please select a valid voting website.");
        }
        unset($VoteData);

        //check the cooldown
        if (time() < $cooldown)
        {
            $this->errors->Add("The voting website is on cooldown.");
        }

        $this->errors->Check('/vote');

        if ($ipCheck == true)
        {
            $IPcooldown = $this->user->getVoteIPCooldown($siteid);
        }

        $userId = $this->user->get('id');
        $timestamp = $this->getTime();
        $ip = $this->security->getip();

        //add new record so we could later have statistics per month
        $insert = $this->db->prepare("INSERT INTO `vote_data` (`account`, `siteid`, `timestamp`, `ip`) VALUES (:acc, :site, :time, :ip);");
        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
        $insert->bindParam(':site', $siteid, PDO::PARAM_INT);
        $insert->bindParam(':time', $timestamp, PDO::PARAM_STR);
        $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
        $insert->execute();	
        unset($insert);
        
        //Update counter
        $year = date('Y');
        $month = date('n');
        
        $insert = $this->db->prepare("INSERT IGNORE INTO `votecounter` (`account`, `year`, `month`) VALUES (:acc, :year, :month);");
        $insert->bindParam(':acc', $userId, PDO::PARAM_INT);
        $insert->bindParam(':year', $year, PDO::PARAM_INT);
        $insert->bindParam(':month', $month, PDO::PARAM_INT);
        $insert->execute();
        unset($insert);
        
        $update = $this->db->prepare("UPDATE `votecounter` SET `counter` = `counter` + 1 WHERE `account` = :acc AND `year` = :year AND `month` = :month LIMIT 1;");
        $update->bindParam(':acc', $userId, PDO::PARAM_INT);
        $update->bindParam(':year', $year, PDO::PARAM_INT);
        $update->bindParam(':month', $month, PDO::PARAM_INT);
        $update->execute();
        unset($update);

        //check if we have active recruiter link
        if ($this->user->getRecruiterLinkState() == RAF_LINK_ACTIVE)
        {
            //if we have a recruiter link we should start counting the votes aiming 5 votes reward
            //to reward the recruiter every 5 votes without having to save the count each vote
            //we can simply get the total votes made by this account and try to devide em by 5
            
            //get the count
            $res = $this->db->prepare("SELECT COUNT(*) FROM `vote_data` WHERE `account` = :acc;");
            $res->bindParam(':acc', $userId, PDO::PARAM_INT);
            $res->execute();
            $count_row = $res->fetch(PDO::FETCH_NUM);
            $count = $count_row[0];
            unset($count_row);
            unset($res);
            
            //now let's reward the recruiter if it's time
            if ((int)$count > 0 && ((int)$count % 5) == 0)
            {
                $recruiter = $this->user->get('recruiter');

                //update the recruiter points
                $update = $this->db->prepare("UPDATE `account_data` SET `silver` = silver + :points WHERE `id` = :acc LIMIT 1;");
                $update->bindParam(':acc', $recruiter, PDO::PARAM_INT);
                $update->bindParam(':points', $rafPointsReward, PDO::PARAM_INT);
                $update->execute();
                
                //check if the points ware updated
                if ($update->rowCount() > 0)
                {
                    //log into coin activity
                    $ca = new CoinActivity($this->user->get('recruiter'));
                    $ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
                    $ca->set_SourceString('Referral 5 votes reward');
                    $ca->set_CoinsType(CA_COIN_TYPE_SILVER);
                    $ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
                    $ca->set_Amount($rafPointsReward);
                    $ca->execute();
                    unset($ca);
                }
                unset($update);
            }
        }
        
        if ($ipCheck == true && time() < $IPcooldown)
        {
            //set the cooldown
            $this->user->setCooldown('votingsite'.$siteid, strtotime('+'.$cooldownTime));
            $this->errors->Add("The website failed to update your Silver coins. Reason: Someone has already voted from this IP.");
        }
        else
        {
            //update the user points
            $update = $this->db->prepare("UPDATE `account_data` SET `silver` = silver + :points WHERE `id` = :acc LIMIT 1;");
            $update->bindParam(':acc', $userId, PDO::PARAM_INT);
            $update->bindParam(':points', $pointsPerVote, PDO::PARAM_INT);
            $update->execute();
                
            //check if the points ware updated
            if ($update->rowCount() > 0)
            {
                AccountActivity::Insert('Voted on <b>' . $voteSitesData['name'] . '</b>');

                //log into coin activity
                $ca = new CoinActivity();
                $ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
                $ca->set_SourceString($voteSitesData['name'] . ' Vote');
                $ca->set_CoinsType(CA_COIN_TYPE_SILVER);
                $ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
                $ca->set_Amount($pointsPerVote);
                $ca->execute();
                unset($ca);
        
                //set the cooldown
                $this->user->setCooldown('votingsite'.$siteid, strtotime('+'.$cooldownTime));
                $this->errors->triggerSuccess();
            }
            else
            {
                $this->errors->Add("The website failed to update your Silver coins.");
            }
            unset($update);
        }

        $this->errors->Check('/vote');
        exit;
    }
}