<?php

class Mango_Attributemapping_Model_Attributemapping extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('attributemapping/attributemapping');
    }

    protected function _isAttributeswatchesEnabled() {
        if ($this->_attributeswatches_enabled == NULL) {
            $this->_attributeswatches_enabled = Mage::helper('core')->isModuleEnabled('Mango_Attributeswatches');
        }
        return $this->_attributeswatches_enabled;
    }

    public function getResourceCollection() {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }
        $resource_collection = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
        $resource_collection
                ->getSelect()
                ->join(
                        array('child_attribute' => $this->getResource()->getTable("eav/attribute_option")), 'main_table.child_attribute_value_id = child_attribute.option_id', array())
                ->join(
                        array('child_attribute_label' => $this->getResource()->getTable("eav/attribute_option_value")), 'child_attribute_label.option_id = child_attribute.option_id and child_attribute_label.store_id=0', array("value"))
        ;
        /* insert mango_attributeswatches information  */
        if ($this->_isAttributeswatchesEnabled()) {
            $resource_collection
                    ->getSelect()
                    ->joinLeft(
                            array('attributeswatches' => $this->getResource()->getTable("attributeswatches/attributeswatches")), 'main_table.child_attribute_value_id = attributeswatches.option_id', array('*'));
        }
        return $resource_collection;
    }

    public function getGridCollection() {
        if (empty($this->_resourceCollectionName)) {
            Mage::throwException(Mage::helper('core')->__('Model collection resource name is not defined.'));
        }
        $resource_collection = Mage::getResourceModel($this->_resourceCollectionName, $this->_getResource());
        $resource_collection
                ->getSelect()
                ->join(
                        array('child_attribute' => $this->getResource()->getTable("eav/attribute_option")), 'main_table.child_attribute_value_id = child_attribute.option_id', array())
                ->join(
                        array('child_attribute_label' => $this->getResource()->getTable("eav/attribute_option_value")), 'child_attribute_label.option_id = child_attribute.option_id and child_attribute_label.store_id=0', array("value"))
                ->columns("GROUP_CONCAT(DISTINCT CONCAT(main_table.attributemapping_id , ':' , main_table.parent_attribute_value_id ) ORDER BY main_table.attributemapping_id ASC SEPARATOR ',') as parent_attribute_value_ids")
                ->group('main_table.child_attribute_value_id')
        ;
        /* insert mango_attributeswatches information  */
        if ($this->_isAttributeswatchesEnabled()) {
            $resource_collection
                    ->getSelect()
                    ->joinLeft(
                            array('attributeswatches' => $this->getResource()->getTable("attributeswatches/attributeswatches")), 'main_table.child_attribute_value_id = attributeswatches.option_id', array('*'));
        }
        return $resource_collection;
    }

    public function refresh() {
        $tablename = $this->getResource()->getTable("attributemapping/attributemapping");
        /* include also the attributes that will be displayed in the list and in the product view */
        $_fields = Mage::helper("attributemapping")->getAttributesWithMapping();
        if ($_fields) {
            /* will get attribute ids for parent and child attributes */
            foreach ($_fields as $_field) {
                if (!is_array($_field) || count($_field) != 2)
                    continue;
                /* get attribute id' */
                $_parent_attribute_id = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $_field[0]);
                $_child_attribute_id = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $_field[1]);
                if ($_parent_attribute_id && $_child_attribute_id) {
                    $query = "insert into " . $tablename . " (parent_attribute_id, child_attribute_id, child_attribute_value_id) "
                            . "select '" . $_parent_attribute_id . "', '" . $_child_attribute_id . "' , option_id from "
                            . $this->getResource()->getTable("eav/attribute_option")
                            . " ao inner join "
                            . $this->getResource()->getTable("eav/attribute")
                            . " a on a.attribute_id = ao.attribute_id "
                            . " where a.attribute_id = '" . $_child_attribute_id . "' "
                            . " and ao.option_id not in (select child_attribute_value_id from " . $tablename . ") ";
                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $connection->beginTransaction();
                    $connection->query($query);
                    $connection->commit();
                }
            }
        }
        return true;
    }

}
