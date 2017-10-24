<?php
class Mindstermob_Mobileconnect_Model_Products extends Mage_Core_Model_Abstract {
    public function _construct()
    {   
       $this->_init('mobileconnect/products');
    }

      public function getCategoriestree(){
          // Mage::app();



       $categoriesArray = Mage::getModel('catalog/category')
                   ->getCollection()
                   ->addAttributeToSelect('*')
                   ->addIsActiveFilter()
                   ->load()
                   ->toArray();
       //echo json_encode($categoriesArray);exit;

       //print_r($categoriesArray);exit;
       $cat_tree=array();
           $categories = array();
           $tree_key=0;
           foreach($categoriesArray as $categoryId => $category){
                         $arr["category_id"] = $arr["entity_id"];
                         $categoriesArray[$categoryId]["category_id"]=$categoriesArray[$categoryId]["entity_id"];
                         //unset($arr[$oldkey]);
                     }
                     // echo json_encode($categoriesArray);exit;
           foreach ($categoriesArray as $categoryId => $category) {

                if($category['level']==2){

                    $cat_tree[$tree_key]=$category;
                    $cat_tree[$tree_key]["children"]=array();
                    $inner_tree=0;


                    foreach($categoriesArray as $categoryId1 => $category1){

                        if($category1["parent_id"]==$category['category_id']){
                         $cat_tree[$tree_key]["children"][$inner_tree]=$categoriesArray[$categoryId1];
                         $cat_tree[$tree_key]["children"][$inner_tree]["children"]=array();
                               $inner_tree_2=0;
                               foreach($categoriesArray as $categoryId2 => $category2){
                                   if($category2["parent_id"]==$category1['category_id']){
                                       $cat_tree[$tree_key]["children"][$inner_tree]["children"][$inner_tree_2]=$categoriesArray[$categoryId2];

                                       $inner_tree_2++;
                                   }
                               }

                         $inner_tree++;
                        }
                    }
                   $tree_key++;

                }





           }
           return json_encode($cat_tree);
        //echo   json_encode($cat_tree);
       }

      public function getCategoriestree_soap(){

               $client = new SoapClient('http://'.$base_url->getSoapUrl().'/api/soap?wsdl');
               //$client = new SoapClient('http://10.10.10.149/stories_updated/api/soap?wsdl');
                //$client = new SoapClient('http://10.10.10.149/stories_updated/api/v2_soap?wsdl');
               //$client = new SoapClient('http://10.10.10.149/stories_magento/api/soap?wsdl');
                //$client = new SoapClient('http://10.10.10.149/stories_magento/api/soap?wsdl');
               // Can be added in Magento-Admin -> Web Services with role set to admin
               // log in to API
               try {
                   $sessionId = $client->login(getSoapUser(),getSoapPass());
                  // echo $sessionId ;

               } catch (Exception $e) {
                   print_r($e);
                   die();
               }

               // get all categories
                $allCats = $client->call($sessionId, 'catalog_category.tree');
               //echo($allCats["name"]);
               //print_r($allCats);

                foreach($allCats as $cat){
                   if(is_array($cat)){
                    foreach($cat as $cat1){
                       // print_r($cat1);
                        foreach($cat1 as $cat2){
                            if(is_array($cat2)){
                              //echo(json_encode($cat2));
                              $out_cat=json_encode($cat2);
                                 foreach($cat2 as $cat3){
                                     //print_r($cat3);
                                     //echo json_encode($cat3);
                                     //echo "----------------------END----------------";
                              }
                            }

                        }

                    }

                    }
                  //  echo "---------------";
                }
               //echo "al--demp"+json_encode($allCats);
               //echo $out_cat;

               return $out_cat;


       }//-----------------end of function getCategoriestree--------------

       //=================get product by category id==================================

     public  function getproducts($category_id){
           //$category_id=3;
         //================Auf all colors and materials==========//
          $color_options = array();
        $attribute = Mage::getSingleton('eav/config')
    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'color');

        if ($attribute->usesSource()) {
              $color_options = $attribute->getSource()->getAllOptions(false);

        }
        $materials_options = array();
        $attribute = Mage::getSingleton('eav/config')
    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'material');

        if ($attribute->usesSource()) {
              $materials_options = $attribute->getSource()->getAllOptions(false);

        }
        
        $filter_categories = array();
        $category_main = Mage::getModel('catalog/category')->load($category_id);
       $subcategories = $category_main->getChildrenCategories();
       if (count($subcategories) > 0){
           //echo $category->getName();
           foreach($subcategories as $subcategory){
               $filter_categories[]= array("value" => $subcategory->getId(),"label" => $subcategory->getName() );
               //$filter_categories[] = $subcategory->getName();
               //$filter_categories[] = $subcategory->getId();

           }
}
         //===================Auf all colors and materials==========//
         
           $base_url=Mage::getModel('mobileconnect/baseurl');
           $json = array('success' => true, 'products' => array());
           $category = Mage::getModel ('catalog/category')->load($category_id);
           $products = Mage::getResourceModel('catalog/product_collection')
                         // ->addAttributeToSelect('*')
                         ->AddAttributeToSelect('name')
                         ->addAttributeToSelect('price')
                         ->addFinalPrice()
                         ->addAttributeToSelect('small_image')
                         ->addAttributeToSelect('image')
                         ->addAttributeToSelect('thumbnail')
                         ->addAttributeToSelect('short_description')
                         ->addUrlRewrite()
                        ->addAttributeToSelect('color')
                         ->addAttributeToSelect('material')
                         ->AddCategoryFilter($category);
           Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
           $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
           $c = 0;
           $m = 0;
           $json["colors"] = array();
           $json["materials"] = array();
           $_colors = array();
           $_materials = array();
           foreach($products as $product){
               //===========Auf get colors and materials========//
               if($product->getColor() != ''){        
                    $key = array_search($product->getColor(), array_column($color_options, 'value'));
                    
                      //$json["colors"][$c] = $color_options[$key];
                    $_colors[$c] = $color_options[$key];
                      $c++;
                    
                   }

                    if($product->getMaterial() != ''){        
                    $key = array_search($product->getMaterial(), array_column($materials_options, 'value'));
                    
                      //$json["materials"][$m] = $materials_options[$key];
                    $_materials[$m] = $materials_options[$key];
                      $m++;
                    
                   }
                   
               //===========Auf get colors and materials========//    
               //---get stock details------------------//
           $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
           $stockItemData = $stockItem->getData();
           //$json["product"]["quantity"]=$stockItemData["qty"];
           //$json["product"]["id"]=$product_id;
           $quantity=$stockItemData["qty"];
       //print_r($stockItemData);

       //resize the image
       //$image =   Mage::helper('catalog/image')->init($product, 'small_image')
       //            ->constrainOnly(false)
       //            ->keepAspectRatio(true)
       //            ->keepFrame(true)
       //            ->keepTransparency(true)
       //            ->backgroundColor(array(255,255,255))
       //            ->resize(640, 640);


       //----end of get stock details-----------//
               $json['products'][] = array(
                       'id'                    => $product->getId(),
                       'name'                  => $product->getName(),
                       'description'           => $product->getShortDescription(),
                       'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                       'href'                  => $product->getProductUrl(),
                       'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                       'quantity'             => $quantity,
					   'currency_code'        => "Rs.",
                   );


           }
           $_materials = array_map("unserialize", array_unique(array_map("serialize", $_materials)));
           $_colors = array_map("unserialize", array_unique(array_map("serialize", $_colors)));
           $_materials_out = array();
           $_colors_out = array();
           foreach ($_materials as $key=>$value){
               $_materials_out[]= $value;
           }
           foreach ($_colors as $key=>$value){
               $_colors_out[]= $value;
           }
           $json["materials"] = $_materials_out;
           $json["colors"] = $_colors_out;
           
           $json["sub_categories"] = $filter_categories;
           return $json;
       }
       //-----------making ip image url-----------------//
       //(string)Mage::helper('catalog/image')->init($product, 'thumbnail')
       //=================end of product by category id=============================

       function getImageUrl($product){
          $temp=(string)Mage::helper('catalog/image')->init($product, 'thumbnail');
          $formatted=  str_replace("localhost", "10.10.10.149", $temp);
          return $formatted;

       }
     public function getSearchresults($searchstring){
         
         $json=array('success' => true,"products"=>array());
            $base_url=Mage::getModel('mobileconnect/baseurl');
            $product_collection = Mage::getResourceModel('catalog/product_collection')
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('name', array('like' => '%'.$searchstring.'%'))
                         ->load();

            foreach ($product_collection as $product) {
                //$ids[] = $product->getName();
                 //---get stock details------------------//
           $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
           $stockItemData = $stockItem->getData();
           //$json["product"]["quantity"]=$stockItemData["qty"];
           //$json["product"]["id"]=$product_id;
           $quantity=$stockItemData["qty"];
       //print_r($stockItemData);
       //Mage::helper('core')->currency($product->getPrice(), true, false)
       //----end of get stock details-----------//
                $json['products'][] = array(
                       'id'                    => $product->getId(),
                       'name'                  => $product->getName(),
                       'description'           => $product->getShortDescription(),
                       'price'                 => number_format($product->getPrice(),2), //." ".$currencyCode,
                       'href'                  => $product->getProductUrl(),
                       'thumb'                 =>$base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'thumbnail')),
                      'quantity'             => $quantity,
					  'currency_code'        => "Rs.",
                   );


            }
            return(json_encode($json));
         
         
     }//=======end of function===========//
 //=====================AUF edit====================//
     public function filterProducts($category_id,$subcategory_id,$material_sel,$color_sel,$price_upper){
         $json=array('success' => true,"products"=>array());
         $base_url = Mage::getModel('mobileconnect/baseurl');
         if($subcategory_id != ''){
             $category_id = $subcategory_id;
         }
 $category = Mage::getModel ('catalog/category')->load($category_id);
 $products_pr = Mage::getResourceModel('catalog/product_collection');
 
 //if($category_id != $material_sel)
                            $products =   $products_pr->addAttributeToSelect('*')
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
                              ->addAttributeToSelect('color')
                              ->addAttributeToSelect('material')
                              //->setPage(1, 4);
                              ->AddCategoryFilter($category);
                            $filter_arr = array();
                            if($color_sel != '' && $material_sel != ''){
                                $filter_arr = array(
                                   array('attribute'=> 'color','eq' => "$color_sel"),
                                   array('attribute'=> 'material','eq' => "$material_sel"),    
                                   );
                                 $products = $products_pr->addAttributeToFilter($filter_arr);
                                
                            }
                            if($color_sel != ''){
                                $filter_arr = array(
                                   array('attribute'=> 'color','eq' => "$color_sel"),
                                      
                                   );
                                 $products = $products_pr->addAttributeToFilter($filter_arr);
                            }
                            if($material_sel != ''){
                                $filter_arr = array(
                                   array('attribute'=> 'material','eq' => "$material_sel"),
                                      
                                   );
                                 $products = $products_pr->addAttributeToFilter($filter_arr);
                            }
                           
                            
                      
                         if($price_upper != ''){
                             $products = $products_pr->addFieldToFilter('price',array(array('from'=>'0','to'=>"$price_upper"))); 
                         }
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
                //$currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
               // $json[$cat_label]["category_id"] = $cat_id;
                foreach($products as $product){ 
                    $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));
                    //---get stock details------------------//
                    
                    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                    $stockItemData = $stockItem->getData();
                    //$json["product"]["quantity"]=$stockItemData["qty"];
                    //$json["product"]["id"]=$product_id;
                    $quantity=$stockItemData["qty"];



                //----end of get stock details-----------//
                        $json["products"][] = array(
                                'id'                    => $product->getId(),
                                'name'                  => $product->getName(),
                                'description'           => $product->getShortDescription(),
                                'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                                'href'                  => $product->getProductUrl(),
                                'currency_code'         => "Rs.",
                                'thumb'                 => $product_image,
                                //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                'quantity'             => $quantity,
                                'color'               => $product->getColor(),
                                'material'               => $product->getMaterial(),
                            );


                }
                
                return $json;
                
     }
     public function addReview($product_id,$customer_id,$review_data){
         
          // $customer_id = 15;
           // $product_id = 308;
         
            $row_source_review["title"] = $review_data->title;
            $row_source_review['Review'] = $review_data->review;
            $row_source_review['review_value1'] = $review_data->votes_1;
            $row_source_review['review_value2'] = $review_data->votes_2;
            $row_source_review['review_value3'] = $review_data->votes_3;
            
            $row_source_review['Status'] = "Pending";

            $_customer = Mage::getModel("customer/customer")->load($customer_id);
            $_session = Mage::getSingleton('customer/session')->setCustomer($_customer)->setCustomerAsLoggedIn($_customer);

            $_review = Mage::getModel('review/review')
           ->setEntityPkValue($product_id)
           //->setStatusId($sc_to_mage_review_status[$row_source_review['Status']])
           ->setStatusId(2)         
           ->setTitle($row_source_review['title'])
           ->setDetail($row_source_review['Review'])
           ->setEntityId(1)
           //->setStoreId($store)
           //->setStores(array($store))
           ->setStores(array(Mage::app()->getStore()->getId()))             
           ->setCustomerId($_customer->getId())
           ->setNickname($_customer->getFirstname())
           ->save();
           $_review->aggregate();
          $rating_options = array(
           1 => array(1,2,3,4,5), // <== Look at your database table `rating_option` for these vals
           2 => array(6,7,8,9,10),
           3 => array(11,12,13,14,15)
           );

           $rating_options_selected = array(
           1 => $row_source_review['review_value1'], // <== Look at your database table `rating_option` for these vals
           2 => $row_source_review['review_value2'],
           3 => $row_source_review['review_value3']
           );
           
           foreach($rating_options as $rating_id => $option_ids):
           try {
               $_rating = Mage::getModel('rating/rating')
                   ->setRatingId($rating_id)
                   ->setReviewId($_review->getId())
                   //->addOptionVote($option_ids[$rating_value-1],$product_id);
                  //->addOptionVote($option_ids[3],$product_id);  
                  ->addOptionVote($option_ids[$rating_options_selected[$rating_id]],$product_id);       
           } catch (Exception $e) {
               //die($e->getMessage());
               return $e->getMessage();
           }
           endforeach;
         return "saved";
     }
     public function getVotes($product_id){
         
            $star_point = 0;
            $reviews = Mage::getModel('review/review')
            ->getResourceCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId()) 
            ->addEntityFilter('product', $product_id)
            ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
            ->setDateOrder()
            ->addRateVotes();
            /**
            * Getting average of ratings/reviews
            */
            $avg = 0;
            $totalrv = 0;
            $totalrvper =0;
            $ratings = array();

            if (count($reviews) > 0) {
            foreach ($reviews->getItems() as $review) {
                foreach( $review->getRatingVotes() as $vote ) {

                $totalrv = $totalrv+$vote->getValue();
                $totalrvper = $totalrvper + $vote->getPercent(); 
                $ratings[] = $vote->getPercent();

                }
            }
            //echo $totalrv;exit;
            $totalrv = ($totalrv/3)/4;
            $totalrvper = ($totalrvper/3)/4;

            $avgrate = round($totalrv, 1);

            }

            $avgnew = array_sum($ratings)/count($ratings);
            $star_point  = (5/100)*$avgnew;  
            
            return $star_point;
     }
     
     public function getAllMostViewedProducts($product_id)
        {     
            // number of products to display
          $json = array('success' => true, 'message' =>'' );
         $base_url = Mage::getModel('mobileconnect/baseurl');
            $productCount = 10; 
            
            $total_count = 0;
            // store ID
            $storeId    = Mage::app()->getStore()->getId(); 

            // get today and last 30 days time
            $today = time();
            $last = $today - (60*60*24*30);

            $from = date("Y-m-d", $last);
            $to = date("Y-m-d", $today);

            // get most viewed products for last 30 days
            $products = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')        
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addViewsCount()
                //->addViewsCount($from, $to)
                ->setPageSize($productCount); 

            Mage::getSingleton('catalog/product_status')
                    ->addVisibleFilterToCollection($products);
            Mage::getSingleton('catalog/product_visibility')
                    ->addVisibleInCatalogFilterToCollection($products);
            
            foreach($products as $product){
                if($total_count > 20){
                    break;
                }
                $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));

                //==========get stock quantity==============//
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                       $stockItemData = $stockItem->getData();
                       //$json["product"]["quantity"]=$stockItemData["qty"];
                       //$json["product"]["id"]=$product_id;
                       $quantity = $stockItemData["qty"];
                //==========get stock quantity==============//
               if( $product->getId() != $product_id){        
                $json["products"][] = array(
                                   'id'                    => $product->getId(),
                                   'name'                  => $product->getName(),
                                   'description'           => $product->getShortDescription(),
                                   'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                                   'href'                  => $product->getProductUrl(),
                                    'thumb'                => $product_image,
                                    'currency_code'        => "Rs.",
                                   //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                   'quantity'             => $quantity
                               );
                $total_count++;
               }
            }
            return $json;
        }
     public function getTopMostViewedProducts($product_id)
        {     
            // number of products to display
         $base_url = Mage::getModel('mobileconnect/baseurl');
            $productCount = 10; 
            
            $total_count = 0;
            // store ID
            $storeId    = Mage::app()->getStore()->getId(); 

            // get today and last 30 days time
            $today = time();
            $last = $today - (60*60*24*30);

            $from = date("Y-m-d", $last);
            $to = date("Y-m-d", $today);

            // get most viewed products for last 30 days
            $products = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect('*')        
                ->setStoreId($storeId)
                ->addStoreFilter($storeId)
                ->addViewsCount()
                //->addViewsCount($from, $to)
                ->setPageSize($productCount); 

            Mage::getSingleton('catalog/product_status')
                    ->addVisibleFilterToCollection($products);
            Mage::getSingleton('catalog/product_visibility')
                    ->addVisibleInCatalogFilterToCollection($products);
            
            foreach($products as $product){
                if($total_count > 3){
                    break;
                }
                $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));

                //==========get stock quantity==============//
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                       $stockItemData = $stockItem->getData();
                       //$json["product"]["quantity"]=$stockItemData["qty"];
                       //$json["product"]["id"]=$product_id;
                       $quantity = $stockItemData["qty"];
                //==========get stock quantity==============//
               if( $product->getId() != $product_id){        
                $json[] = array(
                                   'id'                    => $product->getId(),
                                   'name'                  => $product->getName(),
                                   'description'           => $product->getShortDescription(),
                                   'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                                   'href'                  => $product->getProductUrl(),
                                    'image'                => $product_image,
                                    'currency_code'        => "Rs.",
                                   //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                   'quantity'             => $quantity
                               );
                $total_count++;
               }
            }
            return $json;
        }
 public function getAllnewarrivals(){
     $json = array('success' => true, 'message' =>'' );
     $total_count = 0;
    // $json = array();
        $base_url = Mage::getModel('mobileconnect/baseurl');
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
                if($total_count > 10){
                    break;
                }
                $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));

                //==========get stock quantity==============//
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                       $stockItemData = $stockItem->getData();
                       //$json["product"]["quantity"]=$stockItemData["qty"];
                       //$json["product"]["id"]=$product_id;
                       $quantity = $stockItemData["qty"];
                //==========get stock quantity==============//
               if( $product->getId() != $product_id){        
                $json ["products"][] = array(
                                   'id'                    => $product->getId(),
                                   'name'                  => $product->getName(),
                                   'description'           => $product->getShortDescription(),
                                   'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                                   'href'                  => $product->getProductUrl(),
                                    'thumb'                => $product_image,
                                    'currency_code'        => "Rs.",
                                   //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                   'quantity'             => $quantity
                               );
                $total_count++;
               }
            }

        return $json;
 }       
 public function getTopnewarrival($product_id){
     $total_count = 0;
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
                if($total_count > 1){
                    break;
                }
                $product_image = $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200));

                //==========get stock quantity==============//
                $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                       $stockItemData = $stockItem->getData();
                       //$json["product"]["quantity"]=$stockItemData["qty"];
                       //$json["product"]["id"]=$product_id;
                       $quantity = $stockItemData["qty"];
                //==========get stock quantity==============//
               if( $product->getId() != $product_id){        
                $json[] = array(
                                   'id'                    => $product->getId(),
                                   'name'                  => $product->getName(),
                                   'description'           => $product->getShortDescription(),
                                   'price'                 => number_format($product->getPrice(),2),  //." ".$currencyCode,
                                   'href'                  => $product->getProductUrl(),
                                    'image'                => $product_image,
                                    'currency_code'        => "Rs.",
                                   //'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                                   'quantity'             => $quantity
                               );
                $total_count++;
               }
            }

        return $json;
 }
 public function getAllreviews($productId){
      $json = array('success' => false, 'message' =>'' ); 
        $all_reviews = array();  
        $final_reviews = array();
        $final_reviews_arr = array();
       //productId = $product->getId();
       //$productId = 308;
    try{
         $reviews = Mage::getModel('review/review')
                         ->getResourceCollection()
                         ->addStoreFilter(Mage::app()->getStore()->getId())
                         ->addEntityFilter('product', $productId)
                         ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                         ->setDateOrder()
                         ->addRateVotes();
   }catch(Exception $ex){
       $json["success"] = false;
       $json["message"] = $ex->getMessage();
       $json["reviews"] = null;
       return $json;
   }
       //echo count($reviews);exit;
       $i = 0;
       $total_reviews_count = count($reviews);
       if($total_reviews_count >0){
       foreach( $reviews as $_review ){
           $all_reviews[$i]["title"] = $_review->getData('title');
           $all_reviews[$i]["detail"] = $_review->getData('detail');
           $all_reviews[$i]["nickname"] = $_review->getData('nickname');
           $created_date = date('jS F Y',strtotime($_review->getData('created_at')));
           $all_reviews[$i]["created_at"] = $created_date;
            $ratings = array();
           foreach( $_review->getRatingVotes() as $vote ) {
                   $ratings[] = $vote->getPercent();
               }
           $ratings_avg = array_sum($ratings)/count($ratings); 
           if(!$ratings_avg){
               $ratings_avg = 0;
           }
           $all_reviews[$i]["ratings_avg"] = $ratings_avg;
           //$final_reviews[$ratings_avg] = $all_reviews[$i];
           //$final_reviews_arr[] = $final_reviews;
           $i++;
          } 
   
    }
    
    

    //krsort($final_reviews);
    //print_r($final_reviews);exit;
    //$final_reviews_arr =  array_slice($final_reviews, 0, 1);
    if(count($all_reviews) > 0){        
        $json["success"] = true;
        $json["message"] = "success";
       $json["reviews"] = $all_reviews;       
        return $json;
    }else{
        $json["success"] = true;
        $json["message"] = "No reviews";
        $json["reviews"] = null;
        return $json;
    }
 }
 public function getTopreview($productId){
     
        $all_reviews = array();  
        $final_reviews = array();
        $final_reviews_arr = array();
       //productId = $product->getId();
       //$productId = 308;

       $reviews = Mage::getModel('review/review')
                       ->getResourceCollection()
                       ->addStoreFilter(Mage::app()->getStore()->getId())
                       ->addEntityFilter('product', $productId)
                       ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                       ->setDateOrder()
                       ->addRateVotes();
       //echo count($reviews);exit;
       $i = 0;
       $total_reviews_count = count($reviews);
       if($total_reviews_count >0){
       foreach( $reviews as $_review ){
           $all_reviews[$i]["title"] = $_review->getData('title');
           $all_reviews[$i]["detail"] = $_review->getData('detail');
           $all_reviews[$i]["nickname"] = $_review->getData('nickname');
           $created_date = date('jS F Y',strtotime($_review->getData('created_at')));
           $all_reviews[$i]["created_at"] = $created_date;
            $ratings = array();
           foreach( $_review->getRatingVotes() as $vote ) {
                   $ratings[] = $vote->getPercent();
               }
           $ratings_avg = array_sum($ratings)/count($ratings); 

           $all_reviews[$i]["ratings_avg"] = $ratings_avg;
           $final_reviews[$ratings_avg] = $all_reviews[$i];
            $i++;
          } 
   
    }

    krsort($final_reviews);
    //print_r($final_reviews);exit;
    $final_reviews_arr =  array_slice($final_reviews, 0, 1);
    if(count($final_reviews_arr) > 0){
        return $final_reviews_arr[0];
    }else{
        return null;
    }
 }    
 public function getProductdetails($product_id){
      $base_url=Mage::getModel('mobileconnect/baseurl');
     $json = array('success' => true, 'product' => array());
        $product = Mage::getModel('catalog/product')->load($product_id);
        $details=$product->getData();

        //=====get gallery images================
        $gallery = $product->getMediaGalleryImages();
        $paths = array();
        //$obj = new Mage_Catalog_Block_Product_View_Media();
        $helper = Mage::Helper('catalog/image');
        $img_id=0;
        foreach ($gallery as $image) {
            //$paths[] = getBaseurl()."media/catalog/product".$image->getFile();
            $paths[$img_id]["small"] = (string)Mage::helper('catalog/image')->init($product,'small_image',$image->getFile())->keepAspectRatio(true)->resize(75,75);
             $paths[$img_id]["large"]=(string)Mage::helper('catalog/image')->init($product,'small_image',$image->getFile())->keepAspectRatio(true)->resize(1000,1000);
             $paths["thumbnails"][]=  array("ids"=>$img_id,"src"=>(string)Mage::helper('catalog/image')->init($product,'small_image',$image->getFile())->keepAspectRatio(true)->resize(75,75));
              $json["product"]["gallery_full"][]= array("ids"=>$img_id,"src"=>(string)Mage::helper('catalog/image')->init($product,'small_image',$image->getFile())->keepAspectRatio(true)->resize(640,640));

        //(string)Mage::helper('catalog/image')->init($product, 'small_image',$image->getFile())->keepAspectRatio(true)->resize(200, 200)

             $img_id++;
        }
        //sort($paths);
        $json["product"]["gallery"]=$paths;
        $json["product"]["attributes"]=array();
        //========end of grt gallery images========//
        //---get stock details------------------//
        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product_id);
        $stockItemData = $stockItem->getData();
        $json["product"]["quantity"]=(float)$stockItemData["qty"];
        $json["product"]["id"]=$product_id;
        //print_r($stockItemData);

        //----end of get stock details-----------//


        //material,dimensions,color,sku
        $json["product"]["name"]=$details["name"];
        $json["product"]["price"]=$details["price"]+0;
        $json["product"]["description"]=$details["description"];
        $json["product"]["short_description"]=$details["short_description"];
        $json["product"]["is_in_stock"]=$details["is_in_stock"];
        $json["product"]["image"]=$base_url->getBaseurl()."media/catalog/product".$details["image"];
        $json["product"]["thumbnail"]=$details["thumbnail"];
		$json["product"]["currency_code"]= "Rs.";
         //=========attribute details======================//
         $colorValue = Mage::getModel('catalog/product')
			->load($product_id)
			->getAttributeText('color');
         if(!$colorValue){
             $colorValue = '';
         }
         $materialValue = Mage::getModel('catalog/product')
			->load($product_id)
			->getAttributeText('material');
         if(!$materialValue){
             $materialValue = '';
         }
         
         //=========attribute details=====================//
        $json["product"]["attributes"]=array("material"=>$materialValue,"dimensions"=>$details["dimensions"],"color"=>$colorValue,"sku"=>$details["sku"]);
        $json["product"]["custom_options"]=array();
        $custom_option=array();
        if($product->hasOptions){
            $optionsArr = $product->getOptions();
            foreach($optionsArr as  $optionKey => $optionVal)
            {
                
                   $options=array();
                                
                          foreach($optionVal->getValues() as $valuesKey => $valuesVal)
                            {
                                  
                                $options[]=array("key"=>$valuesVal->getId(), "val"=>$valuesVal->getTitle());


                            }
                       $custom_option["titles"][]=array("title"=>$optionVal->getTitle(),"title_id"=>$optionVal->getId(),"options"=>$options);     

                            //$optStr.= "</select>";
            }//----end of options foreach---

        }//=============end of options=======//
        $json["product"]["custom_options"] = $custom_option;
        $json["product"]["topreview"] = $this->getTopreview($product_id);
        $json["product"]["top_new_arrivals"] = $this->getTopnewarrival($product_id);
        $json["product"]["top_most_viewed"] = $this->getTopMostViewedProducts($product_id);
        $json["product"]["votes"] = $this->getVotes($product_id);
        return(json_encode($json));

  }//=======end of function=============//      
 
 public function getGalleryimages($product_id){
     $base_url=Mage::getModel('mobileconnect/baseurl');
     $product = Mage::getModel('catalog/product')->load($product_id);
    $details=$product->getData();
    $json["photos"]=array();
    //=====get gallery images================
    $gallery = $product->getMediaGalleryImages();
    $paths = array();
    foreach ($gallery as $image) {
        $paths[] =array('src'=>$base_url->getBaseurl()."media/catalog/product".$image->getFile());
    }
    //sort($paths);
    $json["photos"]=$paths;
    return(json_encode($json));
     
 }//====end of function==//
 
 public function getQuickview($product_id){
     
     $model = Mage::getModel('catalog/product'); //getting product model
    
$_product = $model->load($product_id); //getting product object for particular product id
     $_url = Mage::helper('checkout/cart')->getAddUrl($_product);
     $formKey = Mage::getSingleton('core/session')->getFormKey();
     
     $json["data"]=array();
    //===get images ========// 
     $imgs1 =  $this->getGalleryimages($product_id);
     $obj = json_decode($imgs1);
     $imgs = "";
     $imgs_path = "";
      foreach($obj->photos as $img){
        $imgs = $imgs.'<div class="item"><img src="'.$img->src.'" alt="bed"/></div>';
       
      }
      //==============get images===========//
      //============get stock details==========//
       if($_product->isSaleable()){
            if($_product->stock_item->is_in_stock == 1){
                    $stock_status = "In Stock";
            }else{
                    $stock_status = "Out of Stock";
            }
        }
      //===========get stock details===========//
      //============get qty details =============//
      
      //=============get qty details ============//
//    foreach($obj->photos as $img){
//       
//       echo "igs--".$img->src;
//   }
// <h1><a href="'. $_product->getProductUrl().'">'.  $_product->getName().'</a></h1>        
      $json["imgs"] = $imgs;
      $formattedPrice = Mage::helper('core')->currency($_product->getPrice(), true, false);
     $json["data"] = '<div class="PopupBg">
         <form action="'.$_url.'" method="post" id="product_addtocart_form">
             <input name="form_key" type="hidden" value="'.$formKey.'">
            <div class="PopupClose"><img src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/rwd/default/img/close.png"/></div><!--close-->
            
           
            <div class="PopUp_Discription_Div_Full">
                <div class="PopUp_Item_left">
                    <div id="sync1" class="owl-carousel">
                          '.$imgs.'
                    </div>
                    <div id="sync2" class="owl-carousel">
                           '.$imgs.'
                    </div>
                </div><!--PopUp_Item_left-->
                <div class="PopUp_Item_Right">
                    <div class="PopupItemName">'.  $_product->getName().'</div><!--PopupItemName-->
                    <div class="PopupStockDetails">'.$stock_status.'</div><!--PopupStockDetails-->
                    <div class="PopupItemDescription">'. $_product->getShortDescription().'</div><!--PopupItemDescription-->
                    <div class="PopupItemPrice">'.$formattedPrice.'</div><!--PopupItemPrice-->
                    <div class="PopupInputDiv">
                        <div class="PopupInputLabel">Qty.</div><!--PopupInputLabel-->
                        <input type="text" name="qty" id="qty" maxlength="5" value="1" class="PopupInputBox"/>
                        <input type="button" value="Add to Cart" onclick="productAddToCartForm.submit(this)" class="PopupInputSbmt">
                    </div><!--PopupInputDiv-->
                </div><!--PopUp_Item_Right-->
            </div><!--PopUp_Discription_Div_Full-->
        </div><!--PopupBg-->
        </form>
';
     
     return(json_encode($json));
 }
 
 

}//=======end of class===============//
?>