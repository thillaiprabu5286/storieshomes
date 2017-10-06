<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AjaxLayeredNavigation module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $this->collectRoutes('frontend', 'standard');
        $front->addRouter('ajaxlayerednavigation', $this);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        if (!Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            return false;
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            return false;
        }

        $seoSuffix = Mage::getStoreConfig('ajaxlayerednavigation/seo/suffix');
        $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');

        $identifier = trim($request->getPathInfo(), '/');
        $identifier = str_replace($mageSuffix, '', $identifier);
        $urlParts = explode($seoSuffix, $identifier, 2);

        if (!isset($urlParts[1])) {
            return false;
        }

        $rewriteModel = Mage::getModel('core/url_rewrite');
        $rewriteModel->setStoreId(Mage::app()->getStore()->getId());
        $category = substr($urlParts[0], 0, -1);

        if ('catalogsearch/result' == $category) {
            $frontName = 'catalogsearch';
            $shouldBeSecure = '/catalogsearch/result/index';
            $controllerClass = 'result';
            $action = 'index';
            $categoryPath = '/catalogsearch/result/index';
        } elseif ('ajaxlayerednavigation' == $category) {
            $frontName = 'ajaxlayerednavigation';
            $shouldBeSecure = '/ajaxlayerednavigation/layered/view';
            $controllerClass = 'layered';
            $action = 'view';
            $categoryPath = '/ajaxlayerednavigation/layered/view';
        } else {
            $categoryPath = $category . $mageSuffix;
            $frontName = 'catalog';
            $shouldBeSecure = '/catalog/category/view';
            $controllerClass = 'category';
            $action = 'view';
            $rewriteModel->loadByRequestPath($categoryPath);

            if (!$rewriteModel->getId()) {
                return false;
            }
        }
        $modules = $this->getModuleByFrontName($frontName);

        $exist = false;
        foreach ($modules as $realModule) {
            $request->setRouteName($this->getRouteByFrontName($frontName));
            $this->_checkShouldBeSecure($request, $shouldBeSecure);
            $controllerClassName = $this->_validateControllerClassName($realModule, $controllerClass);
            if (!$controllerClassName) {
                continue;
            }
            $controllerInstance = Mage::getControllerInstance(
                $controllerClassName, $request, $this->getFront()->getResponse()
            );
            if (!$controllerInstance->hasAction($action)) {
                continue;
            }
            $exist = true;
            break;
        }

        if (!$exist) {
            return false;
        }

        // Set the required data on $request object

        if ('catalogsearch/result' != $category) {
            $request->setPathInfo($rewriteModel->getTargetPath());
            $request->setRequestUri('/' . $rewriteModel->getTargetPath());
        }

        $request->setModuleName($frontName)
            ->setControllerName($controllerClass)
            ->setActionName($action)
            ->setControllerModule($realModule);


        if ('catalogsearch/result' == $category) {
            $request->setQuery($request->getQuery());
        } elseif ('ajaxlayerednavigation' == $category) {

        } else {
            $request->setParam('id', $rewriteModel->getCategoryId())
                ->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $categoryPath
            );
        }
        $params = explode('/', trim(str_replace('/f/', '/', $urlParts[1]), '/'));

        $activeFilters = array();

        for ($i = 0; $i < count($params) - 1; $i++) {
            if ($params[$i] == "cat") {
                if ($params[$i+1] == "q") {
                    continue;
                }
            }
            if (isset($params[$i + 1])) {
                $activeFilters[$params[$i]] = $params[$i + 1];
                ++$i;
            }
        }
        $activeFilters += $request->getPost();
        $newActiveFilters = array();
        foreach ($activeFilters as $key => $value) {
            if ("q" == $key) {
                $newActiveFilters[$key] = str_replace('+',' ', urldecode($value));
            } else {
                $newActiveFilters[$key] = $value;
            }
        }
        $request->setParams($newActiveFilters);
        $delimiter = TM_AjaxLayeredNavigation_Model_Item::getDelimiter();
        if ('ajaxlayerednavigation' != $category) {
            $request->setParam('filters', $newActiveFilters);
        }

        $request->setDispatched(true);
        $controllerInstance->dispatch($action);

        return true;
    }
}
