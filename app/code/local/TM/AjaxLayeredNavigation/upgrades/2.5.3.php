<?php

class TM_AjaxLayeredNavigation_Upgrade_2_5_3 extends TM_Core_Model_Module_Upgrade
{

    public function getOperations()
    {
        return array(
            'configuration' => $this->_getConfiguration(),
        );
    }

    private function _getConfiguration()
    {
        return array('ajaxlayerednavigation/general/enabled' => 1);
    }
}
