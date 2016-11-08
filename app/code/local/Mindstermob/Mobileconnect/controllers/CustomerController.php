<?php
class Mindstermob_Mobileconnect_CustomerController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        
        
    }
    
    public function loginAction(){
        
        $params =json_decode(file_get_contents('php://input'));
      
        if( $params->email !='' && $params->passwd !=''){
            $email =trim($params->email);
            $password =trim($params->passwd);
             $model = Mage::getModel('mobileconnect/customer'); 
             
              $out=$model->customer_login($email,$password);
                $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody($out);

        }
        
    } //=========login action===========//
    
    public function registerAction(){
        
        $params =json_decode(file_get_contents('php://input'));
        if( $params->email !='' && $params->passwd !='' && $params->firstname !='' && $params->lastname !='' && $params->repasswd !='' ){
            $firstname=trim($params->firstname);
          $lastname=trim($params->lastname);
          $email=trim($params->email);
          $passwd=trim($params->passwd);
           $model = Mage::getModel('mobileconnect/customer'); 
           $out=$model->customer_register($firstname,$lastname,$email,$passwd);
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody($out);
           
            
        }else{
            
            $json = array('status'=>'error','message'=>'Parameter invalid','code'=>'','data' => array());
            $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody(json_encode($json));
        }
        
        
        
    }//========register action=======//
   public function profileAction(){
       $params =json_decode(file_get_contents('php://input'));
      $login_email=$params->email;
      if($login_email!=""){
           $model = Mage::getModel('mobileconnect/customer'); 
          $out=$model->getCustomerprofile($login_email);
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
           $this->getResponse()->setBody($out);
      }else {
          $json=array("success"=>false,"message"=>"Not registered","data"=>"");
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody(json_encode($json));
       
        }
       
   } //=======end of function======//
  
     public function forgotpassAction() {
         $params =json_decode(file_get_contents('php://input'));
         if( $params->email !=''  ){
              $email=trim($params->email);
           $model = Mage::getModel('mobileconnect/customer'); 
          $out=$model->customer_forgotpassword($email);
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
             
             
         }
         
     }
     public function updateAction() {
         
         $params =json_decode(file_get_contents('php://input'));
         $data=$params->data;
        $logindata=$params->logindata;
         $login_email=$logindata->email;
         $login_password=$logindata->password;

         $formdata=$params->formdata;

         $newEmail=$formdata->email;
         $firstname=$formdata->firstname;
         $lastname=$formdata->lastname;
         $model = Mage::getModel('mobileconnect/customer'); 
          $out=$model->Updatecustomerprofile($login_email,$login_password,$newEmail,$firstname,$lastname);
           $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
         
     }
     
     public function changepassAction() {
         $params =json_decode(file_get_contents('php://input'));
        //echo print_r($params->data);
        //echo $params->data[0]->id;


        //$data=$params->data;
        //$data=$params->data;
        $logindata=$params->logindata;
         $login_email=$logindata->email;
         $login_password=$logindata->passwd;

         $formdata=$params->formdata;

         $current_pass=$formdata->current_passwd;
         $new_pass=$formdata->new_passwd;
         
         $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->ChangeCustomerpassword($login_email,$login_password,$current_pass,$new_pass);
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
         
     }//======end of function========//
     
    public function ordersAction() {
        
    $params =json_decode(file_get_contents('php://input'));
    //echo print_r($params->data);
    //echo $params->data[0]->id;


    //$data=$params->data;
    $logindata=$params->logindata;
     $email=$logindata->email;
     //echo $email;
     $password=$logindata->passwd;    
     $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->getCustomerorders($email,$password);
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
        
    }
     public function orderdetailsAction() {
         $params =json_decode(file_get_contents('php://input'));
        //echo print_r($params->data);
        //echo $params->data[0]->id;


        //$data=$params->data;
        $order_id=$params->id_order;
        $logindata=$params->logindata;
         $email=$logindata->email;
         //echo $email;
         $password=$logindata->passwd;
         
         $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->getCustomerorderdetails($order_id,$email,$password);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
         
         
     }//==========end of action===========//
   public function addaddressAction() {
       
       $params =json_decode(file_get_contents('php://input'));
        //echo print_r($params->data);
        //echo $params->data[0]->id;


        $data=$params->formdata;
        $logindata=$params->logindata;
         $email=$logindata->email;
         $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->addAddress($data,$email);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
         
   }//===========end of add address function========//
   
   public function updateaddressAction(){
       
       $params =json_decode(file_get_contents('php://input'));
        $data=$params->formdata;
        $addressId =$params->id_address;
        $logindata=$params->logindata;
        $email=$logindata->email;
        $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->Updatecustomeraddress($data,$addressId,$email);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
       
   }//============end of customer adddress==========//
   
    public function deleteaddressAction() {
        
        $params =json_decode(file_get_contents('php://input'));
        //$data=$params->formdata;
        //$logindata=$params->logindata;
         //$email=$logindata->email;
        $addressId=$params->id_address;
         $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->deleteAddress($addressId);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
        
        
    }//======end of function============//
    
    public function addressbyidAction() {
        
        $params =json_decode(file_get_contents('php://input'));

        $addressId =$params->id_address;
        $logindata=$params->logindata;
         $email=$logindata->email;
         $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->getAddressbyId($addressId,$email);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
        
    }//=======end of function============//
    public function addressformdataAction() {
        $params =json_decode(file_get_contents('php://input'));
        //$data=$params->formdata;
        $country_id =$params->country_id;
        $login_email=$params->email;
        $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->getAddressformdata($country_id,$login_email);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
        
    }//=====endof function=========//
    
    public function addressesAction() {
        
        $params =json_decode(file_get_contents('php://input'));       
        $logindata=$params->logindata;
         $email=$logindata->email;
         $password=$logindata->password;         
        $model = Mage::getModel('mobileconnect/customer'); 
         $out=$model->getCustomerAddresses($logindata,$email,$password);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
        
        
    }//=====endof function=========//
    
    
     
}//====end of class=========//

