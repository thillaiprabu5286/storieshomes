<?php
class Mindstermob_Mobileconnect_Model_Baseurl extends Mage_Core_Model_Abstract {
	
	
	
           function getSoapUrl(){
                //$ip_var_hostname="192.168.200.89/stories_updated";
                 $ip_var_hostname="www.storieshomes.com";
                //$ip_var_hostname="10.10.10.31/stories/stories_updated";
                //$ip_var="localhost";


                return $ip_var_hostname;

            }   
            function getSoapUser(){
                $user_var="soapuser";
                return $user_var;
            }   
            function getSoapPass(){
                $pass_var="apikey";


                return $pass_var;

            }   

            function getBaseurl(){
            // $ip_var="192.168.200.75";
            $ip_var="www.storieshomes.com";

            //$ip_var="122.166.42.158:8085";      
            $out_url=str_replace("localhost",$ip_var,Mage::getBaseUrl());
             return $out_url;   
            }
            function getBaseurl_products($str){ 
             //$ip_var="192.168.200.75";
              $ip_var="www.storieshomes.com";
             //$ip_var="122.166.42.158:8085";   
            //$ip_var="10.10.10.31";
            //$ip_var="122.166.42.158:8085/stories/";  

             //$out_url=str_replace("localhost",$ip_var,Mage::getBaseUrl());

             $out_url=str_replace("localhost",$ip_var,$str);

             return $out_url;   
            }
        
}
?>