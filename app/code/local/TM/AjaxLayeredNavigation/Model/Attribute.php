<?php
class TM_AjaxLayeredNavigation_Model_Attribute
    extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    protected function _createItem($label, $value, $count=0, $active = 0, $display=null, $image=null, $position=null, $minus=null)
    {
        return Mage::getModel('ajaxlayerednavigation/item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($active)
            ->setDisplay($display)
            ->setImage($image)
            ->setMinus($minus)
            ->setPosition($position);
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
                $itemData['active'],
                $itemData['display'],
                $itemData['image'],
                $itemData['position'],
                $itemData['minus']
            );
        }
        $this->_items = $items;
        return $this;
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('ajaxlayerednavigation/attribute');
        }
        return $this->_resource;
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param   int $optionId
     * @return  unknown
     */
    protected function _getOptionText($optionId)
    {
        return $this->getAttributeModel()->getFrontend()->getOption($optionId);
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $calculatedAttributes = Mage::registry('ajaxlayerednav/calculated_attributes');
        if (!$calculatedAttributes) {
            $calculatedAttributes = array();
            Mage::register('ajaxlayerednav/calculated_attributes', $calculatedAttributes);
        }
        if (isset($calculatedAttributes[$this->getAttributeModel()->getAttributeCode()])) {
            return $this;
        }
        $calculatedAttributes[$this->getAttributeModel()->getAttributeCode()] = 1;
        Mage::unregister('ajaxlayerednav/calculated_attributes');
        Mage::register('ajaxlayerednav/calculated_attributes', $calculatedAttributes);

        $filter = $request->getParam($this->_requestVar);
        $mageSuffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        if (null !== $filter) {
            $filter = str_replace($mageSuffix, '', $filter);
        } else {
            return $this;
        }

        $this->setData('request', $filter);

        if ($this->isAdvancedSearchPage()) {
            $expFilter = $filter;
        } else {
            $expFilter = explode('-', $filter);
        }

        if (!$filter) {
            return $this;
        }
        if (is_array($filter)) {
            $expFilter = $filter;
        }
        $attribute = $this->getAttributeModel();
        $options = $attribute->getFrontend()->getSelectOptions();
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
            $seoOptions = Mage::registry('seo_options');
            $seoFilter = explode(',', $filter);
            $seoFilters = array();
            foreach ($seoFilter as $newFilter) {
                foreach ($seoOptions as $id=>$label) {
                    if (urldecode($newFilter) == $label) {
                        $seoFilters[] = $id;
                    }
                }
            }
            $filter = implode(',', $seoFilters);
            $expFilter = explode(',', $filter);
        }
        if ($this->isAdvancedSearchPage()) {
            $expFilter = (array)$filter;
        }

        $newExp = array();
        foreach($expFilter as $checkFilter) {
            if ("" == $checkFilter) { continue; }
            foreach($options as $option) {
                if ($checkFilter == $option['value']) {
                    $newExp[] = $checkFilter;
                }
            }
        }
        $expFilter = $newExp;
        $this->setData('current_filter' , $expFilter);
        if (count($expFilter) > 0) {
            $this->_getResource()->applyFilterToCollection($this, $expFilter);
            foreach ($expFilter as $filters) {
                $text = $this->_getOptionText($filters);

                if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
                    if (array_key_exists($filters, $seoOptions)) {
                        $filters = $seoOptions[$filters];
                    }
                }

                if ($text != '' || !is_null($text)) {
                    $this->getLayer()->getState()->addFilter(
                        $this->_createItem($text, $filters)
                    );
                }
            }
        }

        return $this;
    }

    /**
     * Check whether specified attribute can be used in LN
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterable();
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();

        $this->_requestVar = $attribute->getAttributeCode();

        $currnetFilter = array();
        if ($this->isAdvancedSearchPage()) {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        } elseif (Mage::registry('query_request')) {
            $currnetFilter = Mage::registry('query_request');
        } else {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        }
        $key = $this->getLayer()->getStateKey().'_'.$this->_requestVar;
        $data = null;
        if ($data === null) {
            $options      = $attribute->getFrontend()->getSelectOptions();
            $optionsCount = $this->_getResource()->getCount($this);
            $display      = $this->_getResource()->getAttributeDisplayType($attribute->getId());
            $image        = $this->_getResource()->getOptionImage($attribute->getId());
            $sort         = $this->_getResource()->getOptionPosition($attribute->getId());
            $data         = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }

                if ($currnetFilter) {
                    $active = $this->getItemIsActive($currnetFilter, $this->_requestVar, $option['value']);
                } else {
                    $active = false;
                }
                $sorter = null;

                $position = null;
                if (null !== $image) {
                    if (array_key_exists($option['value'], $image)) {
                        $optionImg = $image[$option['value']];
                    } else {
                        $optionImg = null;
                    }
                } else {
                    $optionImg = null;
                }
                if (null !== $sort) {
                    if (array_key_exists('position', $sort)) {
                        if (array_key_exists($option['value'], $sort['position'])) {
                            $position = $sort['position'][$option['value']];
                        } else {
                            $position = 0;
                        }

                        $sorter = 1;
                        $orderBy = $sort['order'];
                    } else {
                        $sorter  = $sort['sort'];
                        $orderBy = $sort['order'];
                    }
                }

                if (array_key_exists($option['value'], $optionsCount) && $optionsCount[$option['value']] > 0 && !$active) {
                    $optCount = $optionsCount[$option['value']];

                    if (is_array($optCount)) {
                        if (array_key_exists("exist", $optCount)) {
                            $minus = true;
                            $count = $optCount["exist"];
                        } else {
                            $minus = false;
                            $count = $optCount["plus"];
                        }
                    } else {
                        $minus = false;
                        $count = $optionsCount[$option['value']];
                    }
                    $data[] = array(
                        'label'   => $option['label'],
                        'value'   => $option['value'],
                        'count'   => $count,
                        'active'  => $active,
                        'display' => $display,
                        'minus'   => $minus,
                        'image'   => $optionImg,
                        'position' => $position
                    );
                } elseif ($active) {
                    if (!array_key_exists($option['value'], $optionsCount)) {
                        $count = 0;
                    } else {
                        $count = $optionsCount[$option['value']];
                    }

                    $data[] = array(
                        'label'   => $option['label'],
                        'value'   => $option['value'],
                        'count'   => $count,
                        'minus'   => false,
                        'active'  => $active,
                        'display' => $display,
                        'image'   => $optionImg,
                        'position' => $position
                    );
                }
            } //end foreach
            /*  Sort Array    */
            if (null !== $sorter) {
                if ((int)$sorter == 1) {
                    if ((int)$orderBy == 1) {
                        $order = SORT_ASC;
                    } else {
                        $order = SORT_DESC;
                    }

                    $data = $this->arraySort($data, 'position', $order);
                } elseif ((int)$sorter == 2) {
                    if ((int)$orderBy == 1) {
                        $order = SORT_ASC;
                    } else {
                        $order = SORT_DESC;
                    }

                    $data = $this->arraySort($data, 'count', $order);
                } elseif ((int)$sorter == 3) {
                    if ((int)$orderBy == 1) {
                        $order = SORT_ASC;
                    } else {
                        $order = SORT_DESC;
                    }

                    $data = $this->arraySort($data, 'label', $order);
                }
            }
            if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')
                && !$this->isAdvancedSearchPage()) {
                $data = $this->seoLabelsUrl($data);
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
            );

            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;
    }

    public function seoLabelsUrl($data)
    {
        $result = array();
        $seoLabel = Mage::registry('seo_options');

        foreach($data as $id => $value) {
            if (array_key_exists($value['value'], $seoLabel)) {
                $result[] = array(
                        'label'   => $value['label'],
                        'value'   => $seoLabel[$value['value']],
                        'count'   => $value['count'],
                        'active'  => $value['active'],
                        'display' => $value['display'],
                        "minus"  => $value['minus'],
                        'image'   => $value['image'],
                        'position' => $value['position']
                );
            }

        }
        return $result;
    }

    public function getItemIsActive($activeFilter, $name, $value)
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
            $seoOptions = Mage::registry('seo_options');
            if (array_key_exists($value, $seoOptions)) {
                $seoValue = $seoOptions[$value];
            } else {
                $seoValue = null;
            }
            foreach ($activeFilter as $key => $item) {
                if ($key == $name) {
                    $expValue = explode(',', urldecode($item));
                    if (in_array($seoValue, $expValue)) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            foreach ($activeFilter as $key => $item) {
                if ($key == $name) {
                    if (is_array($item)) {
                        $expValue = $item;
                    } else {
                        $expValue = explode('-', $item);
                    }

                    if (in_array($value, $expValue)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    public function arraySort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                break;
                case SORT_DESC:
                    arsort($sortable_array);
                break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public function getUrlValue($value, $var_name)
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
            $result = array();
            $oldQuery = array();
            $query = array();

            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            } else {
                $oldQuery = Mage::app()->getRequest()->getParams();
            }
            /*
             * Remove SEO sufix
             */
            if (count($oldQuery)) {
                end($oldQuery);
                $key = key($oldQuery);
                reset($oldQuery);
                $mageUrlSuf = Mage::getStoreConfig('catalog/seo/category_url_suffix');
                if (strlen($mageUrlSuf)) {
                    if (substr($oldQuery[$key], -strlen($mageUrlSuf)) == $mageUrlSuf) {
                        $oldQuery[$key] = substr($oldQuery[$key], 0, -strlen($mageUrlSuf));
                    }
                }
            }
            /**
             * AttributePage fix
             */
            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }

            if (array_key_exists($var_name, $oldQuery)) {
                $delimiter = TM_AjaxLayeredNavigation_Model_Item::getDelimiter();
                $oldParams = explode($delimiter, $oldQuery[$var_name]);
                $oldParams[] = $value;
                sort($oldParams);
                $oldQuery[$var_name] = implode($delimiter, $oldParams);
            } else {
                $query = array(
                    $var_name => $value
                );
            }

            $result = array_merge($query, $oldQuery);
            ksort($result);
            return $result;
        } else {
            $result = array();
            $query = array();
            $oldQuery = array();
            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            } else {
                $oldQuery = Mage::app()->getRequest()->getParams();
            }

            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }
            if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
                $oldQuery = Mage::app()->getRequest()->getParams();
                $oldQuery[$var_name][] = $value;

                return $oldQuery;
            } else {
                if (array_key_exists($var_name, $oldQuery)) {
                    $oldQuery[$var_name] .= '-' . $value;
                } else {
                    $query = array(
                        $var_name => $value
                    );
                }
            }

            $result = array_merge($query, $oldQuery);

            ksort($result);
            return $result;
        }
    }

    public function getResetValue($currentValue=null, $varName=null)
    {
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {
            $result = array();
            $oldQuery = array();

            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            } else {
                $oldQuery = Mage::app()->getRequest()->getParams();
            }
            /**
             * AttributePage fix
             */
            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }

            $currentValue = str_replace(' ', '%20', $currentValue);
            if (array_key_exists($varName, $oldQuery)) {
                if (count(explode(',', $oldQuery[$varName])) > 1) {
                    $oldQuery[$varName] = str_replace(array(',' . $currentValue, $currentValue . ','), '', $oldQuery[$varName]);
                } else {
                    $oldQuery[$varName] = null;
                }
            } else {
                $oldQuery[$varName] = null;
                //unset($oldQuery[$varName]);
            }
            $result = $oldQuery;
            ksort($result);
            return $result;
        } else {
            $result = array();
            $oldQuery = array();

            if (Mage::registry('query_request')) {
                $oldQuery = Mage::registry('query_request');
            } elseif (Mage::registry('root_request')) {
                $oldQuery = Mage::registry('root_request');
            } else {
                $oldQuery = Mage::app()->getRequest()->getParams();
            }
            /**
             * AttributePage fix
             */
            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }
            if ($this->isAdvancedSearchPage()) {
                $oldQuery = Mage::app()->getRequest()->getParams();

                if (array_key_exists($varName, $oldQuery)) {
                    if(in_array($currentValue, $oldQuery[$varName])) {
                        $currentIndex = array_search($currentValue, $oldQuery[$varName]);
                        if (count($oldQuery[$varName]) > 1) {
                            unset($oldQuery[$varName][$currentIndex]);
                        } else {
                            unset($oldQuery[$varName]);
                        }

                    }
                }

                $result = $oldQuery;
                ksort($result);

                return $result;
            }

            if (array_key_exists($varName, $oldQuery)) {
                if (count(explode('-', $oldQuery[$varName])) > 1) {
                    $oldQuery[$varName] = str_replace(array('-' . $currentValue, $currentValue . '-'), array('', ''), $oldQuery[$varName]);
                } else {
                    $oldQuery[$varName] = null;
                }
            }
            $result = $oldQuery;
            ksort($result);
            return $result;
        }
    }

    public function getItemsCount()
    {
        return count($this->getItems());
    }

    public function isAdvancedSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'advanced');
    }
}
