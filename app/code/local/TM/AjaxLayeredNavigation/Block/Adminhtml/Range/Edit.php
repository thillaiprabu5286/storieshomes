<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Range_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'ajaxlayerednavigation';
        $this->_controller = 'adminhtml_range';
        $this->_headerText = Mage::helper('ajaxlayerednavigation')->__('Manage Ranges');

        $this->_updateButton('save', 'label', Mage::helper('ajaxlayerednavigation')->__('Save Range'));
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        return Mage::helper('ajaxlayerednavigation')->__("Edit Ranges");
    }

}