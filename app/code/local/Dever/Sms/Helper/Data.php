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
                $content = "Dear {$options['name']},\nThank you for your interest in our product: {$options['product_name']} of product code: {$options['sku']} for Rs.{$options['price']}. Your coupon code is {$options['coupon_code']}. Show the coupon at store during the time of purchase. Limited stocks only. Visit Stories,Calicut. Hurry up. T&C Apply.\nRegards,\nTeam Stories";
                break;
            default:
                //Do Nothing
        }

        //Send Sms
        $response = $this->triggerSms($content, $options['phone']);
        return $response;
    }

    public function triggerSms($content, $mobile)
    {
        $data = array (
            'username' => 'stories',
            'password' => 'stories123',
            'mobile' => $mobile,
            'message' => $content,
            'sendername' => 'STORIS',
            'routetype' => 2
        );

        $apiUrl = 'http://sapteleservices.com/SMS_API/sendsms.php';
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