<?php

class Mindstermob_Mobileconnect_ProductController extends Mage_Core_Controller_Front_Action
{
    
    public function indexAction()
    {
        
 //$category_id=$this->getRequest()->getPost();
   //echo "sjhsjs".$category_id;
       $model = Mage::getModel('mobileconnect/products'); 
       //$obj = new Mindstermob_Mobileconnect_Model_Products();
      ///var_dump($obj);
      //echo("class name-".get_class(Mage::getModel('mobileconnect/products')));
    // var_dump($model);
       $params =json_decode(file_get_contents('php://input'));
        //echo $params; exit;
        if( $params->category_id !=''){
          $category_id=$params->category_id;
          $products=$model->getproducts($category_id);

          //echo json_encode($products);
          //echo $category_id;
           $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody(json_encode($products));


        }else{

           $allCategories=$model->getCategoriestree(); 
            //$allCategories='[{"id":6,"name":"Mobile Phones"},{"id":7,"name":"Tablets"}]';
          // echo $allCategories; 
           //echo "hi";
             $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody($allCategories);
           

        }

        //$response=array("name"=>"nadeem");
      
        
        
        
        
        
        
    }
    
    public function searchAction(){
        $params =json_decode(file_get_contents('php://input'));

       $model = Mage::getModel('mobileconnect/products'); 
        if( $params->query_text !=''){
          $searchstring=$params->query_text;
           $out=$model->getSearchresults($searchstring);
             $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody($out);
        }else{
             $json=array('success' => false,"products"=>array());
             $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody(json_encode($json));
        }
    }//=====end of search action====//
    
    //=========Api for quick view in home page=========//
	public function ajaxviewAction()
    {
        //print_r('<pre>');
        //print_r($this->getRequest ()->getParam ( 'id' ));
        //exit;
         //$params =(file_get_contents('php://input'));
        $model = Mage::getModel('mobileconnect/products'); 
         $p_id = $this->getRequest ()->getParam ( 'id' );
         //echo print_r($params);exit;
         //$out1 = array("data"=>$p_id);
         
          $out=$model->getQuickview($p_id);
         
         
         
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
    }
    public function addReviewAction(){
        $json = array('success' => false, 'product' => array());
        $model = Mage::getModel('mobileconnect/products'); 
        $params =json_decode(file_get_contents('php://input'));
        if($params->product_id != '' && $params->product_id != ''){
            $product_id = $params->product_id;
            $customer_id = $params->customer_id;
            $out = $model->addReview($product_id,$customer_id,$params->data);
            if($out == "saved"){
                $json = array('success' => true, 'message' => "saved successfully");
            }else{
                $json = array('success' => false, 'message' => $out);
            }
            
             $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody(json_encode($json));



            }else{
                $json = array('success' => false, 'message' => "parameter missing");
               // echo json_encode($json);
                $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
               $this->getResponse()->setBody(json_encode($json));

            }
        
        
    }
    public function detailsAction()
    {
           $model = Mage::getModel('mobileconnect/products'); 
            $params =json_decode(file_get_contents('php://input'));

            if($params->product_id != ''){
            $product_id=$params->product_id;
            $out=$model->getProductdetails($product_id);
             $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
             $this->getResponse()->setBody($out);



            }else{
                $json = array('success' => false, 'product' => array());
               // echo json_encode($json);
                $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
               $this->getResponse()->setBody(json_encode($json));

            }

         
         
    }//=====end of details=======//
    
    public function galleryAction(){
        
        $params =json_decode(file_get_contents('php://input'));
        $product_id= $params->product_id;
        $model = Mage::getModel('mobileconnect/products'); 
        
        $out= $model->getGalleryimages($product_id);
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out); 
            
        
        
    }//==========end of gallery====//
    
    public function cmsAction(){
         $json = array('success' => false, 'page' =>'' );
         $params =json_decode(file_get_contents('php://input'));
         $page_id= $params->page_id;
         $page = Mage::getModel('cms/page');
        $page->setStoreId(Mage::app()->getStore()->getId());
        $page->load($page_id);
        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($page->getContent());
        $json["page"]=$html;
        $json["success"]=true;
        $out= json_encode($json);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out); 
         
     }
     public function contactAction(){
         $json = array('success' => false, 'message' =>'' );
         $params =json_decode(file_get_contents('php://input'));
        //if( $params->email !='' && $params->passwd !='' && $params->firstname !='' && $params->lastname !='' && $params->repasswd !='' ){
            $firstname=trim($params->firstname);
         $phone=trim($params->phone);
          $email=trim($params->email);
          $comment=trim($params->comment);
            $post = array('name'=>$firstname,'email'=>$email,'telephone'=>$phone,'comment'=>$comment);
            $postObject = new Varien_Object();
            $postObject->setData($post);
            $translate = Mage::getSingleton('core/translate');
            $mailTemplate = Mage::getModel('core/email_template');
            /* @var $mailTemplate Mage_Core_Model_Email_Template */

            try{
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
             ->setReplyTo($post['email'])
             ->sendTransactional(
              Mage::getStoreConfig('contacts/email/email_template'),
              Mage::getStoreConfig('contacts/email/sender_email_identity'),
              Mage::getStoreConfig('contacts/email/recipient_email'),
              null,
              array('data' => $postObject)
             );
             $translate->setTranslateInline(true);
             //echo Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');
               $json = array('success' => true, 'message' =>'Thank you for contacting us' );
            }catch(Exception $e){
               $json = array('success' => false, 'message' =>'Unable to submit your request' );
              //echo Mage::helper('contacts')->__('Unable to submit your request. Please, try again later');

            }
           $out= json_encode($json);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out); 
         
     }
    
     //=================AUF edit filter all products=========//
     public function filterproductsAction(){
         $params = json_decode(file_get_contents('php://input'));
        $category_id = $params->category_id;
        $material_sel = $params->material_sel;
        $color_sel = $params->color_sel;
        $price_upper = $params->price_upper;
        $subcategory_id = $params->subcategory_id;
        $model = Mage::getModel('mobileconnect/products'); 
        
        $out = $model->filterProducts($category_id,$subcategory_id,$material_sel,$color_sel,$price_upper);
        //$json["reviews"] = $out;
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($out)); 
}
     //================AUF edit All reviews===============//
     
     public function allreviewsAction(){
          
        $params = json_decode(file_get_contents('php://input'));
        $product_id = $params->product_id;
        $model = Mage::getModel('mobileconnect/products'); 
        
        $out = $model->getAllreviews($product_id);
        //$json["reviews"] = $out;
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($out)); 
         
     }
     public function allnewarrivalsAction(){
       
        $params = json_decode(file_get_contents('php://input'));
        //$product_id = $params->product_id;
        $model = Mage::getModel('mobileconnect/products'); 
        
        $out = $model->getAllnewarrivals();
        $json["new_arrivals"] = $out;
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($json)); 
         
     }
     public function allmostviewedproductsAction(){
       
        $params = json_decode(file_get_contents('php://input'));
        $product_id = $params->product_id;
        $model = Mage::getModel('mobileconnect/products'); 
        
        $out = $model->getAllMostViewedProducts($product_id);
        $json["most_viewed"] = $out;
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($json)); 
         
     }
     //===============AUF edit All reviews===============//
    
}
