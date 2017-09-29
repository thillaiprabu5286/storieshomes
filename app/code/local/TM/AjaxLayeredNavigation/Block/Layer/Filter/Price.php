<?php
class TM_AjaxLayeredNavigation_Block_Layer_Filter_Price extends TM_AjaxLayeredNavigation_Block_Layer_Filter_Abstract
{
    /**
     * Initialize Price filter module
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tm/ajaxlayerednavigation/layer/price.phtml');
        $this->_filterModelName = 'ajaxlayerednavigation/price';
    }

    /**
     * Prepare filter process
     *
     * @return Mage_Catalog_Block_Layer_Filter_Price
     */
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
    public function getCategoryId()
    {
        $layer = Mage::getSingleton('catalog/layer')->getCurrentCategory();

        return $layer->getId();
    }

    public function getMaxPrice()
    {
        return $this->_filter->getMaxPriceInt();
    }

    public function getMinPrice()
    {
        return $this->_filter->getMinPriceInt();
    }

    public function getMaxRange()
    {
        return $this->_filter->getMaxRangeInt();
    }

    public function getMinRange()
    {
        return $this->_filter->getMinRangeInt();
    }

    public function getParamUrl()
    {
        if ($this->isHome() || $this->isSeoHome()) {
            $urlPath = 'ajaxlayerednavigation/';
            $url = Mage::getBaseUrl() . $urlPath . Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix').'/f';
        } else {
            $url = Mage::helper('core/url')->getCurrentUrl();
        }
        if (!Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            $url = Mage::helper('core/url')->getCurrentUrl();
        }
        return $url;
    }

    public function isHome()
    {
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
        $homePage = false;

        if($page =='cms'){
            $cmsSingletonIdentifier = Mage::getSingleton('cms/page')->getIdentifier();
            $homeIdentifier = Mage::app()->getStore()->getConfig('web/default/cms_home_page');
            if($cmsSingletonIdentifier === $homeIdentifier){
                $homePage = true;
            }
        }

        return $homePage;
    }

    public function isSeoHome()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'ajaxlayerednavigation');
    }

    public function getActiveFilter()
    {
        $attribute = $this->getAttributeModel();

        $query = Mage::registry('query_request');
        if (!is_array($query)) {
            $query = array();
        }
        $code = $attribute->getAttributeCode();
        $plus = false;
        if (array_key_exists($code, $query)) {
            $plus = true;
        }

        return $plus;
    }

    public function getUrlVar()
    {
        return $this->_filter->getAttributeModel()->getAttributeCode();
    }
}
