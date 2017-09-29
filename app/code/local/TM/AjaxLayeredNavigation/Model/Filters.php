<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible partner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Model_Filters extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
    parent::_construct();
    $this->_init('ajaxlayerednavigation/filters');
    }

    public function refreshAttributes()
    {
        return $this->_getResource()->refreshAttributes();
    }
	
}