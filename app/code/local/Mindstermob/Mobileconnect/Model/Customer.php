<?php
class Mindstermob_Mobileconnect_Model_Customer extends Mage_Core_Model_Abstract {

     public function _construct()
    {   
       $this->_init('mobileconnect/customer');
    }
    
    public function customer_login($email,$password){
         try {
           // $email =trim($params->email);
           // $password =trim($params->passwd);
           // $email="nadeem.p@aufait.in";
            //$password="nadeem";
            $session = Mage::getSingleton('customer/session');
            $login = $session->login($email,$password);
           if ($session->isLoggedIn()) {
               //echo "success";
                $session->setCustomerAsLoggedIn($session->getCustomer());
                      $customer_id=$session->getCustomerId();
                      $send_data["success"]=true;
                      $send_data["message"]="Login Success";
                      $send_data["customer_id"]=$customer_id;
                      
                      
                    // echo json_encode($send_data);
              // if(!Mage::getSingleton('customer/session')->isLoggedIn()){
           //$yourCustomerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
           $field = 'customer_id';
             $customer = Mage::getModel('customer/customer')->load($customer_id);
                if (!$customer->getId()) {
                    //$this->_fault('not_exists');
                    // If customer not found.
                }
                 $send_data["data"]=$customer->toArray();
                // echo json_encode($send_data);
               // print_r($customer->toArray());
           //$orders->getSelect()->where('e.customer_id ='.$customer_id);

           } else {
               //echo "fails";
               $send_data["message"]="Invalid email or password";
               $send_data["success"]=false;
           }
       } catch (Exception $ex) {

          // Zend_Debug::dump( strip_tags($ex->getMessage()));
            $send_data["message"]="Invalid email or password";
           $send_data["success"]=false;
          // echo json_encode($send_data);

       }
        return(json_encode($send_data));
        
    }//=======end of function=====//
    
    public function customer_register($firstname,$lastname,$email,$passwd){
        $json = array('status'=>'error','message'=>'','code'=>'','data' => array());
        $websiteId = Mage::app()->getWebsite()->getId();
          $store = Mage::app()->getStore();
         try{
          $customer = Mage::getModel("customer/customer");
          $customer   ->setWebsiteId($websiteId)
                      ->setStore($store)
                      ->setFirstname($firstname)
                      ->setLastname($lastname)
                      ->setEmail($email)
                      ->setPassword($passwd);

     
              $customer->save();
              
              //================ sending E mail====================//
              if ($customer->getWebsiteId())
                    {
                        $storeId = $customer->getSendemailStoreId();
                        $customer->sendNewAccountEmail('registered', '', $storeId);
                        
                      
                       // $newPassword = $customer->generatePassword();
                       // $customer->changePassword($newPassword);
                       // $customer->sendPasswordReminderEmail();
                        
                       // $customer->updated_at =date('Y-m-d H:i:s', time()); 
                        $customer->save();
                    }
              
              //================= end of sending email===============//
             // echo "saved successfully";
             // echo "success";
             $json["status"]="success";
             
              
          }
          catch (Exception $e) {
              $json["status"]="error";
              $json["message"]=$e->getMessage();   
              //echo $e->getMessage();
             // Zend_Debug::dump(($e->getMessage()));
           
              
          }
          
         return (json_encode($json));
        
    }//======end of function==========//
    
    public function getCustomerprofile($login_email){
        $json=array("success"=>false,"message"=>"","data"=>"");
        try{
     
        $customer = Mage::getModel('customer/customer');
                   $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                  $customer->loadByEmail($login_email);
                  $json["data"]=$customer->toArray();
                  $json["success"]=true;
                  $json["message"]="success";
         }catch(Exception $ex) {
             $json["success"]=false;
              $json["message"]="Error";
        }  
        return json_encode($json);
    } //=====end of function==============//
    
    public function customer_forgotpassword($email){
        
         $websiteId = Mage::app()->getWebsite()->getId();
          $store = Mage::app()->getStore();

          $customer = Mage::getModel("customer/customer");
          $customer   ->setWebsiteId($websiteId)
                      ->setStore($store);
                      
          
          $customer ->loadByEmail($email);
          //echo($customer->getId());
          
          //$customer = Mage::getModel('customer/customer')->loadByEmail($customer_id);
          
          //print_r($customer ->loadByEmail($email));
          
          
          if ($customer->getId()) {
                try {
                    
                    //$newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $newResetPasswordLinkToken =  Mage::app()->getHelper('customer')->generateResetPasswordLinkToken();
                   //echo $newResetPasswordLinkToken; 
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                   //echo ($customer->changeResetPasswordLinkToken($newResetPasswordLinkToken)); 
                    $customer->sendPasswordResetConfirmationEmail();
                    //echo($customer->sendPasswordResetConfirmationEmail());
                      $send_data["success"]=true;
                       $send_data["message"]="An Email sent with Reset password link";
                     // $send_data["message"]="Login Success";
                     // $send_data["customer_id"]=$customer_id;
                    
                } catch (Exception $exception) {
                    //$this->_getSession()->addError($exception->getMessage());
                    //echo $exception->getMessage();
                    $send_data["success"]=false;
                    $send_data["message"]="Email sending failed";
                    //
                    //return;
                }
                
               // echo json_encode($send_data);
                
            }
          return json_encode($send_data);
        
        
    }//=============enf of function=========//
    
    public function Updatecustomerprofile($login_email,$login_password,$newEmail,$firstname,$lastname) {
        
        $json=array("success"=>false,"status"=>0,"message"=>"","data"=>array("firstname"=>"","lastname"=>"","email"=>""));
        $valid=0;           
       $customer = Mage::getModel('customer/customer');
       $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
       $customer->loadByEmail($login_email);

       if($customer->getId()>1){
            $customer2 = Mage::getModel('customer/customer');
            $customer2->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer2->loadByEmail($newEmail);
            if($customer2->getId()<1){
               $customer->setEmail($newEmail); 
            }
            $customer->setFirstname($firstname); 
            $customer->setLastname ($lastname); 
            $customer->save();
             $json["success"]=true;
            $json["data"]["firstname"]=$firstname;
            $json["data"]["lastname"]=$lastname;
            $json["data"]["email"]=$newEmail;
            $json["status"]=1;
            $json["message"]="Updated successfully";


        }
        //else{
        //     $json["message"]="Customer not exist";
        //}


       //echo json_encode($json); 
        return json_encode($json); 
        
    }//=====end of function============//
    
    public function ChangeCustomerpassword($login_email,$login_password,$current_pass,$new_pass){
//        $logindata=$params->logindata;
//        $login_email=$logindata->email;
//        $login_password=$logindata->passwd;
//
//        $formdata=$params->formdata;
//
//        $current_pass=$formdata->current_passwd;
//        $new_pass=$formdata->new_passwd;


       //$email="nadeem.p@aufait.in";
       //$newEmail="nadeem.p@aufait.in";
       //$firstname="Nadeem";
       //$lastname="Para";
       $json=array("success"=>false,"message"=>"");

       $validate = 0; $result = '';
       //$customerid = 46;
       $username = $login_email;
       $oldpassword = $current_pass;
       $newpassword = $new_pass;

        $websiteId=Mage::app()->getWebsite()->getId();
        try {
            // $login_customer_result = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->authenticate($username, $oldpassword);
             $login_customer_result = Mage::getModel('customer/customer');
             $login_customer_result->setWebsiteId(Mage::app()->getWebsite()->getId());
             $login_customer_result->authenticate($username, $oldpassword);
             $validate = 1;
        }
        catch(Exception $ex) {
             $validate = 0;
        }
        if($validate == 1) {
             try {
                  $customer = Mage::getModel('customer/customer');
                   $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                  $customer->loadByEmail($login_email);
                  $customer->setPassword($newpassword);
                  $customer->save();
                  $result = 'Your Password has been Changed Successfully';
                  $json["message"]=$result;
                   $json["success"]=true;
             }
             catch(Exception $ex) {
                  $result = 'Error : '.$ex->getMessage();
                  $json["message"]=$result;
                  $json["success"]=false;
             }
        }
        else {
             $result = 'Incorrect Old Password.';
               $json["message"]=$result;
                $json["success"]=false;
        }
        return json_encode($json);
        
        
    }//==============end of function======//
 public function getCustomerorders($email,$password) {
     $base_url=Mage::getModel('mobileconnect/baseurl');
      $json = array('status'=>'error','login'=>'0','message'=>'','code'=>'','orders' => array());
        if($email!=''){
         $session = Mage::getSingleton('customer/session');
           try {
             $login = $session->login($email,$password);


             if ($session->isLoggedIn()) {
                 //echo "Successfully Logged in";

               $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
               $field          = 'customer_id';
               $collection     = Mage::getModel("sales/order")->getCollection()
                                  ->addAttributeToSelect('*')
                                  ->addFieldToFilter($field, $customer_id);
                          //$orders->getSelect()->where('e.customer_id ='.$customer_id);
                 $orders = Mage::getResourceModel('sales/order_collection')
                   ->addFieldToSelect('*')
                   ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                   ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                   ->setOrder('created_at', 'desc');   

               // $this->setOrders($orders); 

                //echo print_r($orders);
                foreach ($orders as $order){
                    $date = date_create($order["created_at"]);
                    $order_date=date_format($date, 'm-d-Y');

                    //============get items and details==============//
                    $items_arr=array();
                    $order_inn = Mage::getModel("sales/order")->loadByIncrementId($order->getRealOrderId()); 
                           //load order by order id 
                           $ordered_items = $order_inn->getAllItems(); 
                           //print_r($ordered_items);

                           Foreach($ordered_items as $item){
                              //======get product options================//
                               $custom_option=array();
                                $options = $item->getProductOptions(); 
                               $customOptions = $options['options']; 
                               if(!empty($customOptions))
                                {
                                   //print_r($customOptions);exit;
                                   //====iterate titles======//
                                    foreach ($customOptions as $option)
                                    {
                                        $optionTitle = $option['label'];
                                        $optionValue = $option['value'];
                                        //echo "label-".$optionTitle."--value--".$optionValue;
                                        $custom_option["titles"][]=array("option_title"=>$optionTitle,"title_value"=>$optionValue); 
                                    }
                                }
                               
                               //======end of product options===========//
                               
                              $product = Mage::getModel('catalog/product')->load($item->getProductId());
                              $details=$product->getData();

                               $items_arr[]=array( "product_id"=>$item->getItemId(),
                                               "product_name"=>$item->getName(),
                                              // "display_price"=>(float)$item->getPrice(),
											  "display_price"=> number_format($item->getPrice(),2),
                                               "product_quantity"=>(float)$item->getQtyOrdered(),
                                                "options"=>$custom_option,
                                               "image"=>$base_url->getBaseurl()."media/catalog/product".$details["image"],
                                               "display_total"=>(float)$item["base_row_total"]); 

                           } 
                    //==================end of get items and details==================//

                    $json["orders"][]=array( "stock_quantity"=>$stockItemData["qty"],
                               "address_to"=>$order["customer_firstname"]." ".$order["customer_lastname"],
                                "order_id"=>$order->getRealOrderId(),
                              "status"=>ucfirst($order["status"]),
                              "display_total"=>(float)$order["grand_total"],
                              "order_date"=>$order_date,
                              "shipping_amount"=>(float)$order["shipping_amount"],
                             "items"=>$items_arr
                            );               

                }
               $json["status"]="success";
               $json["message"]="order retrieved";


             } else {
                 //echo "Not Logged in";
                  $json["status"]="error";
                 $json["message"]="login failed";
             }
         } catch (Exception $ex) {

              $json["status"]="error";
                 $json["message"]="failed";
         }
       }else{

           $json["status"]="error";
                 $json["message"]="login failed";
       }
       return json_encode($json);
     
     
 }//==============end of function======//
 
 public function getCustomerorderdetails($order_id,$email,$password) {
        $base_url=Mage::getModel('mobileconnect/baseurl');
        $json = array('status'=>'success','login'=>'0','message'=>'','items' => array(),'address'=>array(),'grand_total'=>'','order_id'=>'','shipping_amount'=>'','shipping_address'=>array(),'billing_address'=>array());
       //========================get shipping address================================
         $session = Mage::getSingleton('customer/session');
                   $login = $session->login($email,$password);
                   $address_shipping=array("street"=>'',"firstname"=>'',"lastname"=>'',"company"=>'',"city"=>'',"country_id"=>'',"region"=>'',"postcode"=>'',"phone_mobile"=>'');
                  if ($session->isLoggedIn()) {
                      //echo "success"; 
                      //====================load customer specific details=======================//

                       $session->setCustomerAsLoggedIn($session->getCustomer());
                       $customer_id=$session->getCustomerId();                  
                      $customer = Mage::getModel('customer/customer')->load($customer_id);
                       $cust_arr=$customer->getData();
                       //echo "shipping--".$cust_arr["default_shipping"];
                        //$send_data["data"]=$customer->toArray();
                       // echo json_encode($send_data);
                       $addressId1=$cust_arr["default_shipping"];
                       //$addressId2=$cust_arr["default_billing"];
                       $address_i = Mage::getModel('customer/address')->load($addressId1);
                       //print_r($address->getData()); 
                       $address_arr=$address_i->getData();
                       //echo "adress===".$address_arr["street"];
                       $json["address"]["street"]=ucfirst($address_arr["street"]); 
                       $json["address"]["firstname"]=ucfirst($address_arr["firstname"]); 
                       $json["address"]["lastname"]=ucfirst($address_arr["lastname"]); 
                       $json["address"]["company"]=ucfirst($address_arr["company"]); 
                       $json["address"]["city"]=ucfirst($address_arr["city"]); 
                       $json["address"]["country_id"]=$address_arr["country_id"]; 
                       $json["address"]["region"]=$address_arr["region"];
                       $json["address"]["postcode"]=$address_arr["postcode"];
                       $json["address"]["telephone"]=$address_arr["telephone"];
                      // $json["address"]=$address_shipping;
                       //echo json_encode($address_shipping);

                       //=================end of customer specific details============================//
                       //$json["login"]="1";


                      // echo json_encode($arr_json);

                   } else {
                      //echo "fails";
                      //$send_data["success"]=false;
                  }


       //=========================end of get shipping address======================== 

                  $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
               $field          = 'customer_id';
               $collection     = Mage::getModel("sales/order")->getCollection()
                                  ->addAttributeToSelect('*')
                                  ->addFieldToFilter($field, $customer_id);
                          //$orders->getSelect()->where('e.customer_id ='.$customer_id);
                 $orders = Mage::getResourceModel('sales/order_collection')
                   ->addFieldToSelect('*')
                   ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                   ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                   ->setOrder('created_at', 'desc');   
                  foreach ($orders as $order){
                      if($order->getRealOrderId()==$order_id){
                           $date = date_create($order["created_at"]);
                           $order_date=date_format($date, 'm-d-Y');
                         $json['grand_total']=(float)$order["grand_total"];
                         $json['shipping_amount']=(float)$order["shipping_amount"];
                          $json['order_id']=$order_id;   
                          $json['date']=$order_date;
                           $json['status']=ucfirst($order["status"]);


                          break;
                      }

                  }
       //===============get info from orders==================================



       //================end of get info from orders===========================           

       //===============get customer order details===============//
       //$order_id = 100000027; 
       $order = Mage::getModel("sales/order")->loadByIncrementId($order_id); 
       //load order by order id 
       $ordered_items = $order->getAllItems(); 
       //print_r($ordered_items);
       //==========get billing and shipping address=================//
       $shippingAddress = Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
       $billingAddress = Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
       //print_r($shippingAddress->street);exit;                   
         $json["shipping_address"]["firstname"] = ucfirst($shippingAddress->firstname);
        $json["shipping_address"]["lastname"] =ucfirst($shippingAddress->lastname);
        $json["shipping_address"]["street"] =ucfirst($shippingAddress->street);
        $json["shipping_address"]["company"] =ucfirst($shippingAddress->company);
        $json["shipping_address"]["city"] =ucfirst($shippingAddress->city);
        $json["shipping_address"]["country_id"] =$shippingAddress->country_id;
        $json["shipping_address"]["region"] =$shippingAddress->region;
        $json["shipping_address"]["postcode"] =$shippingAddress->postcode;
        $json["shipping_address"]["telephone"] =$shippingAddress->telephone;

        $json["billing_address"]["firstname"] = ucfirst($billingAddress->firstname);
        $json["billing_address"]["lastname"] =ucfirst($billingAddress->lastname);
        $json["billing_address"]["street"] =ucfirst($billingAddress->street);
        $json["billing_address"]["company"] =ucfirst($billingAddress->company);
        $json["billing_address"]["city"] =ucfirst($billingAddress->city);
        $json["billing_address"]["country_id"] =$billingAddress->country_id;
        $json["billing_address"]["region"] =$billingAddress->region;
        $json["billing_address"]["postcode"] =$billingAddress->postcode;
        $json["billing_address"]["telephone"] =$billingAddress->telephone;                   
       //=============end of billing and shipping address==========//

       Foreach($ordered_items as $item){     
            $custom_option=array();
                        $options = $item->getProductOptions(); 
                       $customOptions = $options['options']; 
                       if(!empty($customOptions))
                        {
                           //print_r($customOptions);exit;
                           //====iterate titles======//
                            foreach ($customOptions as $option)
                            {
                               // print_r($option);exit;
                               //  print_r($options);

                                $optionTitle = $option['label'];

                                $optionValue = $option['value'];
                                //echo "label-".$optionTitle."--value--".$optionValue;
                                $custom_option["titles"][]=array("option_title"=>$optionTitle,"title_value"=>$optionValue); 
                            }
                        }
          $product = Mage::getModel('catalog/product')->load($item->getProductId());
          $details=$product->getData();

           $json["items"][]=array( "product_id"=>$item->getItemId(),
                           "product_name"=>$item->getName(),
                          // "display_price"=>(float)$item->getPrice(),
						  
						  "display_price"=>number_format($item->getPrice(),2),
                           "product_quantity"=>(float)$item->getQtyOrdered(),
                           "options"=>$custom_option,
                           "image"=>$base_url->getBaseurl()."media/catalog/product".$details["image"],
                           "display_total"=>(float)$item["base_row_total"]); 

       } 



       //===============end of customer order details===============//
       return json_encode($json);
     
 }//==========end of function============//
 public function addAddress($data,$email){
    // $email = "nadeem.p@mindster.in";
/*$websiteId=Mage::app()->getWebsite()->getId();
$customer = Mage::getModel('customer/customer')
                     ->setWebsiteId($websiteId)
                     ->loadByEmail($email);
 $cust_arr=$customer->getData();
                print_r($cust_arr);exit;*/
        $default_billing=false;
        $default_shipping=false;
        //echo $data->shipping_status;exit;
        //print_r($data);exit;
        $json=array("status"=>"error","message"=>"Address not added","code"=>"","data"=>"");
        if($email!==""){
			
            $websiteId=Mage::app()->getWebsite()->getId();
           $customer = Mage::getModel('customer/customer')
                     ->setWebsiteId($websiteId)
                     ->loadByEmail($email);
          //$customer = Mage::getModel('customer/customer')->load($customer_id);
                $cust_arr=$customer->getData();
              //  print_r($cust_arr);exit;
             //  echo "shippind add--".$cust_arr["default_shipping"]; 
          //  echo "billing add--".$cust_arr["billing_shipping"]; 
                  if(!array_key_exists("default_billing",$cust_arr) && !array_key_exists("default_billing",$cust_arr)){
        //echo "inside if";exit;
        $default_billing=true;
        $default_shipping=true;
    }else{
                if($data->billing_status){

                    $default_billing=true;
                    //echo "inside if".$default_billing;exit;

                }

                if( $data->shipping_status){

                    $default_shipping=true;

                 }

            }
           ///echo "out side".$default_billing.'-------'.$default_shipping;exit;
            try{
            $addressData =  array (
                                "prefix" => "",
                               "firstname" => $data->firstname,
                                "middlename" => "",
                                "lastname" => $data->lastname,
                                "suffix" => "",
                                "company" => $data->company,
                                "street" => $data->street,
                                "city" => $data->city ,
                                "country_id" =>$data->id_country,
                               "region" => $data->code,
                                "region_id" => $data->id_state,
                                "postcode" => $data->postcode,
                                "telephone" => $data->phone_mobile,
                                "fax" => "",
                                "is_default_billing" => $default_billing,
                                "is_default_shipping" => $default_shipping,
                            );

             $customer = Mage::getModel('customer/customer');
             $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
             $customer->loadByEmail($email);
             $address   = Mage::getModel('customer/address');
             $address->addData($addressData);
             $customer->addAddress($address);
             $customer->save();
             $json["status"]="success";
              $json["message"]="Address added successfully";
            // $json=array("status"=>0);
             //echo "saved successfully";
            }catch(Exception $ex){
                $json["status"]="error";
              $json["message"]="Address not added";

            }
        }else{

             $json["status"]="error";
             $json["message"]="Address not added";
        }
        return json_encode($json);
     
     
 }//=====end of function=====
 
 public function Updatecustomeraddress($data,$addressId,$email) {
     
        $default_billing=false;
        $default_shipping=false;


        $json = array('status'=>'error','message'=>'','code'=>'','data' => array());
        if($email!=""){



            if($data->billing_status){

                    $default_billing=true;

                }
                if($data->shipping_status){

                    $default_shipping=true;

                }
             try{
                    //$proxy = new SoapClient('http://'.getSoapUrl().'/api/soap?wsdl');
                    //$sessionId = $proxy->login(getSoapUser(),getSoapPass());

                    //echo "<br />sessionId : ".$sessionId;
                    //===============get customer id===========//
                    $customer = Mage::getModel('customer/customer');
                    $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                    $customer->loadByEmail($email);
                    $customer_id=$customer->getId();
                    //===============get customer id===========//
                    //$load_customer =5;
                    if($customer_id>1){
                        $addressData =  array (
                                            "prefix" => "",
                                           "firstname" => $data->firstname,
                                            "middlename" => "",
                                            "lastname" => $data->lastname,
                                            "suffix" => "",
                                            "company" => $data->company,
                                            "street" => $data->street,
                                            "city" => $data->city ,
                                            "country_id" => "IN",
											"region" => $data->id_state,
                                           //"region" => "KL",
                                            //"region_id" => "502",
											"region_id" => $data->region_id,
                                            "postcode" => $data->postcode,
                                            "telephone" => $data->phone_mobile,
                                            "fax" => "",
                                            "is_default_billing" => $default_billing,
                                            "is_default_shipping" => $default_shipping,
                                        );



                       // $proxy->call($sessionId, 'customer_address.update', array($addressId, $addressData));

                        $address = Mage::getModel('customer/address')->load($addressId);



                        //print_r($address);exit;
                         $address->setData("prefix", $addressData["prefix"]);
                        $address->setData("firstname", $addressData["firstname"]);
                        $address->setData("middlename", $addressData["middlename"]);
                        $address->setData("lastname", $addressData["lastname"]);
                        $address->setData("suffix", $addressData["suffix"]);
                        $address->setData("company", $addressData["company"]);
                        $address->setData("street", $addressData["street"]);
                        $address->setData("city", $addressData["city"]);
                        $address->setData("country_id", $addressData["country_id"]);
                        $address->setData("region", $addressData["region"]);
                        $address->setData("region_id", $addressData["region_id"]);
                        $address->setData("postcode", $addressData["postcode"]);
                        $address->setData("telephone", $addressData["telephone"]);
                        $address->setData("fax", $addressData["fax"]);
                        $address->setData("is_default_billing", $addressData["is_default_billing"]);
                        $address->setData("is_default_shipping", $addressData["is_default_shipping"]);
                        $address->save();



                        $json["status"]="success";
                        $json["message"]="Updated successfully";

                    }else{
                         $json["status"]="error";
                        $json["message"]="Customer not exist";

                    }
            }catch(Exception $ex){
                $json["status"]="error";
                 $json["message"]="Update failed";
            }
        }else{
             $json["status"]="error";
                 $json["message"]="email not found";

        }
        return json_encode($json);
     
 }//========end of function=========//
 
 public function deleteAddress($addressId){
     $json=array("message"=>"");
    if($addressId!==""){
    try{
        $address = Mage::getModel('customer/address')->load($addressId);
        $address->delete();
        $json["message"]="Address deleted";
        //echo json_encode($json);
    }catch(Exception $ex){

        $json["message"]="Failed";
       // echo json_encode($json);
    }
    }else{

      $json["message"]="Failed";
       // echo json_encode($json);  

    }
     return json_encode($json); 
 }//======end of function==========//
 
    public function getAddressbyId($addressId,$email) {
        
        $json = array('status'=>'error','message'=>'','code'=>'','country_data'=>'','state_data'=> array(),'data' => array());
        if($email!=""){

               try{ 
               $address = Mage::getModel('customer/address')->load($addressId);
                               //print_r($address->getData());exit; 
                               $address_arr=$address->getData();
                               //echo "adress===".$address_arr["street"];
                               $json['data']["address1"]=$address_arr["street"]; 
                               $json['data']["firstname"]=$address_arr["firstname"]; 
                               $json['data']["lastname"]=$address_arr["lastname"]; 
                               $json['data']["company"]=$address_arr["company"]; 
                               $json['data']["city"]=$address_arr["city"]; 
                               $json['data']["country_id"]=$address_arr["country_id"]; 
                               $json['data']["state"]=$address_arr["region"];
                               $json['data']["postcode"]=$address_arr["postcode"];
                               $json['data']["phone_mobile"]=$address_arr["telephone"];
                               $json['data']["id_address"]=$addressId;
                                $json['data']["street"]=$address_arr["street"];
                               //$json['data']["alias"]="Shipping address";

                               $countryCollection = Mage::getModel('directory/country_api')->items();
                               $json["country_data"]=$countryCollection;


                               $regionCollection = Mage::getModel('directory/region_api')->items($address_arr["country_id"]);
                               //echo json_encode($regionCollection);
                              $json["state_data"]=$regionCollection;


                               $json["message"]="Action success";
                              $json["status"]="success";


               } catch(Exception $ex){

                   $json["message"]="Action failed";
                   $json["status"]="error";

               }

       }else{

           $json["message"]="Not logged in";
           $json["status"]="error";
       }
       return json_encode($json);
        
    }//============end of function======//
 
     public function getAddressformdata($country_id,$login_email) {
        $json=array("status"=>"success","message"=>"","data"=>null,"country_data"=>"");
        if($login_email!=""){


        if($country_id===""){
             try{

                $customer = Mage::getModel('customer/customer');
                           $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                          $customer->loadByEmail($login_email);
                          $json["data"]=$customer->toArray();


                 $countryCollection = Mage::getModel('directory/country_api')->items();
                 $json["country_data"]=$countryCollection;
                  $json["status"]="success";
                          $json["message"]="";

            }catch(Exception $ex) {
                     $json["status"]="error";
                      $json["message"]="Error";
                }     


        }else{
            try{
            $regionCollection = Mage::getModel('directory/region_api')->items($country_id);
            //echo json_encode($regionCollection);
                $json["country_data"]=$regionCollection;
                $json["status"]="success";
                $json["message"]="";
             }catch(Exception $ex) {
                     $json["status"]="success";
                      $json["message"]="Error";
                }     

        }

        }else {
            $json["status"]="success";
            $json["message"]="user not registered";
        }

        return json_encode($json);
         
     }//=========end of function===============//
     
     public function getCustomerAddresses($logindata,$email,$password) {
         
         if($email=='none'){
            //$arr_json= getCartItems($data);
            $arr_json["login"]="0";
             return json_encode($arr_json);
         }else{

             if( $email !='' && $password !=''){
                try {
                    $email =trim($email);
                    $password =trim($password);
                   // $email="nadeem.p@aufait.in";
                    //$password="nadeem";
                    $session = Mage::getSingleton('customer/session');
                    $login = $session->login($email,$password);
                    $json=array("street"=>'',"firstname"=>'',"lastname"=>'',"company"=>'',"city"=>'',"country_id"=>'',"region"=>'',"postcode"=>'',"phone_mobile"=>'');
                    $json1=array("street"=>'',"firstname"=>'',"lastname"=>'',"company"=>'',"city"=>'',"country_id"=>'',"region"=>'',"postcode"=>'',"phone_mobile"=>'');

                    if ($session->isLoggedIn()) {
                       //echo "success";



                       //====================load customer specific details=======================//

                        $session->setCustomerAsLoggedIn($session->getCustomer());
                        $customer_id=$session->getCustomerId();                  
                       $customer = Mage::getModel('customer/customer')->load($customer_id);
                        $cust_arr=$customer->getData();
                        //echo "shipping--".$cust_arr["default_shipping"];
                         //$send_data["data"]=$customer->toArray();
                        // echo json_encode($send_data);
                        if($cust_arr["default_shipping"]!='' && $cust_arr["default_shipping"]!=''){


                        $addressId1=$cust_arr["default_shipping"];
                        $addressId2=$cust_arr["default_billing"];

                        $address = Mage::getModel('customer/address')->load($addressId1);
                        //print_r($address->getData()); 
                        $address_arr=$address->getData();
                        //echo "adress===".$address_arr["street"];
                        $json["street"]=ucfirst($address_arr["street"]); 
                        $json["firstname"]=ucfirst($address_arr["firstname"]); 
                        $json["lastname"]=$address_arr["lastname"]; 
                        $json["company"]=ucfirst($address_arr["company"]); 
                        $json["city"]=ucfirst($address_arr["city"]); 
                        $json["country_id"]=$address_arr["country_id"]; 
                        $json["region"]= ucfirst($address_arr["region"]);
                        $json["postcode"]=$address_arr["postcode"];
                        $json["phone_mobile"]=$address_arr["telephone"];
                        $json["id_address"]=$addressId1;
                        $json["alias"]="Shipping address";

                        $json["id_address"]=$addressId1;
                        //======================billing address================//
                        $address1 = Mage::getModel('customer/address')->load($addressId2);
                        //print_r($address->getData()); 
                        $address_arr1=$address1->getData();
                        //echo "adress===".$address_arr["street"];
                        $json1["street"]=ucfirst($address_arr1["street"]); 
                        $json1["firstname"]=ucfirst($address_arr1["firstname"]); 
                        $json1["lastname"]=$address_arr1["lastname"]; 
                        $json1["company"]=ucfirst($address_arr1["company"]); 
                        $json1["city"]=ucfirst($address_arr1["city"]); 
                        $json1["country_id"]=$address_arr1["country_id"]; 
                        $json1["region"]=ucfirst($address_arr1["region"]);
                        $json1["postcode"]=$address_arr1["postcode"];
                        $json1["phone_mobile"]=$address_arr1["telephone"];
                        $json1["id_address"]=$addressId2;
                        $json1["alias"]="Billing address";
                        //$addressId2=3;
                        $json1["id_address"]=$addressId2;
                        //======================end of billing address==========//


                        
                        //=================end of customer specific details============================//
                        //$json["login"]="1";
                         //$arr_json= getCartItems($data);
                         $arr_json["login"]=1;
                         $arr_json["addresses"][0]=$json;
                         $arr_json["addresses"][1]=$json1;
                          //echo json_encode($arr_json);

                        }else{
                            $arr_json["addresses"]='';
                            //echo json_encode($arr_json);
                        }

                        //======get other addresses===============//
                        $customer = Mage::getModel('customer/customer');
                        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                        $customer->loadByEmail($email);
                        //$customer_id=$customer->getId();
                         //$customer = Mage::getModel('customer/customer')->load($customer_id);
                         $customerAddress = array();
                        #loop to create the array
                         //print_r($customer->getAddresses());exit;
                        foreach ($customer->getAddresses() as $address)
                        {
                            $temp_arr=$address->toArray();
                            $temp_arr["id_address"]=$temp_arr["entity_id"];
                              if($temp_arr["entity_id"]!==$addressId1 && $temp_arr["entity_id"]!==$addressId2){

                                 // $customerAddress[] = $address->toArray();
                                  $temp_arr["firstname"]=ucfirst($temp_arr["firstname"]);
                                  $temp_arr["street"]=ucfirst($temp_arr["street"]);
                                   $temp_arr["company"]=ucfirst($temp_arr["company"]);
                                   $temp_arr["city"]=ucfirst($temp_arr["city"]);
                                  $customerAddress[] = $temp_arr;
                              }

                           //print_r($customerAddress) ."----------------------";
                        }

                        $arr_json["other_addresses"]=$customerAddress;
                        //echo json_encode($arr_json);
                        return json_encode($arr_json);
                        //===========get other addresses=============//


                    } else {
                       //echo "fails";
                       //$send_data["success"]=false;
                   }
               } catch (Exception $ex){

                    Zend_Debug::dump( strip_tags($ex->getMessage()));

               }
            }

         }

         
     }//=========end of function===============//
 
}//=============end of class=================//