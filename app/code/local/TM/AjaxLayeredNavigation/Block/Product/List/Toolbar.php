<?php

class TM_AjaxLayeredNavigation_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    /**
     * Set collection to pager
     *
     * @param Varien_Data_Collection $collection
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);

        $primaryFieldName = $this->_collection->getResource()->getIdFieldName();
        $this->_collection->setOrder($primaryFieldName, 'asc');
        return $this;
    }
}
