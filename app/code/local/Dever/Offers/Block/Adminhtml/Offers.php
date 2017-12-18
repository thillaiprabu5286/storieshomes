<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 04/09/16
 * Time: 12:09 PM
 */
class Dever_Offers_Block_Adminhtml_Offers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'dever_offers';
        $this->_controller = 'adminhtml_offers';
        $this->_headerText = Mage::helper('dever_offers')->__('Manage Booking');

        parent::__construct();
    }
}