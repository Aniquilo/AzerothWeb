<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Ipn extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function paypal()
    {
        $this->loadConfig('payments');
        //Load the most important module
        $this->loadLibrary('accounts.finances');
        //Log this transaction
        $this->loadLibrary('transaction.logging');
        $this->loadLibrary('coin.activity');

        $paypalConfig = $this->configItem('paypal', 'payments');
        $bonuses = $this->configItem('bonuses', 'payments');

        //Setup the log class
        $Logs = new TransactionLogging_Paypal();

        //Setup the finances class
        $finance = new AccountFinances();

        // STEP 1: Read POST data
        // reading posted data from directly from $_POST causes serialization 
        // issues with array data in POST
        // reading raw POST data from input stream instead. 
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval)
        {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc'))
        {
            $get_magic_quotes_exists = true;
        } 

        foreach ($myPost as $key => $value)
        {        
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1)
            { 
                $value = urlencode(stripslashes($value)); 
            }
            else
            {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        //Save the variables
        $Logs->SetVariables($_POST);
        //Seve the query
        $Logs->SetQuery($req);

        // STEP 2: Post IPN data back to paypal to validate
        $ch = curl_init($paypalConfig['url']);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, ROOTPATH . '/resources/ca-bundle.crt');
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        // In wamp like environments that do not come bundled with root authority certificates,
        // please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
        // of the certificate as shown below.
        // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        if (!($res = curl_exec($ch)))
        {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        // STEP 3: Inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0)
        {
            $Identity = $_POST['custom'];
            $Amount = (int)$_POST['mc_gross'];
            $Quantity = $_POST['quantity'];
            $txn_id	= $_POST['txn_id'];
            
            /*Check payment status.*/
            if ($_POST['payment_status'] != "Completed")
            {
                $Logs->append('[Error] This transaction is not completed but '.$_POST['payment_status'].' Reason: '.$Logs->ResolvePending($_POST['pending_reason']).'.', 'error');
                $Logs->save();
                exit;
            }
            
            /*Prevent txnid recycling.*/ 		
            $res = $this->db->prepare("SELECT `txn_id` FROM `paypal_logs` WHERE `txn_id` = :txn AND `paypal_status` = 'Completed';");
            $res->bindParam(':txn', $txn_id, PDO::PARAM_STR);
            $res->execute();
            
            if ($res->rowCount() > 0)
            {
                $Logs->append('[Error] This transaction id is a duplicate.');
                $Logs->save();
                exit;
            }
            unset($res);
            
            /*Verify that the money ware sent to our email*/
            if ($_POST['receiver_email'] != $paypalConfig['email'])
            {
                $Logs->append('[Error] The payment receiver is not our e-mail address.');
                $Logs->save();
                exit;
            }
            
            /*log successsfull transaction*/
            $Logs->append('[Success] Successful transaction, proceeding to user updates!');
            
            /*get the account id*/
            $row = $this->authentication->getAccountByIdentity($Identity);

            if ($row === false)
            {
                //log invalid account
                $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                $Logs->append('[Error] Invalid account, could not resolve the account id by Identity.');
                $Logs->save();
                exit;
            }
            else
            {
                //save as var
                $accId = (int)$row['id'];
                $recruiter = (int)$row['recruiter'];
                //save memory
                unset($row);
                
                //Set the account id
                $finance->SetAccount($accId);
                //Set the currency to gold
                $finance->SetCurrency(CURRENCY_GOLD);
                
                /*select current gold coins*/
                $res2 = $this->db->prepare("SELECT `gold` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
                $res2->bindParam(':acc', $accId, PDO::PARAM_INT);
                $res2->execute();

                if ($res2->rowCount() == 0)
                {
                    $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                    $Logs->append('[Error] Invalid account, could not get the gold value by account id.');
                    $Logs->save();
                    exit;
                }
                else
                {
                    //fetch
                    $row = $res2->fetch(PDO::FETCH_ASSOC);

                    //var
                    $gold = (int)$row['gold'];

                    //save memory
                    unset($row);
                    
                    //We might have a formula to calculate the Coins by Amount Donated
                    $AmountCoins = abs($Amount);
                    
                    // Define the bonus
                    $BonusAmount = 0;
                    
                    // check if we have bonus groups
                    if ($bonuses && is_array($bonuses) && count($bonuses) > 0)
                    {
                        // detect the bonus group
                        foreach ($bonuses as $bonus)
                        {
                            list($min, $max) = explode('-', $bonus['range']);
                            
                            if ($AmountCoins >= $min && $AmountCoins <= $max)
                            {
                                // That's the right group
                                // Add the bonus to the amount
                                $BonusAmount = abs(floor(((int)$bonus['bonus'] / 100) * $AmountCoins));
                                // add to the amount
                                $AmountCoins = $AmountCoins + (int)$BonusAmount;

                                // Break the loop
                                break;
                            }
                        }
                    }
                    
                    //Set the amount we are Giving/Taking
                    $finance->SetAmount($AmountCoins);
                    
                    //Detect if the payment is a deduction or not
                    if ($Amount < 0) //THIS IS A DEDUCTION
                    {
                        $Logs->append('The transaction is a Deduction Type.');
                        
                        //Take the coins from the user
                        $Deduct = $finance->Charge('Deduction of Gold Coins', CA_SOURCE_TYPE_DEDUCTION);
                        
                        //Check if the deduction was successfull
                        if ($Deduct === true)
                        {
                            //Deduction success
                            $Logs->SetLogType(TRANSACTION_LOG_TYPE_NORMAL);
                            //append message to the log
                            $Logs->append("The deduction was successfull.");
                        }
                        else
                        {
                            //Deduction failed
                            $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                            //append message to the log
                            $Logs->append("The deduction of coins failed, error returned: ".$Deduct.". ");
                        }
                        unset($Deduct);
                    }
                    else //THIS IS A REWARD
                    {
                        $Logs->append('The transaction is a Reward Type.');
                        
                        /*add points, but compare money donated with itemcout*/
                        if ($AmountCoins != ($Quantity + $BonusAmount))
                        {
                            $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                            $Logs->append('[Error] Amount of Coins calculated ('.$AmountCoins.') are not equal to the number of items ('.$Quantity.').');
                            $Logs->save();
                            exit;
                        }
                        else
                        {
                            //Give coins to the user
                            $Reward = $finance->Reward('Purchased Gold Coins', CA_SOURCE_TYPE_PURCHASE);
                            
                            //check if it was updated
                            if ($Reward === true)
                            {		
                                $Logs->append('[Success] The gold was successfully added, value: '.$gold.' was updated to: '.($gold + $AmountCoins).'.');
                                //$Logs->save();
                            }
                            else
                            {
                                $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                                $Logs->append('[Error] Failed to apply new gold value, value: '.$gold.' was going to be updated to: '.($gold + $AmountCoins).'. Return: ' . $Reward);
                                $Logs->save();
                                exit;
                            }
                            unset($Reward);
                            
                            //check if the user has a recruiter
                            if ($recruiter > 0)
                            {
                                // Check if we have more than 10 coins
                                if (abs($Amount) >= 10)
                                {
                                    // Check if there's a link and if the link is active
                                    $linkStatus = RAF_LINK_ACTIVE;
                    
                                    $rafres = $this->db->prepare("SELECT * FROM `raf_links` WHERE `account` = :acc AND `recruiter` = :rec AND `status` = :status ORDER BY id DESC;");
                                    $rafres->bindParam(':acc', $accId, PDO::PARAM_INT);
                                    $rafres->bindParam(':rec', $recruiter, PDO::PARAM_INT);
                                    $rafres->bindParam(':status', $linkStatus, PDO::PARAM_INT);
                                    $rafres->execute();
                                    
                                    if ($rafres->rowCount() > 0)
                                    {
                                        $Logs->append('The user has active link to his recruiter.');
                                        
                                        // Calculate the reward amount
                                        $RAFRewardAmount = (int)floor((10 / 100) * abs($Amount));
                                        
                                        //update the recruiter points
                                        $update = $this->db->prepare("UPDATE `account_data` SET `gold` = gold + :points WHERE `id` = :acc LIMIT 1;");
                                        $update->bindParam(':acc', $recruiter, PDO::PARAM_INT);
                                        $update->bindParam(':points', $RAFRewardAmount, PDO::PARAM_INT);
                                        $update->execute();
                                        
                                        //check if the points ware updated
                                        if ($update->rowCount() > 0)
                                        {
                                            //log into coin activity
                                            $ca = new CoinActivity($recruiter);
                                            $ca->set_SourceType(CA_SOURCE_TYPE_REWARD);
                                            $ca->set_SourceString('Referral donation reward');
                                            $ca->set_CoinsType(CA_COIN_TYPE_GOLD);
                                            $ca->set_ExchangeType(CA_EXCHANGE_TYPE_PLUS);
                                            $ca->set_Amount($RAFRewardAmount);
                                            $ca->execute();
                                            unset($ca);
                                            
                                            $Logs->append('[RAF Success] The recruiter has been rewarded '.$RAFRewardAmount.' gold coins.');
                                        }
                                        else
                                        {
                                            $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                                            $Logs->append('[RAF Error] Failed to reward the recruiter, reward amount is '.$RAFRewardAmount.' gold coins.');
                                            $Logs->save();
                                            exit;
                                        }
                                        unset($RAFRewardAmount, $update);
                                    }
                                    unset($rafres, $linkStatus);
                                }
                            }
                            
                            // Save the log
                            $Logs->save();
                        }
                    }
                }
                unset($res2);
            }
            
            // check whether the payment_status is Completed 			- CHECK
            // check that txn_id has not been previously processed 		- CHECK
            // check that receiver_email is your Primary PayPal email	- CHECK
            // check that payment_amount/payment_currency are correct	- CHECK
            // process payment
        }
        else if (strcmp($res, "INVALID") == 0)
        {
            // log for manual investigation
            $Logs->append('[Error] Paypal did not confirm the query.');
            $Logs->save();
        }

        unset($finance, $Logs);
    }

    public function paymentwall()
    {
        $this->loadConfig('payments');
        //Load the most important lib
        $this->loadLibrary('accounts.finances');

        $paymentwallConfig = $this->configItem('paymentwall', 'payments');

        //Setup the finances class
        $finance = new AccountFinances();

        define('SECRET', $paymentwallConfig['secret_key']); // YOUR SECRET KEY
        define('CREDIT_TYPE_CHARGEBACK', 2);

        //Get them variables
        $userId 	= isset($_GET['uid']) 		? (int)$_GET['uid'] 		: NULL;
        $credits	= isset($_GET['currency']) 	? (int)$_GET['currency'] 	: NULL;
        $type 		= isset($_GET['type']) 		? (int)$_GET['type'] 		: NULL;
        $refId 		= isset($_GET['ref']) 		? $_GET['ref'] 				: NULL;
        $signature 	= isset($_GET['sig']) 		? $_GET['sig'] 				: NULL;

        //Assume failured
        $result = false;

        //Check if them variables are set
        if (!empty($userId) && !empty($credits) && isset($type) && !empty($refId) && !empty($signature))
        {
            //Let's generate the signature
            $signatureParams = array(
                'uid' => $userId,
                'currency' => $credits,
                'type' => $type,
                'ref' => $refId
            );
            $signatureCalculated = $this->pw_GetSignature($signatureParams, SECRET);
            
            //check if IP is in whitelist and if signature matches
            if ($this->pw_isIpAddressValid($_SERVER['REMOTE_ADDR']) && ($signature == $signatureCalculated))
            {
                //Success
                $result = true;
                
                //Log this transaction
                $this->loadLibrary('transaction.logging');
                
                //Setup the log class
                $Logs = new TransactionLogging();
                
                //Save the variables
                $Logs->SetVariables($_GET);
                
                //Set the account id
                $finance->SetAccount($userId);
                
                //Set the currency to gold
                $finance->SetCurrency(CURRENCY_GOLD);
                
                //Check if it's deduction, Paymentwall send amount value with "-"
                if ($type == CREDIT_TYPE_CHARGEBACK)
                {
                    //remove the minus
                    $credits = (int)trim($credits, '-');
                }
                
                //Set the amount we are Giving/Taking
                $finance->SetAmount($credits);
                
                if ($type == CREDIT_TYPE_CHARGEBACK)
                {           
                    // Deduct credits from user
                    // This is optional, but we recommend this type of crediting to be implemented as well
                    // Note that currency amount sent for chargeback is negative, e.g. -5, so be caferul about the sign
                    // Don’t deduct negative number, otherwise user will get credits instead of losing them
                    
                    //Resolve the deduction reason by id
                    switch ($_GET['reason'])
                    {
                        case 1:
                            $reason 	= 'Chargeback';
                            $reasonUser = 'Payment chargeback';
                            break;
                        case 2:
                            $reason 	= 'Credit Card fraud Ban user';
                            $reasonUser = 'Credit Card fraud';
                            break;
                        case 3:
                            $reason 	= 'Order fraud Ban user';
                            $reasonUser = 'Order fraud';
                            break;
                        case 4:
                            $reason 	= 'Bad data entry';
                            $reasonUser = 'Bad data entry';
                            break;
                        case 5:
                            $reason 	= 'Fake / proxy user';
                            $reasonUser = 'Fake / proxy user';
                            break;
                        case 6:
                            $reason 	= 'Rejected by advertiser';
                            $reasonUser = 'Rejected by advertiser';
                            break;
                        case 7:
                            $reason 	= 'Duplicate conversions';
                            $reasonUser = 'Duplicate conversions';
                            break;
                        case 8:
                            $reason 	= 'Goodwill credit taken back';
                            $reasonUser = 'Goodwill credit taken back';
                            break;
                        case 9:
                            $reason 	= 'Cancelled order';
                            $reasonUser = 'Cancelled order';
                            break;
                        case 10:
                            $reason 	= 'Partially reversed transaction';
                            $reasonUser = 'Partially reversed transaction';
                            break;
                        default:
                            $reason 	= 'Unknown code ' . (int)$_GET['reason'];
                            $reasonUser = 'Uuknown reason';
                            break;
                    }
                    //append message to the log
                    $Logs->append("The transaction is deduction type, reason: \"".$reason."\". ");
                    
                    //Take the coins from the user
                    $Deduct = $finance->Charge('Deduction reason: ' . $reasonUser . '.', CA_SOURCE_TYPE_DEDUCTION);
                    
                    //Check if the deduction was successfull
                    if ($Deduct === true)
                    {
                        //Deduction success
                        $Logs->SetLogType(TRANSACTION_LOG_TYPE_NORMAL);
                        //append message to the log
                        $Logs->append("The deduction was successfull.");
                    }
                    else
                    {
                        //Deduction failed
                        $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                        //append message to the log
                        $Logs->append("The deduction of coins failed, error returned: ".$Deduct.". ");
                    }
                    
                    unset($reason, $reasonUser, $Deduct);
                }
                else
                {
                    // Give credits to user

                    //resolve the transaction type
                    switch ($type)
                    {
                        case 0:
                            $TransactionType = 'Credit is given.';
                            $CA_SourceType 	 = CA_SOURCE_TYPE_PURCHASE;
                            $CA_SourceString = 'Purchased Gold Coins';
                            break;
                        case 1:
                            $TransactionType = 'Credit is given as a customer service.';
                            $CA_SourceType 	 = CA_SOURCE_TYPE_REWARD;
                            $CA_SourceString = 'Earned Gold Coins';
                            break;
                        default:
                            $TransactionType = 'Uknown type ' . $type;
                            $CA_SourceType 	 = CA_SOURCE_TYPE_NONE;
                            $CA_SourceString = 'Received gold coins from unknown source';
                            break;
                    }
                    //append message to the log
                    $Logs->append("The transaction is reward type, type: \"".$TransactionType."\". ");
                    
                    //Give coins to the user
                    $Reward = $finance->Reward($CA_SourceString, $CA_SourceType);
                    
                    //check if the reward was successful
                    if ($Reward)
                    {
                        //Reward success
                        $Logs->SetLogType(TRANSACTION_LOG_TYPE_NORMAL);
                        //append message to the log
                        $Logs->append("The rewarding was successfull. ");
                    }
                    else
                    {
                        //Reward failed
                        $Logs->SetLogType(TRANSACTION_LOG_TYPE_URGENT);
                        //append message to the log
                        $Logs->append("The rewarding with coins failed, error returned: ".$Reward.". ");
                    }
                    
                    unset($TransactionType, $CA_SourceType, $CA_SourceString, $Reward);
                }
                unset($finance);
                
                //save the log
                $Logs->save();
            }
        }

        //The request was OK
        if ($result)
        {
            echo 'OK';
            exit;
        }
        else
        {
            header('HTTP/1.0 404 not found');
            exit;
        }
    }

    private function pw_GetSignature($params, $secret)
    {
        $str = '';
        foreach ($params as $k=>$v)
        {
                $str .= "$k=$v";
        }
        $str .= $secret;
        return md5($str);
    }

    private function pw_isIpAddressValid($ipAddress)
	{
		$ipsWhitelist = array(
			'174.36.92.186',
			'174.36.96.66',
			'174.36.92.187',
			'174.36.92.192',
			'174.37.14.28'
		);
		$rangesWhitelist = array(
			'216.127.71.0/24'
        );
        
		if (in_array($ipAddress, $ipsWhitelist)) {
			return true;
		}
		
		foreach ($rangesWhitelist as $range) {
			if ($this->isCidrMatched($ipAddress, $range)) {
				return true;
			}
        }
        
		return false;
    }
    
	private function isCidrMatched($ip, $range)
	{
	    list($subnet, $bits) = explode('/', $range);
	    $ip = ip2long($ip);
	    $subnet = ip2long($subnet);
	    $mask = -1 << (32 - $bits);
	    $subnet &= $mask;
	    return ($ip & $mask) == $subnet;
	}
}