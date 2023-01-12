<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/thirdparty/stripe-php/init.php';

use \Stripe\Stripe;
use \Stripe\Customer;
use \Stripe\ApiOperations\Create;
use \Stripe\Charge;

class StripeLib
{
    private $core;
	private $apiKey;
    private $stripeService;
    private $customer_id;

    public function __construct()
    {
        $this->core =& get_instance();
        $this->core->loadConfig('payments');
        $config = $this->core->configItem('stripe', 'payments');
        $this->apiKey = $config['api_key'];

        $this->stripeService = new \Stripe\Stripe();
        $this->stripeService->setVerifySslCerts(false);

        try
        {
            $this->stripeService->setApiKey($this->apiKey);
        }
        catch (\Stripe\Error\Authentication $e) { }
    }

    private function addCustomer($customerDetailsAry)
    {
        $customer = new Customer();
        $customerDetails = $customer->create($customerDetailsAry);
        
        return $customerDetails;
    }

    private function getCustomerByEmail($email)
    {
        $res = $this->core->db->prepare("SELECT `customer_id` FROM `stripe_customers` WHERE `email` = :email ORDER BY `timestamp` DESC LIMIT 1;");
        $res->bindParam(':email', $email, PDO::PARAM_STR);
        $res->execute();
        
        if ($res->rowCount() > 0)
        {
            $row = $res->fetch();

            return $row['customer_id'];
        }

        return false;
    }

    private function saveCustomer($customer_id, $email)
    {
        $insert = $this->core->db->prepare("INSERT INTO `stripe_customers` (`customer_id`, `email`) VALUES (:customer, :email);");
        $insert->bindParam(':customer', $customer_id, PDO::PARAM_STR);
        $insert->bindParam(':email', $email, PDO::PARAM_STR);
        $insert->execute();

        return ($insert->rowCount() > 0);
    }

    public function prepareCustomer($token, $name, $email)
    {
        // Prepare customer id
        $customer_id = $this->getCustomerByEmail($email);

        if ($customer_id === false)
        {
            $customerDetailsAry = array(
                'email' => $email,
                'name' => $name,
                'source' => $token
            );
            $customerResult = $this->addCustomer($customerDetailsAry);
            $customer_id = $customerResult->id;
            $this->saveCustomer($customer_id, $email);
        }

        $this->customer_id = $customer_id;
    }

    public function chargeCustomer($amount, $currency, $description, $metadata = array())
    {
        $charge = new Charge();

        $cardDetailsAry = array(
            'customer' => $this->customer_id,
            'amount' => $amount * 100,
            'currency' => $currency,
            'description' => $description,
            'metadata' => $metadata
        );
        $result = $charge->create($cardDetailsAry);

        return $result->jsonSerialize();
    }

    public function charge($token, $amount, $currency, $email, $description, $metadata = array())
    {
        $charge = new Charge();

        $cardDetailsAry = array(
            "amount" => $amount * 100,
            "currency" => $currency,
            "source" => $token,
            "receipt_email" => $email,
            "description" => $description,
            'metadata' => $metadata
        );
        $result = $charge->create($cardDetailsAry);

        return $result->jsonSerialize();
    }
}