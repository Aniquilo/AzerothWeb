<?php
//define the root path here
define('ROOTPATH', __DIR__);

require ROOTPATH . '/engine/initialize.php';

//Setup the finances class
$finance = new AccountFinances();
//a little storage for some checks
$Storage = array();

function wlog($log_msg)
{
    $log_file_data = ROOTPATH . '/log_' . date('d-M-Y') . '.log';
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

function IsCounterRewarded($counter)
{
	global $Storage;
	
	if (isset($Storage[$counter]))
	{
		return true;
	}
	
	return false;
}

function SetCounterRewarded($counter)
{
	global $Storage;
	
	$Storage[$counter] = true;
}

wlog('Distributing Top Voters rewards for '.date('F', strtotime(date('Y-m-d')." -1 month")).'.');

//since we're running this on the first day of the next month, we're going to get the month and year of one month ago
$year = date('Y', strtotime(date('Y-m-d')." -1 month"));
$month = date('n', strtotime(date('Y-m-d')." -1 month"));

//Let's start off by finding our top voters of the month
$res = $DB->prepare("SELECT `account`, `counter` FROM `votecounter` WHERE `year` = :year AND `month` = :month ORDER BY `counter` DESC LIMIT 5;");
$res->bindParam(':year', $year, PDO::PARAM_INT);
$res->bindParam(':month', $month, PDO::PARAM_INT);
$res->execute();

if ($res->rowCount() > 0)
{
    $Rank = 0;

	//Loop through the winners
	while ($arr = $res->fetch())
	{
		//Increase the rank
		$Rank++;
		
		//Check if this counter and all accounts that have that same count are already rewarded
		if (IsCounterRewarded($arr['counter']))
		{
			//Skip
			continue;
		}
		
		//We can continue with the rewarding
		//Find all the accounts with that same counter
		$res_more = $DB->prepare("SELECT `account` FROM `votecounter` WHERE `year` = :year AND `month` = :month AND `counter` = :counter;");
		$res_more->bindParam(':year', $year, PDO::PARAM_INT);
		$res_more->bindParam(':month', $month, PDO::PARAM_INT);
		$res_more->bindParam(':counter', $arr['counter'], PDO::PARAM_INT);
		$res_more->execute();
		
		//loop through all the newly found accounts
		while ($accounts = $res_more->fetch())
		{
			//We reward diferently for the top 3 and top 5
			if ($Rank <= 3)
			{
				//Set the account id
				$finance->SetAccount((int)$accounts['account']);
				
				//Set the currency to gold
				$finance->SetCurrency(CURRENCY_GOLD);
				//Set reward amount for gold coins
				$finance->SetAmount(5);
				
				//Set the currency to silver
				$finance->SetCurrency(CURRENCY_SILVER);
				//Set reward amount for gold coins
				$finance->SetAmount(25);
				
				//Give coins to the user
				$finance->Reward('Top Voter of the Month', CA_SOURCE_TYPE_REWARD);
			}
			else
			{
				//Set the account id
				$finance->SetAccount($accounts['account']);
				
				//Set the currency to silver
				$finance->SetCurrency(CURRENCY_SILVER);
				//Set reward amount for gold coins
				$finance->SetAmount(25);
				
				//Give coins to the user
				$finance->Reward('Top Voter of the Month', CA_SOURCE_TYPE_REWARD);
			}
		}
		
		wlog('Rewarding Rank: '.$Rank.', counter: '.$arr['counter'].', accounts count: '.$res_more->rowCount());
		
		SetCounterRewarded($arr['counter']);
	}
	unset($res_more, $accounts, $arr);
}
else
{
	wlog('The month had no Top Voters.');
}
unset($res);

exit;