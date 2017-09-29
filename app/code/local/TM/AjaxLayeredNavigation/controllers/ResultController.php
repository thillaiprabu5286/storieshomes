<?php
require 'app/code/core/Mage/CatalogSearch/controllers/ResultController.php';

class TM_AjaxLayeredNavigation_ResultController
        extends Mage_CatalogSearch_ResultController
{
    /**
     * Display search result
     */
    public function indexAction()
    {
        $query = array();
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')
                && !Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            if($this->getRequest()->getParam('ajaxpro')){
                return parent::indexAction();
            }

            $query      = $this->getRequest()->getParam('filters');
            $categoryId = $this->getRequest()->getParam('id');
            $category   = Mage::getModel('catalog/category')->load($categoryId);
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $seoSuffix  = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
            $isSeoUrl   = strpos($currentUrl, $seoSuffix);
            if (!$isSeoUrl) {
                $query = $this->getRequest()->getQuery();
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $model = Mage::getResourceModel('ajaxlayerednavigation/filters');
                $seoOptions = $model->getSeoOptionsValue();
                //$seoCat = Mage::registry('seo_categories');
                $urlPath = $urlPath = 'catalogsearch/result/'. $seoSuffix .'/';
                $seoCat = array();
                $categories = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('is_active');
                foreach($categories as $category) {
                    if($category->getIsActive() && null !== $category->getUrlKey()) {
                        $seoCat[$category->getId()] = $category->getUrlKey();
                    }
                }
                $model = Mage::getModel('core/url');
                $url = $model->getDirectUrl($urlPath);
                $url = substr($url, 0, -1);

                foreach($query as $key=>$value) {
                    if (!is_numeric($value) && empty($value)) {
                        continue;
                    }
                    if ('cat' == $key) {
                        $url .= '/'.$key .'/'.$seoCat[$value];
                    } elseif ('q' !== $key) {
                        $url .= '/'.$key .'/'.$seoOptions[$value];
                    } else {
                        $url .= '/'.$key .'/'.$value;
                    }
                }
                $url = str_replace('/index','',$url);
                $url .= $mageSuffix;
                $url = urldecode($url);
                $url = str_replace(' ','+',$url);
                header ('HTTP/1.1 301 Moved Permanently');
                header ('Location: ' . $url);
                exit;
            }
        } else {
            $query = $this->getRequest()->getQuery();
        }

        Mage::register('query_request', $query);

        if (!$this->getRequest()->isXmlHttpRequest()) {
            return parent::indexAction();
        }
        $this->loadLayout();
        //$this->renderLayout();
        $layout = $this->getLayout();
        //$update = $layout->getUpdate();
        //$update->merge('catalogsearch_result_index');
        //$layout->generateXml();
        //$layout->generateBlocks();
        $blocks = array(
            'list'     => 'search.result',
            'filter'   => 'tm.catalogsearch.navigation',
            'brend'    => 'my.catalogsearch.top.brend'
        );
        $result = array();
        foreach ($blocks as $key => $block) {
            $_block = $layout->getBlock($block);
            if ($_block) {
                $result[$key] = $_block->toHtml();
            }
        }

        $helper = Mage::helper('ajaxlayerednavigation');
        $result['minRange'] = $helper->getMinPrice($this->getRequest());
        $result['maxRange'] = $helper->getMaxPrice($this->getRequest());

        $newQuery = Mage::helper('catalogsearch')->getQuery();
        $result['categoryName'] = '';

        $this->getResponse()->setBody(Zend_Json::encode($result));

    }
}
