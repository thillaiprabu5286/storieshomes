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
                         ->AddCategoryFilter($category);
           Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
           $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
           foreach($products as $product){ 
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
                       'price'                 => $product->getPrice()+0,  //." ".$currencyCode,
                       'href'                  => $product->getProductUrl(),
                       'thumb'                 => $base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'small_image')->keepAspectRatio(true)->resize(200, 200)),//$image,
                       'quantity'             => $quantity
                   );


           }
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
                       'price'                 => $product->getPrice()+0, //." ".$currencyCode,
                       'href'                  => $product->getProductUrl(),
                       'thumb'                 =>$base_url->getBaseurl_products((string)Mage::helper('catalog/image')->init($product, 'thumbnail')),
                      'quantity'             => $quantity
                   );


            }
            return(json_encode($json));
         
         
     }//=======end of function===========//
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
        $json["product"]["custom_options"]=$custom_option;
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
//========product quick view in home page============//
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