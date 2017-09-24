<?php

class TM_AjaxLayeredNavigation_Model_Mysql4_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
{
    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
        /**
         * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
         */
        $collection = $filter->getLayer()->getProductCollection();

        $attribute  = $filter->getAttributeModel();

        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx';

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
            $connection->quoteInto("{$tableAlias}.value in (?)", $value)
        );


        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            join(' AND ', $conditions),
            array()
        );

        $collection->getSelect()->distinct(true);

        $result = $connection->fetchAll($collection->getSelect());

        $productIds = array();
        foreach ($result as $entity) {
            $productIds[] = $entity['entity_id'];
        }

        if (null !== Mage::registry('current_products_id')) {
            Mage::unregister('current_products_id');
        }

		if (null !== Mage::registry('current_attribute_id')) {
            if (is_array(Mage::registry('current_attribute_id'))) {
                $currentAttributeId = Mage::registry('current_attribute_id');
                $currentAttributeId[] = $attribute->getAttributeId();
            } else {
                $currentAttributeId = array();
                $currentAttributeId[] = Mage::registry('current_attribute_id');
                $currentAttributeId[] = $attribute->getAttributeId();
            }
            Mage::unregister('current_attribute_id');
            Mage::register('current_attribute_id', $currentAttributeId);
        } else {
            Mage::register('current_attribute_id', array($attribute->getAttributeId()));
        }

        Mage::register('current_products_id', $productIds);

        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter)
    {
        // clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
		$currentProducsId = array();
		$currentAttributeId = array();
		if (is_array(Mage::registry('current_products_id'))) {
		    $currentProducsId = Mage::registry('current_products_id');
		}
		if (is_array(Mage::registry('current_attribute_id'))) {
		    $currentAttributeId = Mage::registry('current_attribute_id');
		}

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $connection = $this->_getReadAdapter();
        $attribute  = $filter->getAttributeModel();
        $query = Mage::registry('query_request');
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            $query = Mage::app()->getRequest()->getParams();
        }
        //Zend_Debug::dump($query);
        $code = $attribute->getAttributeCode();
        $plus = false;
        if (null !== $query) {
            if (array_key_exists($code, $query)) {
                $plus = true;
            }
        }

        $attributeId = $attribute->getAttributeId();
        $tableAlias = $attribute->getAttributeCode() . '_idx1';

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );
        $filters = $filter->getCurrentFilter();

        $select
            ->join(
                array($tableAlias => $this->getMainTable()),
                join(' AND ', $conditions),
                array("{$tableAlias}.value", 'entity' => "{$tableAlias}.entity_id"))
            ->where("{$tableAlias}.value not IN (?)", $filters)
            // ->where("{$tableAlias}.entity_id not IN (?)", $currentProducsId)
            ;

        $from = $select->getPart('FROM');
        $selectString = strtolower($select->__toString());
        if (!empty($from[$attribute->getAttributeCode() . '_idx'])) {
            $joinData = $from[$attribute->getAttributeCode() . '_idx'];
            $remove = strtolower(sprintf(
                "%s `%s` AS `%s` ON %s",
                $joinData['joinType'],
                $joinData['tableName'],
                $attribute->getAttributeCode() . '_idx',
                $joinData['joinCondition']
            ));

            $selectString = str_replace($remove, '', $selectString);
        }

        $newId = $connection->fetchAll($selectString);

        $result = array();
        if (in_array((int)$attributeId, $currentAttributeId)) {
            $attributeData = array();
            foreach ($newId as $newValues) {
                $attributeData[$newValues['value']][] = $newValues['entity'];
            }
            foreach($attributeData as $attrID => $filteredIds) {
                $newElements = array_diff($filteredIds, $currentProducsId);
                if (count($newElements) > 0) {
                    $result[$attrID]['plus'] = count($newElements);
                } else {
                    $result[$attrID]['exist'] = count($filteredIds);
                }
            }
        } else {
            $attributeData = array();
            foreach ($newId as $newValues) {
                $attributeData[$newValues['value']][] = $newValues['entity'];
            }
            foreach($attributeData as $attrID => $filteredIds) {
                $result[$attrID] = count($filteredIds);
            }
        }

        return $result;

    }

    public function getAttributeDisplayType($attributeId)
    {
        $getFilterData = $this->_getReadAdapter()->select()
            ->from(array('alnf' => $this->getTable('ajaxlayerednavigation/filters')),array('display_type'))
            ->where('alnf.attribute_id = ?', $attributeId);

        $ranges = $this->_getReadAdapter()->fetchAll($getFilterData);
        if (count($ranges) > 0) {
            if ($ranges[0]['display_type'] > 1) {
                return $ranges[0]['display_type'];
            }
        } else {
            // Mage default range
            return 1;
        }
    }

    public function getOptionImage($attributeId)
    {
        $getFilterData = $this->_getReadAdapter()->select()
            ->from(array('alnf' => $this->getTable('ajaxlayerednavigation/filters')),array('filters_id'))
            ->where('alnf.attribute_id = ?', $attributeId);
        $filterRes = $this->_getReadAdapter()->fetchAll($getFilterData);

        if (count($filterRes) > 0 && null === $filterRes[0]['filters_id']) {
            return null;
        }

        if (count($filterRes) > 0) {
            $filterId = $filterRes[0]['filters_id'];
        } else {
            return null;
        }


        $getOptionData = $this->_getReadAdapter()->select()
            ->from(array('alno' => $this->getTable('ajaxlayerednavigation/options')),array('option_id','layered_image'))
            ->where('alno.filters_id = ?', $filterId);

        $images = $this->_getReadAdapter()->fetchAll($getOptionData);
        $result = array();
        if (count($images) > 0) {
            foreach($images as $image) {
                $result[$image['option_id']] = $image['layered_image'];
            }
            return $result;
        } else {
            return null;
        }

    }

    public function getOptionPosition($attributeId)
    {
        $getFilterData = $this->_getReadAdapter()->select()
            ->from(array('alnf' => $this->getTable('ajaxlayerednavigation/filters')),array('sort', 'order','filters_id'))
            ->where('alnf.attribute_id = ?', $attributeId);

        $sort = $this->_getReadAdapter()->fetchAll($getFilterData);
        if (count($sort) > 0) {
            if ($sort[0]['sort'] == 1) {
                $filterId = $sort[0]['filters_id'];
                $getOptionData = $this->_getReadAdapter()->select()
                    ->from(array('alno' => $this->getTable('ajaxlayerednavigation/options')),array('position','option_id'))
                    ->where('alno.filters_id = ?', $filterId);

                $position = $this->_getReadAdapter()->fetchAll($getOptionData);
                if (count($position)) {
                    $countPosition = array();
                    foreach($position as $pos) {
                        $countPosition[$pos['option_id']] = $pos['position'];
                    }
                } else {
                    $countPosition = 0;
                }
                return array('position' => $countPosition, 'order' => $sort[0]['order']);
            } else {
                return $sort[0];
            }
        } else {
            return null;
        }

    }
}
