<?php

class Mango_Attributeswatches_PriceController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        echo "";
        exit;
    }

    public function getAjaxPriceAction() {
        $_id = $this->getRequest()->getParam("productid");
        $_ids = $this->getRequest()->getParam("productids");

        $_json_response = array();

        if (!trim($_id) && $_ids) {
            $_product_ids = explode(",", $_ids);
            $_item = Mage::getResourceModel('catalog/product_collection')
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addAttributeToFilter('entity_id', array('in' => $_product_ids))
                    ->addAttributeToSort('price')
                    ->setPageSize(1)
                    ->setCurPage(1)
                    ->getFirstItem()
            ;
            $_id = $_item->getId();
        }

        $_product = Mage::getModel('catalog/product')->load($_id);
        $productBlock = $this->getLayout()->createBlock('catalog/product_price')
                ->setProduct($_product)
                ->setTemplate('catalog/product/price.phtml')
                ->toHtml();

        $_json_response = array(
            'result' => 'success',
            'price_html' => $productBlock
        );

        $this->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setBody(json_encode($_json_response))
                ->sendResponse();
        exit;
    }

}
