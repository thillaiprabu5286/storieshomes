<?php
class Mindstermob_Mobileconnect_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
       // echo "Hello tuts+ World";
        
        
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
  //  return json_encode($cat_tree);
        
        //$response=array("name"=>"nadeem");
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
        $this->getResponse()->setBody(json_encode($cat_tree));
        
        

    }
}