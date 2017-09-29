<?php
class TM_AjaxSearch_Adminhtml_Model_System_Config_Source_CmsSearchAttributes
{
    public function toOptionArray()
    {
        $attributes = array('title', 'content');
        $result = array();
        foreach ($attributes as $attribute) {
            $result[] = array(
                'value' => $attribute,
                'label' => ucfirst($attribute)
            );
        }
        return $result;
    }
}
