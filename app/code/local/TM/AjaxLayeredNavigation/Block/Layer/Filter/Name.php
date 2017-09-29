<?php

class TM_AjaxLayeredNavigation_Block_Layer_Filter_Name
    extends Mage_Core_Block_Template
{
    protected $_filter;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tm/ajaxlayerednavigation/layer/name.phtml');
        $this->_filterModelName = 'ajaxlayerednavigation/name';
    }

    public function init()
    {
        $this->_initFilter();
        return $this;
    }

    protected function _prepareFilter()
    {
        return $this;
    }

    protected function _initFilter()
    {
        $this->_filter = Mage::getModel($this->_filterModelName)
            ->setLayer($this->getLayer());
        $this->_prepareFilter();

        $this->_filter->apply($this->getRequest(), $this);
        return $this;
    }

    public function getFilter()
    {
        return $this->_filter;
    }
}