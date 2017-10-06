<?php

class TM_AjaxLayeredNavigation_Block_AdvancedSearch_Layer_Filter_Attribute 
        extends TM_AjaxLayeredNavigation_Block_Layer_Filter_Attribute
{
    /**
     * Set filter model name
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'ajaxlayerednavigation/advancedSearch_layer_filter_attribute';
    }
}
