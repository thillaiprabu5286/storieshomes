<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Layer Attribute Filter Resource Model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mango_Attributemapping_Model_Mysql4_Catalog_Layer_Filter_Parent extends Mage_Core_Model_Resource_Db_Abstract {

    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct() {
        $this->_init('catalog/product_index_eav', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $values) {
        $_child_attribute_values_ids = $this->_getChildAttributeValueIds($filter, $values);
        $collection = $filter->getLayer()->getProductCollection();
        $attribute = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
            $connection->quoteInto("{$tableAlias}.value in ( ? )", $_child_attribute_values_ids)
        );
        $collection->getSelect()->join(
                array($tableAlias => $this->getMainTable()), implode(' AND ', $conditions), array()
        )->distinct();

        /* STORE THIS VALUE, CAN BE USED IN ATTRIBUTESWATCHES OR ANY OTHER PLUGIN... */
        Mage::register("attribute_mapping_selected_values_" . $attribute->getAttributeCode() , $_child_attribute_values_ids);

        $this->setConditions($attribute, $conditions, $this->getMainTable(), $tableAlias);
        return $this;
    }

    protected function _getChildAttributeValueIds($filter, $values) {
        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');
        $_parent_attribute_id = $filter->getParentAttribute()->getId();
        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');
        /**
         * Retrieve our table name
         */
        $table = $resource->getTableName('attributemapping');
        /**
         * Execute the query and store the results in $results
         */
        $_select = $readConnection->quoteInto('SELECT distinct(child_attribute_value_id) FROM ' . $table . ' where parent_attribute_value_id in ( ? ) ', $values);

        //echo $_select; 
        //exit;

        $_child_attribute_value_ids = $readConnection->fetchCol($_select . ' and parent_attribute_id = ? ', $_parent_attribute_id);
        return $_child_attribute_value_ids;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getParentCount($filter) {
        // clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $connection = $this->_getReadAdapter();
        $attribute = $filter->getAttributeModel();
        //$tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $tableAlias = $attribute->getAttributeCode() . '_idy';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );
        /* get count for multiple selection */
        $_from = $select->getPart(Zend_Db_Select::FROM);
        $_all_conditions = $this->getConditions();
        foreach ($_from as $index => $condition) {
            if ($index == ($attribute->getAttributeCode() . "_idx"))
                unset($_from[$attribute->getAttributeCode() . "_idx"]);
        }
        $select->setPart(Zend_Db_Select::FROM, $_from);
        $select
                ->join(
                        array($tableAlias => $this->getMainTable()), join(' AND ', $conditions), array('attributemapping.parent_attribute_value_id', 'count' => new Zend_Db_Expr("COUNT({$tableAlias}.entity_id)")))
                /* have to add the attribute mapping table and group by the parent id... */
                ->join(
                        array('attributemapping' => Mage::getSingleton('core/resource')->getTableName("attributemapping/attributemapping")), "attributemapping.child_attribute_value_id = {$tableAlias}.value", array('parent_attribute_value_id')
                )
                ->group("attributemapping.parent_attribute_value_id")
                ->where("attributemapping.parent_attribute_value_id > 0");
        return $connection->fetchPairs($select);
    }

    public function setConditions($attribute, $conditions, $tablename, $tablealias) {
        $_all_conditions = array();
        if (Mage::registry("filter_conditions")) {
            Mage::unregister("filter_conditions");
        } else {
            $_all_conditions = Mage::registry("filter_conditions");
            Mage::unregister("filter_conditions");
        }
        $_new_conditions = array("tablealias" => $tablealias, "tablename" => $tablename, 'conditions' => $conditions);
        $_all_conditions[$attribute->getAttributeCode()] = $_new_conditions;
        Mage::register("filter_conditions", $_all_conditions);
        return;
    }

    public function getConditions() {
        if (Mage::registry("filter_conditions"))
            return Mage::registry("filter_conditions");
        return false;
    }

}
