<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Option_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media') . 'ajaxlayerednavigation/' . $this->getValue();
        }
        return $url;
    }
}
