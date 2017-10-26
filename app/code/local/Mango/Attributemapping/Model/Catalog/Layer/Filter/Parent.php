<?php

/**
 * Layer attribute filter
 *
 * @category   Mango
 * @package    Mango_Attributemapping
 */
class Mango_Attributemapping_Model_Catalog_Layer_Filter_Parent extends Mage_Catalog_Model_Layer_Filter_Attribute {

    const OPTIONS_ONLY_WITH_RESULTS = 1;

    /**
     * Resource instance
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    protected $_parent_attribute_code;
    protected $_parent_attribute;
    protected $_selected_values = array();

    /**
     * Get request variable name which is used for apply filter
     *
     * @return string
     */
    public function getRequestVar() {
        return $this->_parent_attribute_code;
    }

    /**
     * Set attribute model to filter
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function setAttributeModel($attribute) {
        $this->setData('attribute_model', $attribute);
        /* will get parent data and set other variables.. */
        $_attribute_code = $attribute->getAttributeCode();
        $_info = $this->_getConfig();
        if (isset($_info[$_attribute_code])) {
            $this->_parent_attribute_code = $_info[$_attribute_code];
            $this->setRequestVar($this->_parent_attribute_code);
        }
        return $this;
    }

    protected function _getParent() {
        return $this->_parent_attribute_code;
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
    protected function _getResource() {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('attributemapping/catalog_layer_filter_parent');
        }
        return $this->_resource;
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param   int $optionId
     * @return  string|bool
     */
    protected function _getOptionText($optionIds) {
        $_parent = $this->getParentAttribute();
        $_optionText = array();
        foreach ($optionIds as $optionId) {
            $_optionText[$optionId] = $_parent->getFrontend()->getOption($optionId);
        }
        return $_optionText;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $_url_param = $request->getParam($this->_parent_attribute_code);
        $this->_selected_values = array();
        if (preg_match('/^[0-9,]+$/', $_url_param)) {
            $this->_selected_values = explode(',', $_url_param);
        } elseif ((int) $_url_param > 0) {
            $this->_selected_values[] = $_url_param;
        }
        if (!is_array($this->_selected_values) || count($this->_selected_values) == 0) {
            return $this;
        }
        $text = $this->_getOptionText($this->_selected_values);
        if ($this->_selected_values && count($text)) {
            $this->_getResource()->applyFilterToCollection($this, $this->_selected_values);
            foreach ($this->_selected_values as $option) {
                $this->getLayer()->getState()->addFilter($this->_createItem($text[$option], $option));
            }
            if (!Mage::getStoreConfig("attributemapping/settings/multiple_selection")) {
                $this->_items = array();
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
    protected function _getIsFilterableAttribute($attribute) {
        return $attribute->getIsFilterable();
    }

    /**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems() {
        $data = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $items[] = $this->_createParentItem(
                    $itemData['label'], $itemData['value'], $itemData['url_param_value'], /* added for multiselection of values.. */ $itemData['count']
            );
        }
        $this->_items = $items;
        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        $key = $this->getLayer()->getStateKey() . '_' . $this->_requestVar;
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        if ($data === null) {
            $_parent_attribute = $this->getParentAttribute();
            $options = $_parent_attribute->getSource()->getAllOptions();
            $optionsCount = $this->_getResource()->getParentCount($this);
            $data = array();
            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                $_value = "";
                $_values = $this->_selected_values;
                if (in_array($option["value"], $this->_selected_values)) {
                    $_values = array_diff($this->_selected_values, array($option["value"]));
                } else {
                    $_values[] = $option["value"];
                }
                $_value = join(",", $_values);
                if (Mage::helper('core/string')->strlen($option['value'])) {
                    // Check filter type
                    if ($this->_getIsFilterableAttribute($attribute) == self::OPTIONS_ONLY_WITH_RESULTS) {
                        if (!empty($optionsCount[$option['value']])) {
                            $data[] = array(
                                'label' => $option['label'],
                                'value' => $option['value'],
                                'url_param_value' => $_value,
                                'count' => $optionsCount[$option['value']],
                            );
                        }
                    } else {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $option['value'],
                            'url_param_value' => $_value,
                            'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                        );
                    }
                }
            }
            /* process data to get parent items */
            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createParentItem($label, $value, $url_param_value, $count = 0) {
        return Mage::getModel('attributemapping/catalog_layer_filter_item')
                        ->setFilter($this)
                        ->setLabel($label)
                        ->setUrlParamValue($url_param_value)
                        ->setValue($value)
                        ->setCount($count);
    }

    protected function _getConfig($_parent_first = false) {
        $_config = explode("\n", Mage::getStoreConfig("attributemapping/settings/attributes"));
        $_pairs = array();
        foreach ($_config as $_string) {
            $_pair = explode(",", $_string);
            if (count($_pair) === 2) {
                if ($_parent_first) {
                    $_pairs[$_pair[0]] = $_pair[1];
                } else {
                    $_pairs[$_pair[1]] = $_pair[0];
                }
                $_pairs[$_pair[1]] = $_pair[0];
            }
        }
        return $_pairs;
    }

    public function getParentAttribute() {
        if (!$this->_parent_attribute) {
            $_attribute_code = $this->_getParent();
            $this->_parent_attribute = Mage::getSingleton("eav/config")->getAttribute('catalog_product', $_attribute_code);
        }
        return $this->_parent_attribute;
    }

}
