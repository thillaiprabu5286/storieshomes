<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Option_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'ajaxlayerednavigation';
        $this->_controller = 'adminhtml_option';
        $this->_headerText = Mage::helper('ajaxlayerednavigation')->__('Manage Option Settigns');

        $this->_updateButton('save', 'label', Mage::helper('ajaxlayerednavigation')->__('Save'));
        $this->_removeButton('delete');
        $this->_removeButton('back');
        $this->_removeButton('reset');
//        $this->_updateButton('delete', 'label', Mage::helper('affiliatesuite')->__('Delete Proframe'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        return Mage::helper('ajaxlayerednavigation')->__("Manage Option Settigns");
    }

}