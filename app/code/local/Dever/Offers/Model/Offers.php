<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 16/07/17
 * Time: 5:02 PM
 */
class Dever_Offers_Model_Offers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('dever_offers/offers');
    }
}