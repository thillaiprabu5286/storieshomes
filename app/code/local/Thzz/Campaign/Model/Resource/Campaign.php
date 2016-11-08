<?php
/**
 * Created by PhpStorm.
 * User: thillai.rajendran
 * Date: 6/12/16
 * Time: 1:11 PM
 */
class Thzz_Campaign_Model_Resource_Campaign extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('thzz_campaign/campaign', 'id');
    }
}