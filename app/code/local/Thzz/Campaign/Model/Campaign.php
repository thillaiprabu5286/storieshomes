<?php
/**
 * Created by PhpStorm.
 * User: thillai.rajendran
 * Date: 6/12/16
 * Time: 1:11 PM
 */
class Thzz_Campaign_Model_Campaign extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('thzz_campaign/campaign');
    }
}