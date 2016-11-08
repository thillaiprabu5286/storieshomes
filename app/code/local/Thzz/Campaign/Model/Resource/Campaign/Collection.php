<?php

class Thzz_Campaign_Model_Resource_Campaign_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('thzz_campaign/campaign');
    }
}