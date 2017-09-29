<?php
class TM_AjaxLayeredNavigation_Model_Stock
    extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    protected $_activeFilters = false;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'stock';
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $stock = array();

        $filterValues = explode('-', $filter);
        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
            $filterValues = explode(',', $filter);
        }
        $availableValues = array('in', 'out');
        foreach ($filterValues as $filterValue) {
            if (!in_array($filterValue, $availableValues)) {
                continue;
            }
            switch ($filterValue) {
                case 'in':
                    $stock[] = 1;
                    break;
                case 'out':
                    $stock[] = 0;
                    break;
                default:
                    break;
            }
        }
        $collection = $this->getLayer()->getProductCollection();
        $collection->joinField(
            'is_in_stock',
            'cataloginventory/stock_item',
            'is_in_stock',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        )->addFieldToFilter('is_in_stock', array('in' => $stock));

        foreach ($stock as $item) {
            $label = $item == 1 ? __('In Stock') : __('Out of Stock');
            $this->getLayer()->getState()->addFilter(
                $this->_createItem($label, $item == 1 ? 'in' : 'out')
            );
        }
        $this->_activeFilters = $stock;
        return $this;
    }

    protected function _getItemsData()
    {
        $currnetFilter = array();
        if ($this->isAdvancedSearchPage()) {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        } elseif (Mage::registry('query_request')) {
            $currnetFilter = Mage::registry('query_request');
        } else {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        }
        $data = array();
        $counts = $this->getCounts();
        $stockItems = array(
            'in' =>  __('In Stock'),
            'out' => __('Out of Stock')
        );
        foreach ($stockItems as $value => $label) {
            $val = $value == 'in' ? 1 : 0;
            $count = 0;
            if (array_key_exists($val, $counts)) {
                $count = $counts[$val];
            }
            $active = false;
            if ($currnetFilter && $this->_activeFilters) {
                $active = in_array($val, $this->_activeFilters);
            }

            $data[] = array(
                'label' => $label,
                'value' => $value,
                'count' => $count,
                'active' => $active,
                'display' => 1,
                'image' => null,
                'position' => 1,
                'minus' => false
            );
        }

        return $data;
    }

    public function getCounts()
    {
        if ($this->isAdvancedSearchPage()) {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        } elseif (Mage::registry('query_request')) {
            $currnetFilter = Mage::registry('query_request');
        } else {
            $currnetFilter = Mage::app()->getRequest()->getParams();
        }

        $collection = $this->getLayer()->getProductCollection();
        $collection->addAttributeToSelect('name');

        $select = clone $collection->getSelect();

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        if ($this->isCatalogPage()) {
            $select->reset(Zend_Db_Select::WHERE);
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $select->joinLeft(
            array('inv'=> $collection->getTable('cataloginventory/stock_item')),
            'inv.product_id=e.entity_id',
            array('inv.is_in_stock')
        );
        $select->where('inv.stock_id = ?', 1);
        $select->where('inv.is_in_stock in (?)', array(0, 1));

        if (array_key_exists('n', $currnetFilter)) {
            $name = $currnetFilter['n'];
            $searchItems = explode(' ', $name);
            $query = '';
            foreach ($searchItems as $searchItem) {
                $query .= "cpev.value LIKE '%" . $searchItem . "%' OR ";
            }
            $query = substr($query, 0, -3);
            $entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
            $nameAttrId = Mage::getModel('eav/entity_attribute')
                ->loadByCode($entityTypeId, 'name')
                ->getAttributeId();
            $select->join(
                array('cpev' => 'catalog_product_entity_varchar'),
                'cpev.entity_id=e.entity_id AND cpev.attribute_id='.$nameAttrId." AND (".$query.") ",
                array('name' => 'value')
            );
        }

        $select->columns(array('count' => new Zend_Db_Expr("COUNT(e.entity_id)")));
        $select->group('inv.is_in_stock');

        $selectString = strtolower($select->__toString());
        if ($this->_activeFilters) {
            $from = $select->getPart('FROM');
            $joinData = $from['at_is_in_stock'];
            $remove = strtolower(sprintf(
                "%s `%s` AS `%s` ON %s",
                $joinData['joinType'],
                $joinData['tableName'],
                'at_is_in_stock',
                $joinData['joinCondition']
            ));

            $selectString = str_replace($remove, '', $selectString);
        }

        $result = $collection->getConnection()->fetchAll($selectString);
        $counts = array();
        foreach ($result as $item) {
            $counts[$item['is_in_stock']] = $item['count'];
        }

        return $counts;
    }

    // public function getItemIsActive($activeFilter, $name, $value)
    // {
    //     if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !$this->isAdvancedSearchPage()) {

    //         foreach ($activeFilter as $key => $item) {
    //             if ($key == $name) {
    //                 $expValue = explode(',', urldecode($item));
    //                 if (in_array($seoValue, $expValue)) {
    //                     return true;
    //                 }
    //             }
    //         }
    //         return false;
    //     } else {
    //         foreach ($activeFilter as $key => $item) {
    //             if ($key == $name) {
    //                 if (is_array($item)) {
    //                     $expValue = $item;
    //                 } else {
    //                     $expValue = explode('-', $item);
    //                 }

    //                 if (in_array($value, $expValue)) {
    //                     return true;
    //                 }
    //             }
    //         }
    //         return false;
    //     }
    // }

    public function getItemsCount()
    {
        return [1];
    }

    public function getName()
    {
        return __('Stock Filter');
    }

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

    public function isAdvancedSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'advanced');
    }

    public function isCatalogPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalog'
                && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'view');
    }
}
