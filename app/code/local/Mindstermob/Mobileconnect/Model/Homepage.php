<?php
class Mindstermob_Mobileconnect_Model_Homepage extends Mage_Core_Model_Abstract {
    public function _construct()
    {   
       $this->_init('mobileconnect/homepage');
    }
    
    
    public function getSlider(){
        
        $banner_config = Mage::getStoreConfig('youamaslider');
        $banner_media_url = Mage::getBaseUrl('media') . 'youama/slider/';
        ////print_r($banner_config["slideritem1"]["image"]);exit;
        ////echo "saved ";exit;
        $sliderItems = array();
        
        $item = 0;
        for ($i = 0; $i < 6; $i++)
        {
            if (isset($banner_config["slideritem$i"]['image']))
            {
                if ($banner_config["slideritem$i"]['image'] != null && $banner_config["slideritem$i"]['image'] != '')
                {
                    
                    $sliderItems[$item]['image'] = $banner_media_url . $banner_config["slideritem$i"]["image"];
                    $sliderItems[$item]['title'] = $banner_config["slideritem$i"]["title"];
                    $sliderItems[$item]['link'] = $banner_config["slideritem$i"]["link"];
                    $item++;
                }
            }
        }
        
        return $sliderItems;
    }
    
    public function getOffers(){
        $offerbanners = array();
        $banner_config = Mage::getStoreConfig('youamaslider');
        $banner_media_url = Mage::getBaseUrl('media') . 'youama/slider/';
        //print_r($banner_config["mobileoffer"]);exit;
        $offerbanners["image"] = $banner_media_url = Mage::getBaseUrl('media') . 'youama/slider/'.$banner_config["mobileoffer"]["image"];
        $offerbanners["link"] = $banner_config["mobileoffer"]["link"];
        $offerbanners["link"] = $banner_config["mobileoffer"]["title"];
        return $offerbanners;
       
    }
    //============New Arrival products=================//
    
    public function getNewarrivals(){
        $json = array();
        $base_url=Mage::getModel('mobileconnect/baseurl');
            $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
            $collection = Mage::getResourceModel('catalog/product_collection')
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')
            ->getVisibleInCatalogIds());    
            $products = $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')
            ->getProductAttributes())
            ->addUrlRewrite()
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
            0 => array('date' => true, 'to' => $todayEndOfDayDate),
            1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
            0 => array('date' => true, 'from' => $todayStartOfDayDate),
            1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
            array(
            array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
            array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
            )
            )
            ->addAttributeToSort('news_from_date', 'desc');
            foreach($products as $product){ 
                $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));

                //==========get stock quantity==============//
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                       $stockItemData = $stockItem->getData();
                       //$json["product"]["quantity"]=$stockItemData["qty"];
                       //$json["product"]["id"]=$product_id;
                       $quantity = $stockItemData["qty"];
                //==========get stock quantity==============//
                $json[] = array(
                                   'id'                    => $product->getId(),
                                   'name'                  => $product->getName(),
                                   'description'           => $product->getShortDescription(),
                                   'price'                 => $product->getPrice()+0,  //." ".$currencyCode,
                                   'href'                  => $product->getProductUrl(),
                                    'image'                => $product_image,
                                    'currency_code'        => "Rs.",
                                   //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                   'quantity'             => $quantity
                               );

            }

        return $json;
    }
    
    public function getTopcategories(){
        
        $top_categories =array("furniture" => 3,"furnishing" => 5, "lights" => 86,"carpets" => 76,"homeaccessories" => 13);

$base_url = Mage::getModel('mobileconnect/baseurl');
           
           foreach ($top_categories as $cat_label => $cat_id) {
    
   
                $category = Mage::getModel ('catalog/category')->load($cat_id);
                $products = Mage::getResourceModel('catalog/product_collection')
                              // ->addAttributeToSelect('*')
                              ->AddAttributeToSelect('name')
                              ->addAttributeToSelect('price')
                              ->addFinalPrice()
                              ->addAttributeToSelect('small_image')
                              ->addAttributeToSelect('image')
                              ->addAttributeToSelect('thumbnail')
                              ->addAttributeToSelect('short_description')
                              ->addAttributeToFilter('status', 1) // enabled
                              ->setOrder('created_at', 'desc')
                              ->addUrlRewrite()
                              ->setPage(1, 4)
                              ->AddCategoryFilter($category);
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
                //$currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                $json[$cat_label]["category_id"] = $cat_id;
                foreach($products as $product){ 
                    $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));
                    //---get stock details------------------//
                    
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                    $stockItemData = $stockItem->getData();
                    //$json["product"]["quantity"]=$stockItemData["qty"];
                    //$json["product"]["id"]=$product_id;
                    $quantity=$stockItemData["qty"];



                //----end of get stock details-----------//
                        $json[$cat_label]["products"][] = array(
                                'id'                    => $product->getId(),
                                'name'                  => $product->getName(),
                                'description'           => $product->getShortDescription(),
                                'price'                 => $product->getPrice()+0,  //." ".$currencyCode,
                                'href'                  => $product->getProductUrl(),
                                'currency_code'         => "Rs.",
                                'image'                 => $product_image,
                                //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                'quantity'             => $quantity
                            );


                }
     }//===========top-category-loop===========//
     
     return $json;
     
    }
    
    //=================Favourites=================//
    
    public function getFavourites($data,$email, $password){
      
        $base_url=Mage::getModel('mobileconnect/baseurl');
         $json = array('status'=>'0','login'=>'0','message'=>'','items' => array());
       
           
           foreach($data as $product){
               if($product->id != ''){
                   $product_id = $product->id;
                   //$json = array('success' => true, 'product' => array());
                   $product = Mage::getModel('catalog/product')->load($product_id);
                   $details = $product->getData();
                   //---get stock details------------------//
                   $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
                   $stockItemData = $stockItem->getData();
                  // if($stockItemData["qty"] > $cart->qty){
                       $json["status"]="1";
                       $instock=1;
                       //$details["is_in_stock"]
                      
                       $json["items"][]=array( "stock_quantity"=>$stockItemData["qty"],
                           
                           "id"=>$product_id,
                            "name"=>$details["name"],
                            "price"=>$details["price"]+0,   
                           // "total_price"=>$details["price"]* $cart->qty,
                            "description"=>$details["description"],
                            "short_description"=>$details["short_description"],
                            "is_in_stock"=>$instock,
                           /* "image"=> getBaseurl()."media/catalog/product".$details["image"],*/
                           "image"=> $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),
                            "thumbnail"=>$details["thumbnail"]
                          // "options"=>$cart->options
                        );
                         
                       //$json["cart"][]=array("id"=>$cart->id,"qty"=>$cart->qty,"options"=>$cart->options);

               }else{
                  $json = array('success' => false, 'product' => array());
                  // echo json_encode($json);

               }
                
           }
        return json_encode($json);
    }//==============favourites===============//
    
    //====================filter functions=====================//
    public function getFiltermaterials(){
        $options = array();
        $attribute = Mage::getSingleton('eav/config')
    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'material');

        if ($attribute->usesSource()) {
              $options = $attribute->getSource()->getAllOptions(false);

        }
        return $options;
    }
    
    public function getFiltercolors(){
        $options = array();
        $attribute = Mage::getSingleton('eav/config')
    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'color');

        if ($attribute->usesSource()) {
              $options = $attribute->getSource()->getAllOptions(false);

        }
        return $options;
    }
    //====================filter functions======================//
    
}
 ?>