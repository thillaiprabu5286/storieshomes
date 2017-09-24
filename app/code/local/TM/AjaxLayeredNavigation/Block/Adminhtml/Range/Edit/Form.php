<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Range_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('ajaxlayerednavigation_range');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('ajaxlayerednavigation')->__('General Information'), 'class' => 'fieldset'));

        $fieldset->addField('range', 'text', array(
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Range'),
            'title'     => Mage::helper('ajaxlayerednavigation')->__('Range'),
            'name'      => 'range',
            'note'      => Mage::helper('ajaxlayerednavigation')->__('Example: 0,100,1000,10000,100000')
        ));

        $fieldset->addField('range_id', 'hidden', array(
            'name'      => 'range_id',
            'required'  => false
        ));

        if (count($model->getData())) {
            $form->setValues($model->getData());
        } else {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPageData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();

    }
}