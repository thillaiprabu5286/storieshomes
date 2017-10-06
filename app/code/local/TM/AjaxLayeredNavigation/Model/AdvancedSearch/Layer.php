<?php

class TM_AjaxLayeredNavigation_Model_AdvancedSearch_Layer extends TM_AjaxLayeredNavigation_Model_Layer
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * Get current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        $collection = Mage::getSingleton('catalogsearch/advanced')->getProductCollection();
        //echo $collection->load()->getSelect();
//         $this->_prepareAttributeCollection($collection);
//         $this->prepareProductCollection($collection);
//         $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
//         $collection = Mage::getSingleton('catalogsearch/advanced')->getProductCollection();
        
//        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
//            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
//        } else {
//            $collection = Mage::getResourceModel('catalogsearch/advanced_collection');
//            $this->prepareProductCollection($collection);
//            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
//        }
        
        return $collection;
    }
    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Mage_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
    
    public function getMaxCatalogSearchPrice()
    {
        return Mage::getModel('ajaxlayerednavigation/price')->getMinPriceInt();
    }
}
