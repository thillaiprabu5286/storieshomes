<?php

class TM_AjaxLayeredNavigation_Block_Layer_State extends Mage_Catalog_Block_Layer_State
{
    /**
     * Initialize Layer State template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tm/ajaxlayerednavigation/layer/state.phtml');
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = array();
        }
        return $filters;
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
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'tmdailydeals');
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
            if ($this->isCatalogSearchPage()) {
                $query = Mage::registry('query_request');
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $url = Mage::getBaseUrl() . "catalogsearch/result/" . $seoSuffix;
                if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
                    $url .= "/isAjax/1";
                }
                $url .= "/q/" . str_replace(" ", "+", $query["q"]) . $mageSuffix;
                return $url;
            } elseif ($this->isAdvancedSearchPage()) {
                $urlPath = 'catalogsearch/advanced/index';
                $url = Mage::getUrl($urlPath, array(
                    '_nosid' => true
                ));

                return $url;
            } elseif ($this->isTmAttributePage() || $this->isTmDealsPage()) {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $urlParts = explode("/".$seoSuffix."/", $currentUrl);
                $url = $urlParts[0];
                $url .= "/" . $seoSuffix;
                if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
                    $url .= "/isAjax/1";
                }
                $url .= $mageSuffix;
                return $url;
            } else {
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $currentUrl = Mage::helper('core/url')->getCurrentUrl();
                $urlParts = explode("/".$seoSuffix."/", $currentUrl);
                $url = $urlParts[0];
                $url .= "/" . $seoSuffix;
                if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
                    $url .= "/isAjax/1";
                }
                $url .= $mageSuffix;
                return $url;
            }

            $currentCategory = Mage::registry('current_category');
            $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
            $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            $urlCatPath = $currentCategory->getUrl();

            $url = str_replace($mageSuffix, '', $urlCatPath) .'/'. $seoSuffix;
            $res = array();
            if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
                $res['isAjax'] = 1;
            }
            foreach($res as $key=>$value) {
                if (null!==$value) {
                    $url .= '/'.$key .'/'.str_replace($mageSuffix, '', $value);
                }
            }
            $url = str_replace('/index.php','',$url);
            $url = str_replace('/index','',$url);
            $url .= $mageSuffix;

            return $url;
        }
        if ($this->isAdvancedSearchPage()) {
            $urlPath = 'catalogsearch/advanced/result';
            $url = Mage::getUrl($urlPath, array(
                '_nosid' => true
            ));

            return $url;
        }

        $filterState = array();
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        if (Mage::getStoreConfig('ajaxlayerednavigation/general/use_ajax')) {
            $filterState["isAjax"] = 1;
        }
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            $params['_nosid']     = true;
        }
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $filterState;
        $params['_escape']      = true;

        return Mage::getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer(Mage::getSingleton('catalog/layer'));
        }
        return $this->_getData('layer');
    }
}
