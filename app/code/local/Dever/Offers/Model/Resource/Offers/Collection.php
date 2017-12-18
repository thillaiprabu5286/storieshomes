<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 16/07/17
 * Time: 5:03 PM
 */
class Dever_Offers_Model_Resource_Offers_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('dever_offers/offers');
    }
}