<?php

require 'app/code/core/Mage/Catalog/controllers/CategoryController.php';

class TM_AjaxLayeredNavigation_CategoryController
    extends Mage_Catalog_CategoryController
{
    public function viewAction()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::viewAction();
        }
        $query = array();

        // 301 redirect for seo urls
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            if($this->getRequest()->getParam('ajaxpro')){
                return parent::viewAction();
            }

            $query = $this->getRequest()->getParam('filters');
            $categoryId = $this->getRequest()->getParam('id');
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if (!$category->getIsAnchor()) {
                return parent::viewAction();
            }

            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
            $isSeoUrl = strpos($currentUrl, $seoSuffix);
            if (!$isSeoUrl && $currentUrl !== $category->getUrl()) {
                $query = $this->getRequest()->getQuery();
                $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
                $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                $model = Mage::getResourceModel('ajaxlayerednavigation/filters');
                $seoOptions = $model->getSeoOptionsValue();

                $urlPath = str_replace($mageSuffix, '', $category->getUrlPath()) . '/'. $seoSuffix .'/';

                $model = Mage::getModel('core/url');
                $url = $model->getDirectUrl($urlPath);
                $url = substr($url, 0, -1);
                $notParams = array('___store', '___from_store');
                $listVars = array('price', 'n', 'p', 'mode', 'dir', 'order', 'limit');
                foreach($query as $key=>$value) {
                    if (!in_array($key, $notParams)) {
                        if(in_array($key, $listVars)) {
                            $url .= '/'.$key .'/'.$value;
                        } else {
                            if (null!==$value) {
                                $url .= '/'.$key .'/'.$seoOptions[$value];
                            }
                        }
                    }
                }

                $url = str_replace('/index.php','',$url);
                $url = str_replace('/index','',$url);

                $url .= $mageSuffix;
                header ('HTTP/1.1 301 Moved Permanently');
                header ('Location: ' . $url);
                exit;
            }
        } else {
            $query = $this->getRequest()->getQuery();
        }
        Mage::register('query_request', $query);
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return parent::viewAction();
        }
        $this->_initCatagory();
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->merge('catalog_category_layered');
        $layout->generateXml();
        $layout->generateBlocks();
        $blocks = array(
            'list'     => 'category.products',
            'filter'   => 'tm.catalog.left.navigation',
            'brend'    => 'tm.catalog.top.brend'
        );

        $result = array();
        foreach ($blocks as $key => $block) {
            $result[$key] = $layout->getBlock($block)->toHtml();
        }

        $helper = Mage::helper('ajaxlayerednavigation');
        $result['minRange'] = $helper->getMinPrice($this->getRequest());
        $result['maxRange'] = $helper->getMaxPrice($this->getRequest());

        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function getCategoryName()
    {
        return Mage::registry('current_category')->getName();
    }
}
