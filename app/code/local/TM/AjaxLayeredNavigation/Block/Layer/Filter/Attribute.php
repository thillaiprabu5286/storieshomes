<?php

class TM_AjaxLayeredNavigation_Block_Layer_Filter_Attribute extends TM_AjaxLayeredNavigation_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tm/ajaxlayerednavigation/layer/filter.phtml');
        $this->_filterModelName = 'ajaxlayerednavigation/attribute';
    }

    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }

    public function getActiveFilter()
    {
        $attribute = $this->getAttributeModel();

        $query = Mage::registry('query_request');
        if (!$query) {
            $query = Mage::app()->getRequest()->getParams();
        }
        if (!is_array($query)) {
            $query = array();
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            $query = Mage::app()->getRequest()->getParams();
        }
        $code = $attribute->getAttributeCode();
        $plus = false;
        if (array_key_exists($code, $query)) {
            $plus = true;
        }

        return $plus;
    }
}
