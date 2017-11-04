<?php
class Mango_Attributeswatches_Model_Observer {
    /**
     * Apply after style section is saved....
     */
    public function makeAttributeVisible(Varien_Event_Observer $observer) {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'attributeswatches') {
            //SELECT * FROM eav_attribute WHERE attribute_code = 'hover_image'
            $_resource = Mage::getSingleton('core/resource');
            $_catalog_eav_attribute_table = $_resource->getTableName("catalog/eav_attribute");
            $_eav_attribute_table = $_resource->getTableName("eav/attribute");
            //Mage::log(Mage::app()->getRequest());
            
            $_values = Mage::app()->getRequest()->getParam('groups');
            
            $_field = false;
            if(isset($_values['productlist']['fields']['alternate_image_source']['value'])){
                $_field = trim( $_values['productlist']['fields']['alternate_image_source']['value'] );
            }
            if ($_field) {
                $query = "UPDATE ". $_eav_attribute_table ." ea 
                INNER JOIN ". $_catalog_eav_attribute_table ." cea 
                ON ea.attribute_id = cea.attribute_id 
                SET used_in_product_listing = 1 
                WHERE attribute_code =  :attribute_code;";
                $connection = $_resource->getConnection('core_write');
                $connection->beginTransaction();
                $connection->query($query, array("attribute_code"=>$_field));
                $connection->commit();
            }
        }
    }
    
    public function refreshSwatches($observer){
        /* to make sure new swatches are created... */
        Mage::getModel("attributeswatches/attributeswatches")->refresh();
        return;
    }
    
    public function swatchesForLayeredNavigationFilter(Varien_Event_Observer $observer){
        
        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Layer_View){
            $_attributes_with_swatches = Mage::getStoreConfig("attributeswatches/layerednavigation/attributes") . "," . Mage::getStoreConfig("attributeswatches/layerednavigation/hidelabel");
            $_attributes_with_swatches = array_unique(explode(",", $_attributes_with_swatches));
            
            $_attributes_with_label_only = Mage::getStoreConfig("attributeswatches/layerednavigation/label") ;
            $_attributes_with_label_only = array_unique(explode(",", $_attributes_with_label_only));
            
            /* attributes to display as swatches */
            foreach($_attributes_with_swatches as $_attribute){
                if($block->getChild( $_attribute . '_filter'))
                $block->getChild( $_attribute .  '_filter')->setTemplate("attributeswatches/catalog_layer_filter_swatches.phtml");
            }
            
            /* attributes to display as labels/buttons */
            foreach($_attributes_with_label_only as $_attribute){
                if($block->getChild( $_attribute . '_filter'))
                $block->getChild( $_attribute .  '_filter')->setTemplate("attributeswatches/catalog_layer_filter_label.phtml");
            }
            
        }elseif ($block instanceof Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Image){
            $block->setTemplate("attributeswatches/wishlist_item_column_image.phtml");
                
        }    
        return;
    }
    
    public function wishlistConfigurableProductImage(Varien_Event_Observer $observer){
        $block = $observer->getBlock();
        if ($block instanceof Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Image){
            $_item = $block->getItem();
            $_product = $_item->getProduct();
            if($_product->getTypeId()=="configurable")
            $block->setTemplate("attributeswatches/wishlist_item_column_image.phtml");
        }    
        return;
    }
    
    
    public function addExtraAttributes(Varien_Event_Observer $observer){
        
        
        $collection = $observer->getCollection();
        /* include attributes only for the product details page - configurable products.. */
        if($collection && $collection instanceof Mage_Catalog_Model_Resource_Product_Type_Configurable_Product_Collection){
            Mage::log(Mage::getStoreConfig("attributeswatches/settings/reload_attributes"));
            $_attributes_to_reload = trim(Mage::getStoreConfig("attributeswatches/settings/reload_attributes"));
            $_attributes_to_reload =  ($_attributes_to_reload )?  explode(",", $_attributes_to_reload): false;
            Mage::log($_attributes_to_reload);
            if($_attributes_to_reload){
                $collection->addAttributeToSelect($_attributes_to_reload);
            }
        }
        
        return;
        
        
    }
    
    
}
