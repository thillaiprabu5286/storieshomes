<?php
class TM_AjaxLayeredNavigation_Adminhtml_Model_System_Config_Source_Style
{
     public function toOptionArray()
     {
        $result = array();
        $result[] = array(
            'value' => '1',
            'label' => Mage::helper('ajaxlayerednavigation')->__('Standart Navigation Style')
        );
        $result[] = array(
            'value' => '2',
            'label' => Mage::helper('ajaxlayerednavigation')->__('Standart Navigation Style (Fixed Column Height)')
        );
        $result[] = array(
            'value' => '3',
            'label' => Mage::helper('ajaxlayerednavigation')->__('Accordion Navigation Style')
        );
        $result[] = array(
            'value' => '4',
            'label' => Mage::helper('ajaxlayerednavigation')->__('Menu Navigation Style')
        );
        
        return $result;
     }
}
