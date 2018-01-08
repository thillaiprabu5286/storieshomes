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

        $this->addColumn('message', array(
            'header'    => $helper->__('Message'),
            'index'     => 'message'
        ));

        $this->addColumn('status', array(
            'header'    => $helper->__('Coupon Used'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                '0'         => $helper->__("No"),
                '1'         => $helper->__("Yes"),
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>  $helper->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => $helper->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('booking');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('dever_offers')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
        return $this;
    }
}