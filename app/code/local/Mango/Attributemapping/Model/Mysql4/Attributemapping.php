<?php

class Mango_Attributemapping_Model_Mysql4_Attributemapping extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        // Note that the attributemapping_id refers to the key field in your database table.
        $this->_init('attributemapping/attributemapping', 'attributemapping_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param   string $field
     * @param   mixed $value
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where($this->getMainTable() . '.' . $field . '=?', $value)
                ->join(
                array('attribute_value' => $this->getTable("eav/attribute_option_value")), 'attribute_value.option_id = ' . $this->getMainTable() . '.child_attribute_value_id and attribute_value.store_id=0', array("value", "eav_option_id" => "option_id"))
        ;
        return $select;
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null) {


        if (is_null($field)) {
            $field = $this->getIdFieldName();
        }

        $read = $this->_getReadAdapter();
        if ($read && !is_null($value)) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

}
