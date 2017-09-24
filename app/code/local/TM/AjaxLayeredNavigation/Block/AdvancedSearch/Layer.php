<?php

class TM_AjaxLayeredNavigation_Block_AdvancedSearch_Layer extends TM_AjaxLayeredNavigation_Block_Layer_View
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer());
    }
    
    /**
     * Get attribute filter block name
     *
     * @deprecated after 1.4.1.0
     *
     * @return string
     */
    protected function _getAttributeFilterBlockName()
    {
        return 'ajaxlayerednavigation/advancedSearch_layer_filter_attribute';
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        $this->_attributeFilterBlockName = 'ajaxlayerednavigation/advancedSearch_layer_filter_attribute';
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        return Mage::getSingleton('ajaxlayerednavigation/advancedSearch_layer');
    }

}
