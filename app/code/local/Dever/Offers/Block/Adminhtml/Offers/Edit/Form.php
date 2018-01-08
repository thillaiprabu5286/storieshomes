<?php
/**
 * Created by PhpStorm.
 * User: thillai.rajendran
 * Date: 6/22/16
 * Time: 12:41 PM
 */
class Dever_Offers_Block_Adminhtml_Offers_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $booking = Mage::registry('current_booking');
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('editForm', array(
            'legend' => Mage::helper('dever_offers')->__('Booking Information')
        ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('dever_offers')->__('Change Status'),
            'title'     => Mage::helper('dever_offers')->__('Change Status'),
            'required'  => false,
            'scope'     => 'global',
            'values'    => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray()
        ));

        if ($booking && $booking->getId()) {
            $form->setValues($booking->getData());
            $form->setDataObject($booking);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}