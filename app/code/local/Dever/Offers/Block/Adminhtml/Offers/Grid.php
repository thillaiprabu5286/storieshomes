<?php

class Dever_Offers_Block_Adminhtml_Offers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('offersGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('dever_offers/offers_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('dever_offers');

        $this->addColumn('id', array(
            'header'    => $helper->__('ID'),
            'width'     => '50px',
            'index'     => 'id',
            'type'  => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => $helper->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('email', array(
            'header'    => $helper->__('Email'),
            'index'     => 'email'
        ));

        $this->addColumn('telephone', array(
            'header'    => $helper->__('Phone'),
            'index'     => 'telephone'
        ));

        $this->addColumn('product_name', array(
            'header'    => $helper->__('Product Name'),
            'index'     => 'product_name'
        ));

        $this->addColumn('price', array(
            'header'    => $helper->__('Offer Price'),
            'index'     => 'price'
        ));

        $this->addColumn('coupon_code', array(
            'header'    => $helper->__('Coupon Code'),
            'index'     => 'coupon_code'
        ));

        /*$this->addColumn('sms_sent', array(
            'header'    => $helper->__('Notify Customer'),
            'index'     => 'sms_sent',
            'type'      => 'options',
            'options'   => array(
                '1'         => $helper->__("Yes"),
                '0'         => $helper->__("No"),
            ),
        ));*/

        return parent::_prepareColumns();
    }
}