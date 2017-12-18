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
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}