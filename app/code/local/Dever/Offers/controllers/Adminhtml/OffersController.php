<?php

class Dever_Offers_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dever_offers');
    }
}