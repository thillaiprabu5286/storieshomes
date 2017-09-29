<?php

class Mindstermob_Mobileconnect_HomepageController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        //echo "hii";exit;
        $json_out = array();
        $model = Mage::getModel('mobileconnect/homepage');
        $banner_images = $model->getSlider();
        $new_arrivals = $model->getNewarrivals();
        $top_categories = $model->getTopcategories();
        $mobileoffers = $model->getOffers();
        //$filter_materials = $model->getFiltermaterials();
        //$filter_colors = $model->getFiltermaterials();
        $json_out["status"] = 'success';
        $json_out["banner_images"] = $banner_images;
        $json_out["new_arrivals"] = $new_arrivals;
        $json_out["top_categories"] = $top_categories;
        $json_out["offers"] = $mobileoffers;
        //$json_out["filter"]["materials"] = $filter_materials;
        //$json_out["filter"]["colors"] = $filter_colors;
        
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($json_out));
    }
    
    public function favouriteAction(){
        $params = json_decode(file_get_contents('php://input'));

        $data = $params->data;
        $logindata = $params->logindata;
        $email = $logindata->email;
        $password = $logindata->password;
        $model = Mage::getModel('mobileconnect/homepage');
        $out = $model->getFavourites($data, $email, $password);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($out);
        
    }
    
   
    
}
?>