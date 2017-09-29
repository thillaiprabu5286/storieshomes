<?php

require 'app/code/core/Mage/Catalog/controllers/CategoryController.php';

class TM_AjaxLayeredNavigation_LayeredController
    extends Mage_Catalog_CategoryController
{

    protected function _initCatagory()
    {
        Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
        $categoryId = (int) $this->getRequest()->getParam('id', false);
        if (!$categoryId) {
            $categoryId = Mage::getStoreConfig('ajaxlayerednavigation/home_cat/category');
        }
        if (!$categoryId) {
            return false;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
        Mage::register('current_category', $category);
        try {
            Mage::dispatchEvent('catalog_controller_category_init_after', array('category' => $category, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $category;
    }

    public function setRequestData()
    {

    }

    public function viewAction()
    {
        $query = array();
        $seoQuery = array();
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            if($this->getRequest()->getParam('ajaxpro')){
                return parent::viewAction();
            }
            $seoQuery = $this->getRequest()->getParams();
            $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
            foreach($seoQuery as $key=>$value) {
                $query[$key] = str_replace($mageSuffix, '', $value);
            }
        } else {
            $query = $this->getRequest()->getQuery();
        }
        Mage::register('query_request', $query);

        if ($this->getRequest()->isXmlHttpRequest()) {
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

            $result['categoryName'] = '<div class="page-title category-title"> <h1>'
                . $this->getCategoryName() . '</h1></div>';

            $this->getResponse()->setBody(Zend_Json::encode($result));
        } else {
            if ($category = $this->_initCatagory()) {
                $design = Mage::getSingleton('catalog/design');
                $settings = $design->getDesignSettings($category);

                // apply custom design
                if ($settings->getCustomDesign()) {
                    $design->applyCustomDesign($settings->getCustomDesign());
                }

                Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());

                $update = $this->getLayout()->getUpdate();
                $update->addHandle('default');

                if (!$category->hasChildren()) {
                    $update->addHandle('catalog_category_layered_nochildren');
                }

                $this->addActionLayoutHandles();
                $update->addHandle($category->getLayoutUpdateHandle());
                $update->addHandle('CATEGORY_' . $category->getId());
                $this->loadLayoutUpdates();

                // apply custom layout update once layout is loaded
                if ($layoutUpdates = $settings->getLayoutUpdates()) {
                    if (is_array($layoutUpdates)) {
                        foreach($layoutUpdates as $layoutUpdate) {
                            $update->addUpdate($layoutUpdate);
                        }
                    }
                }

                $this->generateLayoutXml()->generateLayoutBlocks();
                // apply custom layout (page) template once the blocks are generated
                if ($settings->getPageLayout()) {
                    $this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
                }

                if ($root = $this->getLayout()->getBlock('root')) {
                    $root->addBodyClass('categorypath-' . $category->getUrlPath())
                        ->addBodyClass('category-' . $category->getUrlKey());
                }

                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('checkout/session');

                $title = $category->getName();
                $this->getLayout()->getBlock('head')->setTitle($title);

                $this->renderLayout();
            }
            elseif (!$this->getResponse()->isRedirect()) {

                $this->_forward('noRoute');
            }
        }
    }

    public function getCategoryName()
    {
        return Mage::registry('current_category')->getName();
    }
}
