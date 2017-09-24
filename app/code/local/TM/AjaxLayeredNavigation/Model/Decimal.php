<?php

class TM_AjaxLayeredNavigation_Model_Decimal extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    const MIN_RANGE_POWER = 10;

    /**
     * Resource instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
     */
    protected $_resource;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'decimal';
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
     */
    protected function _getResource()
    {
        $this->_resource = Mage::getResourceModel('ajaxlayerednavigation/decimal');

        return $this->_resource;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        parent::apply($request, $filterBlock);

        /**
         * Filter must be string: $index, $range
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            if ('' == $filter['from'] && '' == $filter['to']) {
                return $this;
            }
            $filter = $filter['from'] . ',' . $filter['to'];
        }

        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return $this;
        }

        list($from, $to) = $filter;
        if ('' !== $from && '' !== $to) {
            $this->setFrom((int)$from);
            $this->setTo((int)$to);

            $this->_getResource()->applyFilterToCollection($this, $from, $to);
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($from, $to), $filter)
            );

            $this->setData('max_value', $to);
            $this->setData('min_value', $from);

            $this->_items = array();
        }

        return $this;
    }

    /**
     * Retrieve price aggreagation data cache key
     *
     * @return string
     */
    protected function _getCacheKey()
    {
        $key = $this->getLayer()->getStateKey()
            . '_ATTR_' . $this->getAttributeModel()->getAttributeCode();
        return $key;
    }

    /**
     * Prepare text of item label
     *
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($from, $to)
    {
        //$from   = Mage::app()->getStore()->formatPrice($from, false);
        //$to     = Mage::app()->getStore()->formatPrice($to, false);
        return Mage::helper('catalog')->__('%s - %s', $from, $to);
    }

    /**
     * Retrieve maximum value from layer products set
     *
     * @return float
     */
    public function getMaxValue()
    {
        $max = $this->getData('max_value');
        if (is_null($max)) {
            list($min, $max) = $this->_getResource()->getMinMax($this);
            $this->setData('max_value', $max);
            $this->setData('min_value', $min);
        }
        return $max;
    }

    /**
     * Retrieve minimal value from layer products set
     *
     * @return float
     */
    public function getMinValue()
    {
        $min = $this->getData('min_value');
        if (is_null($min)) {
            list($min, $max) = $this->_getResource()->getMinMax($this);
            $this->setData('max_value', $max);
            $this->setData('min_value', $min);
        }
        return $min;
    }

    /**
     * Retrieve range for building filter steps
     *
     * @return int
     */
    public function getRange()
    {
        $range = $this->getData('range');
        if (!$range) {
            $maxValue = $this->getMaxValue();
            $index = 1;
            do {
                $range = pow(10, (strlen(floor($maxValue)) - $index));
                $items = $this->getRangeItemCounts($range);
                $index++;
            }
            while ($range > self::MIN_RANGE_POWER && count($items) < 2);
            $this->setData('range', $range);
        }

        return $range;
    }

    /**
     * Retrieve information about products count in range
     *
     * @param int $range
     * @return int
     */
    public function getRangeItemCounts($range)
    {
        $rangeKey = 'range_item_counts_' . $range;
        $items = $this->getData($rangeKey);
        if (is_null($items)) {
            $items = $this->_getResource()->getCount($this, $range);
            $this->setData($rangeKey, $items);
        }
        return 1;
    }

    /**
     * Retrieve data for build decimal filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data       = array();
        $range      = $this->getRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        foreach ($dbRanges as $index => $count) {
            $data[] = array(
                'label' => $this->_renderItemLabel($range, $index),
                'value' => $index . ',' . $range,
                'count' => $count,
            );
        }

        return $data;
    }

    public function getItemsCount()
    {
        return 1;
    }
}
