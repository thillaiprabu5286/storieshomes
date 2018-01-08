<?php

class Dever_Offers_Block_Adminhtml_Offers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'dever_offers';
        $this->_controller = 'adminhtml_offers';

        parent::__construct();
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        return Mage::helper('dever_offers')->__('Edit Booking');
    }
}