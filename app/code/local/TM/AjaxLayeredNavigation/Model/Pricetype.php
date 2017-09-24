<?php

class TM_AjaxLayeredNavigation_Model_Pricetype extends Mage_Catalog_Model_Layer_Filter_Price
{
	public function __construct()
	{
		parent::__construct();
		$this->_requestVar = 'test';
	}
	
    protected function _createItem($label, $value, $count=0, $active=0)
    {
        return Mage::getModel('ajaxlayerednavigation/item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($active);
    }
    
    
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active']
            );
        }
        $this->_items = $items;
        return $this;
    }
    
    protected function _getResource()
    {
        return $this->_resource = Mage::getResourceModel('ajaxlayerednavigation/pricetype');
    }
    
    /**
     * Get data for build price filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $data = array();
        $category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        $prices = $this->_getResource()->getDefaultRanges($category->getId());
        $oldQuery = array();    
        if (null !== $prices) {
            $oldQuery = array();
            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            }

            $query = array();
            if (array_key_exists($this->_requestVar, $oldQuery)) {
                $query = explode('-', $oldQuery[$this->_requestVar]);
            }
            for($i = 0; $i < count($prices); $i++) {
                $fromPrice = $prices[$i];
                if (array_key_exists($i + 1, $prices)) {
                    $toPrice = $prices[$i + 1];
                } else {
                    $toPrice = '';
                }
                
                if (count($oldQuery) > 0) {
                    $count = $this->_getResource()->getRangeCount($this, $fromPrice, $toPrice, $this->_requestVar);
                } else {
                    $count = $this->_getResource()->getNotApplyRangeCount($this, $fromPrice, $toPrice);
                }
                
                
                
                $active = false;

                if (in_array($fromPrice . ',' . $toPrice, $query)) {
                    $active = true;
                }
                $data[] = array(
                    'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                    'value' => $fromPrice . ',' . $toPrice,
                    'count' => $count,
                    'active' => $active
                );
            }
        } else {
            if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
                return $this->_getCalculatedItemsData();
            }

            $range = $this->getPriceRange();
            $dbRanges = $this->getRangeItemCounts($range);

            if (!empty($dbRanges)) {
                $lastIndex = array_keys($dbRanges);
                $lastIndex = $lastIndex[count($lastIndex) - 1];

                $oldQuery = array();
                if (Mage::registry('query_request')) {
                    $oldQuery = Mage::registry('query_request');
                } elseif (Mage::registry('root_request')) {
                    $oldQuery = Mage::registry('root_request');
                }

                $query = array();
                if (array_key_exists($this->_requestVar, $oldQuery)) {
                    $query = explode('-', $oldQuery[$this->_requestVar]);
                }

                foreach ($dbRanges as $index => $count) {
                    $fromPrice = ($index == 1) ? '' : (($index - 1) * $range);
                    $toPrice = ($index == $lastIndex) ? '' : ($index * $range);
                    if (array_key_exists($this->_requestVar, $oldQuery)) {
                        $count = $this->_getResource()->getActiveCount($this, $fromPrice, $toPrice);
                    }

                    $active = false;
                    $value = explode(' - ',$index);

                    if (in_array($fromPrice . ',' . $toPrice, $query)) {
                        $active = true;
                    }
                    $data[] = array(
                        'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                        'value' => $fromPrice . ',' . $toPrice,
                        'count' => $count,
                        'active' => $active
                    );
                }
            }
        }
        

        return $data;
    }
    
    protected function _validateFilter($filter)
    {
        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return false;
        }
        foreach ($filter as $v) {
            if (($v !== '' && $v !== '0' && (float)$v <= 0) || is_infinite((float)$v)) {
                return false;
            }
        }
    
        return $filter;
    }
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
    	
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode('-', $filter);
        $interval = array();
        foreach ($filterParams as $priceParam) {
            $filter = $this->_validateFilter($priceParam);
            if (!$filter) {
                return $this;
            }
            
            list($from, $to) = $filter;

            if ($this->checkStateFilter($from, $to)) {
                return $this;
            }
            $interval[] = array($from, $to);

            $priorFilters = array();
            for ($i = 0; $i < count($filterParams); ++$i) {
                $priorFilter = $this->_validateFilter($filterParams[$i]);
                
                if ($priorFilter) {
                    $priorFilters[] = $filterParams[$i];
                } else {
                    //not valid data
                    $priorFilters = array();
                    break;
                }
            }

            $this->getLayer()->getState()->addFilter($this->_createItem(
                $this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
                $filter
            ));
        }
        

        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }
        $this->setInterval($interval);
        $this->_applyPriceRange();
        return $this;
    }

    public function checkStateFilter($from, $to)
    {
        $items = $this->getLayer()->getState()->getFilters();
        foreach ($items as $item) {
            $value = $item->getValue();
            $fromV = $value[0];
            $toV = $value[1];
            if($from == $fromV && $to == $toV) {
                return true;
            }
        }
        return false;
    }

    public function getUrlValue($value, $var_name)
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            $result = array();
            $oldQuery = array();
            $query = array();

            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            }
            
            if (array_key_exists($var_name, $oldQuery)) {
                $delimiter = '-';
                $oldParams = explode($delimiter, $oldQuery[$var_name]);
                $oldParams[] = $value;
                sort($oldParams);
                $oldQuery[$var_name] = implode($delimiter, $oldParams);
            } else {
                $query = array(
                    $var_name => $value,
                    Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls,
                );
            }
            
            $result = array_merge($query, $oldQuery);
            ksort($result);
            return $result;
        } else {
            $result = array();
            $oldQuery = array();
            $query = array();
        
            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            }
        
            if (array_key_exists($var_name, $oldQuery)) {
                $oldQuery[$var_name] .= '-' . $value;
            }
            
            $query = array(
                $var_name => $value,
            );
            
            $result = array_merge($query, $oldQuery);
            
            return $result;
        }
    }
    
    public function getResetValue($currentValue=null, $varName=null)
    {
        $result = array();
        $oldQuery = array();
    
        if (Mage::registry('query_request')) {
            $oldQuery = Mage::registry('query_request');
        }
    
        if (array_key_exists($varName, $oldQuery)) {
            if (count(explode('-', $oldQuery[$varName])) > 1) {
                if (is_array($currentValue)) {
                    $currentValueNew = implode(',', $currentValue);
                } else {
                    $currentValueNew = $currentValue;
                }

                $res = str_replace(
                    array($currentValueNew . '-', '-' . $currentValueNew), 
                    '', 
                    $oldQuery[$varName]
                );
                $oldQuery[$varName] = $res;
            } else {
                $oldQuery[$varName] = null;
            }
        }
        $result = $oldQuery;

        return $result;
    }
    
    public function getMaxValueInt()
    {
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $maxPrice = $this->_getResource()->getMaxValue($this);
            $maxPrice = ceil($maxPrice);
            $this->setData('max_price_int', $maxPrice);
        }
        
        return $maxPrice;
    }
    
    public function getMinValueInt()
    {
        $minPrice = $this->getData('min_price_int');
        if (is_null($minPrice)) {
            $minPrice = $this->_getResource()->getValuePrice($this);
            $minPrice = floor($minPrice);
            $this->setData('min_price_int', $minPrice);
        }
        
        return $minPrice;
    }
    
    public function getMaxRangeInt()
    {
//         $oldQuery = array();
//         if (Mage::registry('query_request')) {
//             $oldQuery = Mage::registry('query_request');
//         } elseif (Mage::registry('root_request')) {
//             $oldQuery = Mage::registry('root_request');
//         }
        
//         $query = array();
//         if (count($oldQuery) == 0) {
//             return $this->getMaxPriceInt();
//         }
        $maxPrice = $this->getData('max_range_int');
        if (is_null($maxPrice)) {
            $maxPrice = $this->_getResource()->getMaxRange($this);
            
            $maxPrice = ceil($maxPrice);
            $this->setData('max_range_int', $maxPrice);
        }

        return $maxPrice;
    }
    
    public function getMinRangeInt()
    {
//         $oldQuery = array();
//         if (Mage::registry('query_request')) {
//             $oldQuery = Mage::registry('query_request');
//         } elseif (Mage::registry('root_request')) {
//             $oldQuery = Mage::registry('root_request');
//         }
        
//         $query = array();
//         if (count($oldQuery) == 0) {
//             return $this->getMinPriceInt();
//         } 
        $minPrice = $this->getData('min_range_int');
        if (is_null($minPrice)) {
            $minPrice = $this->_getResource()->getMinRange($this);
            $minPrice = floor($minPrice);
            $this->setData('min_range_int', $minPrice);
        }
        
        return $minPrice;
    }

    public function getItemsCount()
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/range/slider')) {
            return 1;
        }
    
        return count($this->getItems());
    }
}
