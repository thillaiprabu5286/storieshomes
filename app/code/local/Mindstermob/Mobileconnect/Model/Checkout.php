<?php
class Mindstermob_Mobileconnect_Model_Checkout extends Mage_Core_Model_Abstract {
   
    public function _construct()
    {   
       $this->_init('mobileconnect/checkout');
    }
    
    public function Checkout_cart($data,$email,$password){
        if($email=='none'){
            $arr_json= $this->getCartItems($data);
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
                    $json_bill=array("street"=>'',"firstname"=>'',"lastname"=>'',"company"=>'',"city"=>'',"country_id"=>'',"region"=>'',"postcode"=>'',"phone_mobile"=>'');

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
                        $addressId2=$cust_arr["default_billing"];
                        $address = Mage::getModel('customer/address')->load($addressId1);
                        //print_r($address->getData()); 
                        $address_arr=$address->getData();
                        //echo "adress===".$address_arr["street"];
                        $json["street"]=$address_arr["street"]; 
                        $json["firstname"]=$address_arr["firstname"]; 
                        $json["lastname"]=$address_arr["lastname"]; 
                        $json["company"]=$address_arr["company"]; 
                        $json["city"]=$address_arr["city"]; 
                        $json["country_id"]=$address_arr["country_id"]; 
                        $json["region"]=$address_arr["region"];
                        $json["postcode"]=$address_arr["postcode"];
                        $json["telephone"]=$address_arr["telephone"];
                        $json["id_address"]=$addressId1;
                        $json["alias"]="Shipping address";

                        $json["id_address"]=$addressId1;
                        //======================billing address================//
                        $address1 = Mage::getModel('customer/address')->load($addressId2);
                        //print_r($address->getData()); 
                        $address_arr1=$address1->getData();
                        //echo "adress===".$address_arr["street"];
                        $json1["street"]=$address_arr1["street"]; 
                        $json1["firstname"]=$address_arr1["firstname"]; 
                        $json1["lastname"]=$address_arr1["lastname"]; 
                        $json1["company"]=$address_arr1["company"]; 
                        $json1["city"]=$address_arr1["city"]; 
                        $json1["country_id"]=$address_arr1["country_id"]; 
                        $json1["region"]=$address_arr1["region"];
                        $json1["postcode"]=$address_arr1["postcode"];
                        $json1["telephone"]=$address_arr1["telephone"];
                        $json1["id_address"]=$addressId2;
                        $json1["alias"]="Billing address";
                        //$addressId2=3;
                        $json1["id_address"]=$addressId2;
                        //======================end of billing address==========//

                        //=================end of customer specific details============================//
                        //$json["login"]="1";
                         $arr_json= $this->getCartItems($data);
                         $arr_json["login"]=1;
                         if($json["street"]===null && $json1["street"]==null){
                             $arr_json["addresses"]="";
                         }else{
                         $arr_json["addresses"][0]=$json;
                         $arr_json["addresses"][1]=$json1;

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
                         $i=2;
                        foreach ($customer->getAddresses() as $address)
                        {
                            $temp_arr=$address->toArray();
                            $temp_arr["id_address"]=$temp_arr["entity_id"];
                              if($temp_arr["entity_id"]!==$addressId1 && $temp_arr["entity_id"]!==$addressId2){

                                 // $customerAddress[] = $address->toArray();
                                 // $customerAddress[] = $temp_arr;
                                  $temp_arr["alias"]=$temp_arr["firstname"]."  ".$temp_arr["lastname"]." ".$temp_arr["street"];
                                  $arr_json["addresses"][$i]=$temp_arr;
                                  $i++;
                              }

                           //print_r($customerAddress) ."----------------------";
                        }

                        //$arr_json["other_addresses"]=$customerAddress;
                        //===============end of get other addresses============// 

                        return json_encode($arr_json);

                    } else {
                       //echo "fails";
                       //$send_data["success"]=false;
                   }
               } catch (Exception $ex){

                   Zend_Debug::dump( strip_tags($e->getMessage()));

               }
            }




         }
        
    }//========end of function========//
    
     public function getCartItems($data){
         $base_url=Mage::getModel('mobileconnect/baseurl');
         $json = array('status'=>'0','login'=>'0','message'=>'','items' => array(),'cart'=>array(),'grandtotal'=>'0','addresses'=>array());
            $grand_total=0;
           foreach($data as $cart){
               if($cart->id != ''){
                   $product_id=$cart->id;
                   //$json = array('success' => true, 'product' => array());
                   $product = Mage::getModel('catalog/product')->load($product_id);
                   $details=$product->getData();
                   //---get stock details------------------//
                   $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
                   $stockItemData = $stockItem->getData();
                  // if($stockItemData["qty"] > $cart->qty){
                       $json["status"]="1";
                       $instock=1;
                       //$details["is_in_stock"]
                       if($stockItemData["qty"] < $cart->qty && $cart->qty != $stockItemData["qty"]){
                            $instock=0;
                       }
					   $tot_price =number_format($details["price"],2)* $cart->qty;
                       $json["items"][]=array( "stock_quantity"=>$stockItemData["qty"],
                           "qty"=>$cart->qty,
                           "id"=>$product_id,
                            "name"=>$details["name"],
                            //"price"=>$details["price"]+0,   
							"price"=> number_format($details["price"],2),
                            //"total_price"=>$details["price"]* $cart->qty,
							
							 "total_price"=> number_format($tot_price,2),
                            "description"=>$details["description"],
                            "short_description"=>$details["short_description"],
                            "is_in_stock"=>$instock,
                           /* "image"=> getBaseurl()."media/catalog/product".$details["image"],*/
                           "image"=> $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),
                            "thumbnail"=>$details["thumbnail"],
                           "options"=>$cart->options,
						 "currency_code" => "Rs.",
                        );
                       $grand_total=$grand_total+($details["price"]* $cart->qty);
                       $json["cart"][]=array("id"=>$cart->id,"qty"=>$cart->qty,"options"=>$cart->options);

               }else{
                   //$json = array('success' => false, 'product' => array());
                  // echo json_encode($json);

               }
           }

           //$json["grandtotal"]=$grand_total;
		    $json["grandtotal"] = number_format($grand_total,2);
           $ship_rate=Mage::getStoreConfig('carriers/flatrate/price');
           $json["ship_rate"]=$ship_rate;
           //$json["net_amount"]=$grand_total+$ship_rate;
		   $net_amt = $grand_total+$ship_rate;
		   $json["net_amount"] = number_format($net_amt,2);
           $json["currency_code"] => "Rs.";
           return $json;
         
         
     }//======end of function=======//
     
      public function Checkout_payment($cartitems,$email,$addressId1,$payment_method){
          $ouput_arr=array("payment_method"=>"","order_id"=>"","message"=>"","post"=>null,"status"=>"0");


            //=============save order and get order id================================//
            //$payment_methods=array("cashondelivery","hdfc","hdfc_debit","hdfc_net");

            //$cartitems=array(array("id"=>"4","qty"=>"1"));
            //$email="nadeem.p@aufait.in";
            //$payment_method="cashondelivery";
            //$addressId1="2";
            //echo "cart items".json_encode($email);exit;
            $order_id=$this->saveSalesOrder($cartitems,$payment_method,$email);
            //echo "order_id".$order_id;exit;
            //==============end of get order id=========================//
            //===============load order=================================//
            //echo $order_id;exit;
            //return $order_id;
            //$ouput_arr["message"]=$order_id;
            if($order_id<0){
                $ouput_arr["message"]="Order Fails";
                $ouput_arr["status"]="0";
				$ouput_arr["post"] = null;
                return json_encode($ouput_arr);
               // return;
            }
             $ouput_arr["status"]="1";
            $_order = new Mage_Sales_Model_Order();
            //$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            //$session = Mage::getSingleton('customer/session');
             //echo "order id-".$order_id; exit;      
            $_order->loadByIncrementId($order_id);
            $type = $_order->getPayment()->getMethod();
            //echo "type--".$type;
            $address_i = Mage::getModel('customer/address')->load($addressId1);
            //print_r($address->getData()); 
            $address_arr=$address_i->getData();
            //echo "adress===".$address_arr["street"];
            $adress_final=$address_arr["firstname"]." ".$address_arr["lastname"].", ".$address_arr["street"].", ".$address_arr["city"].", ".$address_arr["region"].", "."PIN-".$address_arr["postcode"].", Phone-".$address_arr["telephone"];
            $ship_address=$adress_final;
            //echo "adreess".$adress_final;
            //$address = $_order->getBillingAddress();
            //$ship_address = $_order->getShippingAddress();
            //print_r($_order);
            //echo "all--".$type."address--".$address;
            //==============end of load order===========================//

            //=================load customer from customer id==============//
            $websiteId = Mage::app()->getWebsite()->getId();
                 $store = Mage::app()->getStore();
                 // Start New Sales Order Quote
                 // Set Sales Order Quote Currenccy
                 $customer = Mage::getModel('customer/customer')
                             ->setWebsiteId($websiteId)
                             ->loadByEmail($email);
               $customer_id=$customer->getId();
               //echo "cust id --".$customer_id;
            //=================end of load customer from customer id=========//

            //=================get payment method from order=========check for valid method===========//

            //=================End of get payment method from order========================//  
               $ouput_arr["payment_method"]=$type;
               $ouput_arr["order_id"]=$order_id;
               if($type=="cashondelivery"){
                   $ouput_arr["message"]="Your Order with order Number-".$order_id." Received. Please check status in My orders page";
                   return json_encode($ouput_arr);

                   //echo "Your Order recieved. Please check status in Myorders page";

               }else{
                 //===============code for hdfc payments=============================
                    $hdfc_methods = array(
                      'hdfc' => '1',
                      'hdfc_debit' => '2',
                      'hdfc_net' => '3'
                  ); 
                  $model = Mage::getModel('hdfc/credit');
                  $customerid = $model->getConfigData('customer_id_mob');
                  $secret = $model->getConfigData('secretmob');
                  
                  $address_arr["street"] = str_replace("\n", " ", $address_arr["street"]);
                  
//                  echo "customer id $customerid";exit;
                  //echo "cust id --".$customerid."secret---".$secret;
                 // echo "Total-".$_order->getCustomerEmail();
                  //echo "uri--".Mage::getUrl('hdfc/payment/response', array('_secure' => true));exit;
                    $post = array(
                          'payment_mode' => $hdfc_methods[$type],
                          'channel' => '10',
                          'account_id' => $customerid,
                          'reference_no' => $order_id, //'STRORD'.$orderId,
                          'mode' => 'LIVE',
                          'currency' => 'INR',
                          'display_currency' => 'INR',
                          'display_currency_rates' => '1',
                          'description' => "Stories life order " . $order_id . ' INR ' . number_format($_order->getBaseGrandTotal(), 2) . " for customer " . $_order->getCustomerEmail(),
//                          'return_url' => Mage::getUrl('hdfc/payment/response', array('_secure' => true)),
                          'return_url' => 'http://www.storieshomes.com/index.php/mindster/Checkout/response',
                          'card_brand' => '',
                          'payment_option' => '',
                          'bank_code' => '',
                          'emi' => '',
                          'page_id' => '',
                          'name' => $address_arr["firstname"] ,
                          'ship_name' => $address_arr["firstname"],
                          'address' => $address_arr["street"],
                          'ship_address' => $address_arr["street"],
                          'city' => $address_arr["city"],
                          'ship_city' => $address_arr["city"],
                          'state' => $address_arr["region"],
                          'ship_state' => $address_arr["region"],
                          'country' => 'IND',
                          'ship_country' => 'IND',
                          'postal_code' => $address_arr["postcode"],
                          'ship_postal_code' => $address_arr["postcode"],
                          'phone' => $address_arr["telephone"],
                          'ship_phone' => $address_arr["telephone"],
                          'email' => $_order->getCustomerEmail(),
                          'amount' => number_format($_order->getBaseGrandTotal(), 2, '.', ''),
                        'secureKey' => $secret
                      );
                  $hashData = $secret;
                  ksort($post);
                  foreach ($post as $key => $value) {
                      if (strlen($value) > 0) {
                          $hashData .= '|' . $value;
                      }
                  }
                  if (strlen($hashData) > 0) {
//                      $post['secure_hash'] = strtoupper(hash('sha512', $hashData));
                  }
                  
//                  Mage::log('hash stirng: '. $hashData. '');
//                  Mage::log('post data: '. json_encode($post ). '');
//                  Mage::log('secure hash: '. $post['secure_hash']);
                  //$url = 'http://www.google.com/';
                  //Mage::app()->getResponse()->setRedirect($url);

                   $ouput_arr["message"]="Your Order with order Number-".$order_id." recieved. Please check status in My orders page";
                   $ouput_arr["post"]=$post;
                   //Mage::getSingleton("core/session")->setPostvalue($post); 
                    //Zend_Registry::set('post', $post);
                    //Mage::app()->loadLayout();
                    //$block = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Template', 'hdfc', array('template' => 'hdfc/redirect.phtml'));
                   // Mage::app()->getLayout()->getBlock('content')->append($block);
                   // Mage::app()->renderLayout();



                   return json_encode($ouput_arr);
                //  echo json_encode($post);
                  //Zend_Registry::set('post', $post);
                  //$this->loadLayout();
                  //$block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'hdfc', array('template' => 'hdfc/redirect.phtml'));
                  //$this->getLayout()->getBlock('content')->append($block);
                  //$this->renderLayout();


               }//==================end of else of hdfc payments=========================
    
          
      }//===========end of functions==========//
      
      public function Checkout_payment_new($post){
        // print_r($post); exit;
          //error_log(print_r($post,true), 3, "/home/stori5i7/public_html/var/errortest.txt");
        $ouput_arr = array("payment_method" => "", "order_id" => "", "message" => "", "post" => "", "status" => "0");
        
         if (empty($post)) {
            $ouput_arr["message"] = "Unknown error occured post";
            return json_encode($ouput_arr);
        }
        
       

        $order = new Mage_Sales_Model_Order();
        //$session = Mage::getSingleton('customer/session');
        $model = Mage::getModel('hdfc/credit');
        //$secret = $model->getConfigData('secret'); 
//        $secret = '47ad360d9ba29ec21caef9f91fd4d753';
        
        //echo $secret; exit;
        
//       
//        $hash = $post['SecureHash'];
//       // echo $hash; exit;
//        unset($post['SecureHash']);
//        
//        ksort($post);
//    
//        $hashData = '';
//        $genHash = '';
//      
//        foreach ($post as $key => $value) {
//         
//            if (strlen($value) > 0) {
//                $hashData .= strtoupper($key) . '=' . $value;
//            }
//           
//        }
//        $hashData .= $secret;
//      //echo $hashData;exit;
//        if (strlen($hashData) > 0) {
//            $genHash = strtoupper(hash('sha1', $hashData));
//        }
         //echo $hash.'<br>'.$genHash;exit;
//        if ($hash !== $genHash) {
//            $ouput_arr["message"] = "Unknown error occured hash";
//             return json_encode($ouput_arr);
//        }
       
       
      
        //$order_id=  str_replace('STRORD', '', $post['MerchantRefNo']);
//        var_dump($post);exit;
//        echo $post->MerchantRefNo;exit;
        $order_id = $post->MerchantRefNo;
      //  echo $order_id; exit;
        $order->loadByIncrementId($order_id);
        $pay = $order->getPayment();
        $pay->setTransactionAdditionalInfo('hdfcTransactionID', $post->TransactionID);
        // $pay->setTransactionAdditionalInfo('hdfcRequestID', $post['RequestID']); // request id is not avliable currently
        $pay->setTransactionAdditionalInfo('hdfcPaymentID', $post->PaymentID);
        $bank_data = array();
        $bank_data['hdfcTransactionID'] = $post->TransactionID;
        $bank_data['hdfcRequestID'] = '';
        $bank_data['hdfcPaymentID'] = $post->PaymentID;
        $pay->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $bank_data
        );
        $pay->save();
        if ($post->ResponseCode != '0') {
            $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            Mage::getSingleton('checkout/session')->unsLastRealOrderId();
            
            $ouput_arr["message"] = "Payment Failed";
//            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
//            return;
            return json_encode($ouput_arr);
        }
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
        $ouput_arr["status"] = 1;
        $ouput_arr["message"] = "Payment Success";
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();





        return json_encode($ouput_arr);
        //  echo json_encode($post);
        //Zend_Registry::set('post', $post);
        //$this->loadLayout();
        //$block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'hdfc', array('template' => 'hdfc/redirect.phtml'));
        //$this->getLayout()->getBlock('content')->append($block);
        //$this->renderLayout();
    }

//==================end of else of hdfc payments=========================
    
          
      
      
    public function saveSalesOrder($productids,$payment_method,$email){
    //$productids=array(4);
   // $email="nadeem.p@aufait.in";
    
    //if($payment_method!="cashondelivery"){
        
         
        
        
        
    //else{
    $out_err = array("ids"=>$productids,"payment method"=>$payment_method,"email"=>$email);
    error_log(print_r($out_err,true), 3, "/home/stori5i7/public_html/var/errortest.txt");
        try{



         $websiteId = Mage::app()->getWebsite()->getId();
         $store = Mage::app()->getStore();
         // Start New Sales Order Quote
         $quote = Mage::getModel('sales/quote')->setStoreId($store->getId());
         
         // Set Sales Order Quote Currency
//         $quote->setCurrency($order->AdjustmentAmount->currencyID);
         $customer = Mage::getModel('customer/customer')
                     ->setWebsiteId($websiteId)
                     ->loadByEmail($email);
         /*if($customer->getId()==""){
             $customer = Mage::getModel('customer/customer');
             $customer->setWebsiteId($websiteId)
                     ->setStore($store)
                     ->setFirstname('Jhon')
                     ->setLastname('Deo')
                     ->setEmail($email)
                     ->setPassword("password");
             $customer->save();
         }*/
         /// for guest orders only:
         //$quote->setCustomerEmail(‘customer@email.com’);
         // Assign Customer To Sales Order Quote
         $quote->assignCustomer($customer);

             // Configure Notification
         $quote->setSendCconfirmation(1);
         //$quote->getSendConfirmation(null);
         // $quote->sendNewOrderEmail();
         //print_r($productids);exit;
         foreach($productids as $ids){
              // print_r($ids);exit;
             $product=Mage::getModel('catalog/product')->load($ids->id);
             
             if($ids->options->titles){
                  //echo "in if";exit;
                foreach($ids->options->titles as $titles){
                    
                    $title_id=$titles->title_id;
                    $val_key=$titles->key;
                   $quote->addProduct($product,new Varien_Object(array('qty' => $ids->qty, 'options'=>array($title_id=>$val_key ) )));

                    
                }
            }else{
              //print_r($product);exit;
              $quote->addProduct($product,new Varien_Object(array('qty' => $ids->qty,'options'=>array("") )));
                
              //$quote->addProduct($product,new Varien_Object(array('qty' => $ids->qty, 'options'=>array(6=>16 ) )));

                
            }
             //Mage::app()->cleanCache();
             //var_dump($product);exit;

             //echo "quantity--".$ids["qty"];exit;
            // $existentOptions = $orderItem->getProductOptions();
           // $existentOptions['your_custom_option'] = $yourCustomValue;
          //   $orderItem->setProductOptions($existentOptions); 
             
             //$quote->addProduct($product,new Varien_Object(array('qty' => $ids->qty)));
         }
         $billingAddress = $quote->getBillingAddress();
         $shippingAddress = $quote->getShippingAddress();
          //    echo($shippingAddress->getShippingMethod());exit; 
         // Set Sales Order Billing Address
         /*
         $billingAddress = $quote->getBillingAddress()->addData(array(
             'customer_address_id' => '',
             'prefix' => '',
             'firstname' => 'Nadeem',
             'middlename' => '',
             'lastname' =>'P',
             'suffix' => '',
             'company' =>'', 
             'street' => array(
                     '0' => 'Noida',
                     '1' => 'Sector 64'
                 ),
             'city' => 'Noida',
             'country_id' => 'IN',
             'region' => 'UP',
             'postcode' => '201301',
             'telephone' => '78676789',
             'fax' => 'gghlhu',
             'vat_id' => '',
             'save_in_address_book' => 1
         ));

         // Set Sales Order Shipping Address
        $shippingAddress = $quote->getShippingAddress()->addData(array(
             'customer_address_id' => '',
             'prefix' => '',
             'firstname' => 'Nadeem',
             'middlename' => '',
             'lastname' =>'P',
             'suffix' => '',
             'company' =>'', 
             'street' => array(
                     '0' => 'Noida',
                     '1' => 'Sector 64'
                 ),
             'city' => 'Noida',
             'country_id' => 'IN',
             'region' => 'UP',
             'postcode' => '201301',
             'telephone' => '78676789',
             'fax' => 'gghlhu',
             'vat_id' => '',
             'save_in_address_book' => 1
         ));*/
//         if($shipprice==0){
//             $shipmethod='freeshipping_freeshipping';
//         }

         // Collect Rates and Set Shipping & Payment Method
         //$payment=$quote->getPayment();
         //$payment->setMethod($payment->getMethod());

       //  $shippingAddress->setCollectShippingRates(true)
        //                 ->collectShippingRates()
                         /*->setShippingMethod('flatrate_flatrate')*/
         //                 ->setShippingMethod('Table Rate')
          //               ->setPaymentMethod($payment_method);
        //=new code=================
         $shippingAddress->removeAllShippingRates()
                ->setCollectShippingRates(true)
                ->setShippingMethod('flatrate_flatrate')
                ->setShippingDescription('Flat Rate - Best Way');
         //===new code===============


         // Set Sales Order Payment
         $quote->getPayment()->importData(array('method' => $payment_method));

         // Collect Totals & Save Quote
         $quote->collectTotals()->save();
         //if($payment_method!="cashondelivery"){
             //$order_insert_id=$quote->getId;
           //  return $order_insert_id;
         //}else{         
            // Create Order From Quote
            //var_dump($quote);
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            //$service->sendNewOrderEmail();

            // var_dump($service);
            //$increment_id = $service->getOrder()->getRealOrderId();
           // $increment_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            // Resource Clean-Up
            //$quote = $customer = $service = null;
             //return $increment_id;
            //================new code==================//
            $order = $service->getOrder();
            $order_insert_id=$order->getIncrementId();
            if($patyment_method=="cashondelivery"){
                $order->sendNewOrderEmail();
            }
            return $order_insert_id;
        // }
         
         } catch(Exception $ex){
            // return $ex->getMessage();
            $order_insert_id=-1;
            return $order_insert_id;
            //Zend_Debug::dump( strip_tags($ex->getMessage()));
            //return $ex->getMessage();

        } 
         //var_dump($order);
        //printf("Created order %s\n", $order->getIncrementId());
        //Code for Magento enterprise.
        //require_once ‘app/Mage.php’;
        //Mage::app();
        /*
        $id=1; // get Customer Id
        $customer = Mage::getModel(‘customer/customer’)->load($id);
        $transaction = Mage::getModel(‘core/resource_transaction’);
        $storeId = $customer->getStoreId();
        $reservedOrderId = Mage::getSingleton(‘eav/config’)->getEntityType(‘order’)->fetchNewIncrementId($storeId);
        $order = Mage::getModel(‘sales/order’)
        ->setIncrementId($reservedOrderId)
        ->setStoreId($storeId)
        ->setQuoteId(0)
        ->setGlobal_currency_code(‘USD’)
        ->setBase_currency_code(‘USD’)
        ->setStore_currency_code(‘USD’)
         //=================end of new code============//
         */
         //echo "order id".$increment_id;
         // Finished
         //return $increment_id;

    }//====end of function==========//
    
    
}//===========end of class================//


?>