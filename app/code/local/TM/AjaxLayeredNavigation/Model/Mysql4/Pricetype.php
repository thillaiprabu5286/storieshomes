<?php

class TM_AjaxLayeredNavigation_Model_Mysql4_Pricetype extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
{
    public function getActiveCount($filter, $from, $to)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $oldSelect = $collection->getSelect();
        $select = clone $oldSelect;
        
        $currentProductIds = $this->_getReadAdapter()->fetchAll($select);

        $priceExpression = $this->_getPriceExpression($filter, $select, false);

        if ($from !== '' && $to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter))
            . ' AND '
            . $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        } elseif ($from !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter));
        } elseif ($to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        }
        
        $state = $filter->getLayer()->getState()->getFilters();
        
        $stateCount = count($state);
        foreach ($state as $item) {
            //print_r($item->getValue());die;
            //$value = explode(',', $item->getValue());
            $value = $item->getValue();
            if (count($value) == 2) {
                if (($value[0] == $from) && ($value[1] == $to)) {
                    return 0;
                }
            }
        }
        
        if ($stateCount == 0) {
            $select->where($where);
            $result = $this->_getReadAdapter()->fetchAll($select);
        } else {
            $select->orWhere($where);
            $result = $this->_getReadAdapter()->fetchAll($select);
        }
        $count = $this->getCountProductIds($currentProductIds, $result);
        return $count;
        
    }
    
    public function getRangeCount($filter, $from, $to, $filterName)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $oldSelect = $collection->getSelect();
        $select = clone $oldSelect;
        $select->limit('1000000');
        $currentProductIds = $this->_getReadAdapter()->fetchAll($select);

        $priceExpression = $this->_getPriceExpression($filter, $select, false);

        if ($from !== '' && $to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter))
            . ' AND '
            . $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        } elseif ($from !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter));
        } elseif ($to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        }
        
        $state = $filter->getLayer()->getState()->getFilters();
        
        $stateCount = count($state);
        foreach ($state as $item) {
            $value = $item->getValue();
            if (count($value) == 2) {
                if (($value[0] == $from) && ($value[1] == $to)) {
                    return 0;
                }
            }
        }
        
        if ($stateCount == 0) {
            $select->where($where);
            
            $result = $this->_getReadAdapter()->fetchAll($select);
        } else {
            $select->orWhere($where);
            
            $result = $this->_getReadAdapter()->fetchAll($select);
        }
        $oldQuery = array();
        if (Mage::registry('query_request')) {
            $oldQuery = Mage::registry('query_request');
        } elseif (Mage::registry('root_request')) {
            $oldQuery = Mage::registry('root_request');
        }
        
        $query = array();
        $count = 0;
        if (array_key_exists($filterName, $oldQuery)) {
            $count = count($result) - count($currentProductIds);
        } elseif (count($oldQuery) > 0) {
            $count = count($result);
        } else {
            $count = count($result);
        }
        
        return $count;
        
    }
    
    public function getNotApplyRangeCount($filter, $from, $to)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $oldSelect = $collection->getSelect();
        $select = clone $oldSelect;
        $currentProductIds = $this->_getReadAdapter()->fetchAll($select);
        
        $priceExpression = $this->_getPriceExpression($filter, $select, false);
        
        if ($from !== '' && $to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter))
            . ' AND '
            . $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        } elseif ($from !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} >= ?", $this->_getComparingValue($from, $filter));
        } elseif ($to !== '') {
            $where = $this->_getReadAdapter()->quoteInto("{$priceExpression} <= ?", $this->_getComparingValue($to, $filter));
        }
        
        $state = $filter->getLayer()->getState()->getFilters();
        
        $stateCount = count($state);
        
        foreach ($state as $item) {
            $value = explode(',', $item->getValue());
            if (count($value) == 2) {
                if (($value[0] == $from) && ($value[1] == $to)) {
                    return 0;
                }
            }
        }
        
        if ($stateCount == 0) {
            $select->where($where);
            $result = $this->_getReadAdapter()->fetchAll($select);
        } else {
            $select->orWhere($where);
            $result = $this->_getReadAdapter()->fetchAll($select);
        }
        $count = $this->getCountProductIds($currentProductIds, $result);
        return count($result);
    }
    
    public function getCountProductIds($currentIds, $applyIds)
    {
        $currentId = array();
        $count = 0;
        foreach ($currentIds as $entity) {
            $currentId[] = $entity['entity_id'];
        }

        foreach ($applyIds as $id) {
            if (!in_array($id['entity_id'], $currentId)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    public function applyPriceRange($filter)
    {
        $interval = $filter->getInterval();
        
        if (!$interval) {
            return $this;
        }
        
        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select, false);
        $where = array();

        foreach ($interval as $value) {
            list($from, $to) = $value;
            if ($from === '' && $to === '') {
                return $this;
            }
            if ($to !== '') {
                $to = (float)$to;
                if ($from == $to) {
                    $to += self::MIN_POSSIBLE_PRICE;
                }
            }
            $newFrom = floor($this->_getComparingValue($from, $filter));
            $newTo = ceil($this->_getComparingValue($to, $filter));
            if ($from !== '' && $to !== '') {
                $where[] = $this->_getReadAdapter()->quoteInto("{$priceExpr} >= ?", $newFrom)
                . ' AND '
                . $this->_getReadAdapter()->quoteInto("{$priceExpr} <= ?", $newTo);
            } elseif ($from !== '') {
                $where[] = $this->_getReadAdapter()->quoteInto("{$priceExpr} >= ?", $newFrom);
            } elseif ($to !== '') {
                $where[] = $this->_getReadAdapter()->quoteInto("{$priceExpr} <= ?", $newTo);
            }
            
        }
        if (count($where)) {
            $select->where(implode(' OR ', $where));
        }
        return $this;
    
    }
    
    public function getCatalogSearchLayer()
    {
        return Mage::getSingleton('ajaxlayerednavigation/catalogSearch_layer');
    }
    
    public function getMaxValue($filter)
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch') {
            $layer = $this->getCatalogSearchLayer();
            $collection = $layer->getProductCollection();

            $select = $collection->getSelect();
            $select->limit('1000000');
            
            $result = $this->_getReadAdapter()->fetchAll($select);
            $price = array();
            foreach ($result as $value) {
                $price[] = $value['min_price'];
            }
            
            if (count($price) > 0) {
                return max($price);
            }
            return 0;
        } else {
            $select = $this->_getSelect($filter);
            $priceExpression = $this->_getFullPriceExpression($filter, $select);
            
            $select->columns(array(
                            'max_price' => new Zend_Db_Expr("MAX($priceExpression)")
            ));
            $result = $this->_getReadAdapter()->fetchCol($select);

            return $result[0];
        }
    }
    
    public function getMinValue($filter)
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch') {
            $layer = $this->getCatalogSearchLayer();
            $collection = $layer->getProductCollection();
            $select = $collection->getSelect();
            $select->limit('1000000');
            
            $result = $this->_getReadAdapter()->fetchAll($select);
            
            $price = array();
            foreach ($result as $value) {
                $price[] = $value['min_price'];
            }
            if (count($price) > 0) {
                return min($price);
            }
            return 0;
            
        } else {
            $select = $this->_getSelect($filter);
            $priceExpression = $this->_getFullPriceExpression($filter, $select);
            
            $select->columns(array(
                'min_price' => new Zend_Db_Expr("MIN($priceExpression)")
            ));
            $result = $this->_getReadAdapter()->fetchCol($select);
            return $result[0];
        }
    }
    
    public function getMaxRange($filter)
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch') {
            $layer = $this->getCatalogSearchLayer();
            $collection = $layer->getProductCollection();
        } else {
            $collection = $filter->getLayer()->getProductCollection();
        }
        
        flog($collection->getSelect()->__toString());
        
        $collection->addAttributeToSelect($filter->getRequestVar());

        $select = $collection->getSelect();
        $select->limit('1000000');

        $result = $this->_getReadAdapter()->fetchAll($select);

        $price = array();
        foreach ($result as $value) {
            $price[] = $value['min_price'];
        }

        if (count($price) > 0) {
            return max($price);
        }
        return 0;
    }
    
    public function getMinRange($filter)
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch') {
            $layer = $this->getCatalogSearchLayer();
            $collection = $layer->getProductCollection();
        } else {
            $collection = $filter->getLayer()->getProductCollection();
        }
        $select = $collection->getSelect();
        $select->limit('1000000');
        
        $result = $this->_getReadAdapter()->fetchAll($select);

        $price = array();
        foreach ($result as $value) {
            $price[] = $value['min_price'];
        }
        if (count($price) > 0) {
            return min($price);
        }
        return 0;
    }
    
    public function getDefaultRanges($categoryId)
    {
        $getRangesData = $this->_getReadAdapter()->select()
            ->from(array('alnr' => $this->getTable('ajaxlayerednavigation/range')),array('range'))
            ->where('alnr.category_id = ?', $categoryId);

        $ranges = $this->_getReadAdapter()->fetchAll($getRangesData);
        if (count($ranges) > 0 && $ranges[0]['range'] != '') {
            return explode(',', $ranges[0]['range']);
        } elseif (Mage::getStoreConfig('ajaxlayerednavigation/range/range') != "") {
            return explode(',', Mage::getStoreConfig('ajaxlayerednavigation/range/range'));
        } else {
            return null;
        }
    }
}

