<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchAttributes
 *
 * @author user100500
 */
class TM_AjaxLayeredNavigation_Adminhtml_Model_System_Config_Source_SearchAttributes
{
    public function toOptionArray()
    {
        $attributes = Mage::getResourceModel('ajaxlayerednavigation/filters')->getFilterableAttributes();
        
        $result = array();
        foreach ($attributes as $attributesSearchable){
            $result[] = array(
                'value' => $attributesSearchable["attribute_id"],
                'label' => $attributesSearchable["frontend_label"]
            );
        }
        return $result;
    }
}
