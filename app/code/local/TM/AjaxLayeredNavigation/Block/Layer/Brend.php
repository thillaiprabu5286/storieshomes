<?php

class TM_AjaxLayeredNavigation_Block_Layer_Brend extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getLayer()
    {
        return Mage::getSingleton('catalog/layer');
    }

    public function getCategoryActiveFilterIds()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        $optionIds = array();

        foreach($filters as $filter) {
            $optionIds[] = $filter->getValue();
        }
        return $optionIds;
    }

    public function getCategoryTitleData()
    {
        $options = $this->getCategoryActiveFilterIds();
        $model   = Mage::getModel('ajaxlayerednavigation/options');
        return $model->getCategoryData($options);
    }
}
