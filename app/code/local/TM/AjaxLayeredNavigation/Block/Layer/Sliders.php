<?php

class TM_AjaxLayeredNavigation_Block_Layer_Sliders extends Mage_Core_Block_Template
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
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch');
    }
    
    public function getSeoCategoryUrl()
    {
        if ($this->isCatalogSearchPage()) {
            $url = 'catalogsearch/result';
        } elseif ($this->isSeoHome() || $this->isHome()) {
            $url = 'ajaxlayerednavigation/';
        } else {
            $category = Mage::registry('current_category');
            $categoryPath = $category->getUrlPath();
            $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $url = str_replace($mageSuffix, '', $categoryPath);
        }
        
        return $url;
    }
}
