<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Option_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ajaxlayerednavigation_option');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('create_fieldset', array('legend'=>Mage::helper('ajaxlayerednavigation')->__('Settings'), 'class' => 'fieldset'));

        $this->_addElementTypes($fieldset);
        
        $fieldset->addField('category_image', 'image', array(
            'name'      => 'category_image',
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Category Image'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Category Image')
        ));
        
        $fieldset->addField('category_title', 'text', array(
            'name'      => 'category_title',
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Category Title'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Category Title')
        ));

        $fieldset->addField('category_description', 'textarea', array(
            'name'      => 'category_description',
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Category Description'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Category Description')
        ));

        // $fieldset->addField('product_page_image', 'image', array(
            // 'name'      => 'product_page_image',
            // 'visible'   => false, 
            // 'label'     => Mage::helper('ajaxlayerednavigation')->__('Product Page Image'),
            // 'title'     => Mage::helper('ajaxlayerednavigation')->__('Product Page Image')
        // ));

        $fieldset->addField('layered_image', 'image', array(
            'name'      => 'layered_image',
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Layered Image'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Layered Image')
        ));

        $fieldset->addField('position', 'text', array(
            'name'      => 'position',
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Position'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Position')
        ));

        $fieldset->addField('foption_id', 'hidden', array(
            'name'      => 'foption_id'
        ));

        if ($model->getData('foption_id')) {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();

    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('ajaxlayerednavigation/adminhtml_option_helper_image')
        );
    }

}