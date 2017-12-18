<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 15/12/17
 * Time: 2:33 PM
 */

class Dever_Sms_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function sendSms($type, $options)
    {
        // Prepare Sms templates
        $content = '';
        switch ($type)
        {
            case 'deals':
                $content = "Dear customer\n,
                Thanks for your interest in our products. You have availed the product {$options['product_name']} for Rs.{$options['price']}. 
                The code is {$options['coupon_code']}. Please show this message at the store when you are visiting. For availing this offer you need to buy products from our store for a minimum of 5000 Rupees\n.
                Team Stories";
                break;
            default:
                //Do Nothing
        }

        //Send Sms
        $this->triggerSms($content, $options['phone']);
        //return $content;
    }

    public function triggerSms($content, $mobile)
    {
        $data = array (
            'username' => 'stories',
            'password' => 'stories123',
            'mobile' => $mobile,
            'message' => urlencode($content),
            'sendername' => Mage::getStoreConfig('dever_config/smsgateway/sender_id'),
            'routetype' => 1
        );

        $apiUrl = 'http://sapteleservices.in/SMS_API/sendsms.php';
        $url = $apiUrl . "?" . http_build_query($data);

        $client = curl_init($url);
        curl_setopt($client, CURLOPT_HEADER, true);    // we want headers
        curl_setopt($client, CURLOPT_NOBODY, true);    // we don't need body
        curl_setopt($client, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($client, CURLOPT_TIMEOUT,10);
        $output = curl_exec($client);
        $httpcode = curl_getinfo($client, CURLINFO_HTTP_CODE);
        curl_close($client);

        return $httpcode;
    }
}