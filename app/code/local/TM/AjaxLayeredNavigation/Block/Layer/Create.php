<?php

class TM_AjaxLayeredNavigation_Block_Layer_Create extends Mage_Core_Block_Template
{
    public function getMaxPrice()
    {
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMaxPriceInt();
    }

    public function getMinPrice()
    {
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMinPriceInt();;
    }

    public function getMaxRange()
    {
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMaxRangeInt();
    }

    public function getMinRange()
    {
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMinRangeInt();
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

    public function isCatalogSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
            && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'result');
    }

    public function isAdvancedSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
            && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'advanced');
    }

    public function isTmAttributePage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'attributepages'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'page');
    }

    public function isTmDealsPage()
    {
        return Mage::app()->getFrontController()->getRequest()->getRouteName() == 'tmdailydeals';
    }

    public function getSeoCategoryUrl()
    {
        if ($this->isCatalogSearchPage()) {
            $url = 'catalogsearch/result';
        } elseif ($this->isSeoHome() || $this->isHome()) {
            $url = 'ajaxlayerednavigation/';
        } elseif ($this->isAdvancedSearchPage()){
            $url = 'catalogsearch/advanced';
        } else {
            $category = Mage::registry('current_category');
            $categoryPath = $category->getUrlPath();
            $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $url = str_replace($mageSuffix, '', $categoryPath);
        }

        return $url;
    }
}
