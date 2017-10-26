<?php
class Mango_Attributemapping_Model_Mysql4_Attributemapping_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('attributemapping/attributemapping');
    }
    public function getSize() {
        if (is_null($this->_totalRecords)) {
            $sql = "select count(*) from (" . $this->getSelectCountSql() . ") as count_groups";
            /**
             * Get the resource model
             */
            $resource = Mage::getSingleton('core/resource');
            /**
             * Retrieve the read connection
             */
            $readConnection = $resource->getConnection('core_read');
            //$sku = $readConnection->fetchOne($query);
            // fetch all rows since it's a joined table and run a count against it.
            $this->_totalRecords = $readConnection->fetchOne($sql); //count( $this->getConnection()->fetchall( $sql, $this->_bindParams ) );
        }
        return intval($this->_totalRecords);
    }
}
