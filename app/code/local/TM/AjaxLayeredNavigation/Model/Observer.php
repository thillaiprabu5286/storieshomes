<?php

class TM_AjaxLayeredNavigation_Model_Observer
{
    public function addToRegisterRootQuery($observer)
    {
        if($observer->getEvent()->getControllerAction()->getFullActionName() == 'ajaxlayerednavigation_layered_view')
        {
            $query = $observer->getControllerAction()->getRequest()->getQuery();
            Mage::register('root_request', $query);
        }
    }

    public function tmcachePrepareCacheKey($object)
    {
        $params = $object->getParams();
        $params->addData(array(
            'ajaxlayerednavigation_is_mobile' => Mage::helper("zodiya_core")->isMobile()
        ));
    }

    public function processAttributepageRequest($observer)
    {
        if (!Mage::getStoreConfigFlag('ajaxlayerednavigation/general/enabled')) {
            return false;
        }

        $controllerAction = $observer->getEvent()->getControllerAction();
        $request = $controllerAction->getRequest();
        if (!$request->isXmlHttpRequest()
            /*|| !$request->getParam('aln', false)*/) {

            return false;
        }

        $layout = $controllerAction->getLayout();
        $blocks = array(
            'list'     => 'attributepage.attribute.view',
            'filter'   => 'tm.catalog.left.navigation',
            'brend'    => 'tm.catalog.top.brend'
        );

        $result = array();
        foreach ($blocks as $key => $block) {
            $result[$key] = $layout->getBlock($block)->toHtml();
        }

        $helper = Mage::helper('ajaxlayerednavigation');
        $result['minRange'] = $helper->getMinPrice($request);
        $result['maxRange'] = $helper->getMaxPrice($request);

        $controllerAction->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function processDealspageRequest($observer)
    {
        if (!Mage::getStoreConfigFlag('tm_dailydeals/general/enabled')) {
            return false;
        }

        $controllerAction = $observer->getEvent()->getControllerAction();
        $request = $controllerAction->getRequest();
        if (!$request->isXmlHttpRequest()
            /*|| !$request->getParam('aln', false)*/) {
            return false;
        }

        $layout = $controllerAction->getLayout();
        $blocks = array(
            'list'     => 'product_list',
            'filter'   => 'tm.catalog.left.navigation',
            'brend'    => 'deals.top.block'
        );

        $result = array();
        foreach ($blocks as $key => $block) {
            $result[$key] = $layout->getBlock($block)->toHtml();
        }

        $helper = Mage::helper('ajaxlayerednavigation');
        $result['minRange'] = $helper->getMinPrice($request);
        $result['maxRange'] = $helper->getMaxPrice($request);

        $controllerAction->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
