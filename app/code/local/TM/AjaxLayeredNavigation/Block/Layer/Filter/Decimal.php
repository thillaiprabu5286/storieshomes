<?php

class TM_AjaxLayeredNavigation_Block_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Decimal//TM_AjaxLayeredNavigation_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Decimal AjaxLayeredNavigation Filter Model
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplate('tm/ajaxlayerednavigation/layer/decimal.phtml');
        $this->_filterModelName = 'ajaxlayerednavigation/decimal';
    }

    protected function _prepareFilter()
    {

        $this->_filter->setAttributeModel($this->getAttributeModel());
        
        return $this;
    }

    public function getMaxValue()
    {
        return (int)$this->_filter->getMaxValue();
    }

    public function getMinValue()
    {
        return (int)$this->_filter->getMinValue();
    }

    public function getUrlVar()
    {
        return $this->_filter->getAttributeModel()->getAttributeCode();
    }

    // public function getActiveFilter()
    // {
    //     $attribute = $this->getAttributeModel();
        
    //     $query = Mage::registry('query_request');
    //     if (!is_array($query)) {
    //         $query = array();
    //     }
    //     $code = $attribute->getAttributeCode();
    //     $plus = false;
    //     if (array_key_exists($code, $query)) {
    //         $plus = true;
    //     }
        
    //     return $plus;
    // }
}