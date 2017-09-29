<?php
class TM_AjaxLayeredNavigation_Model_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
    protected function _createItem($label, $value, $count = 0, $active = 0)
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

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || Mage::registry('current_category_filter')) {
            return $this;
        }
    	if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')) {
    	    $seoCat = Mage::registry('seo_categories');
            $filter = array_search($filter, $seoCat);
            Mage::app()->getRequest()->setParam($this->getRequestVar(), $filter);
    	}
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
        	$catParam = Mage::app()->getRequest()->getParam($this->getRequestVar());
        	$filter = $catParam[0];
        }
        $this->_categoryId = $filter;
        $category   = $this->getCategory();
        Mage::register('current_category_filter', $category);

        $this->_appliedCategory = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($filter);
        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory);

            if (null !== Mage::registry('current_products_id')) {
                Mage::unregister('current_products_id');
            }

            $count = $this->getLayer()->getProductCollection()->getAllIds();

            Mage::register('current_products_id', $count);

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_appliedCategory->getName(), $filter)
            );
        }

        return $this;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        $var_name = $this->_requestVar;
        $oldQuery = array();
        $active = 0;
        if (Mage::registry('query_request')) {
            $oldQuery = Mage::registry('query_request');
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            $oldQuery = Mage::app()->getRequest()->getParams();
        }

        if ($data === null) {
            $categoty = $this->getCategory();
            if (array_key_exists($var_name, $oldQuery)) {
                if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
                    $seoCat = Mage::registry('seo_categories');
                    $catId = array_search($oldQuery[$var_name], $seoCat);
                    $categoty = Mage::getModel('catalog/category')->load($catId);
                } else {
                    $categoty = Mage::getModel('catalog/category')->load($oldQuery[$var_name]);
                }
            }
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $categoty->getChildrenCategories();

            $this->getLayer()->getProductCollection()
                ->addCountToCategories($categories);
            //
            $data = array();

            foreach ($categories as $category) {

                if ($category->getIsActive() && $category->getProductCount()) {
                    $count = $category->getProductCount();

                    $newCount = array();
                    if (Mage::registry('current_products_id')) {
                        $newCount = Mage::registry('current_products_id');
                    }

                    if ($count > 0) {
                        if (array_key_exists($var_name, $oldQuery)) {
                            if ($oldQuery[$var_name] == $category->getId()) {
                                $active = 1;
                            } else {
                                $active = 0;
                            }
                        }
                        $data[] = array(
                            'label' => Mage::helper('core')->htmlEscape($category->getName()),
                            'value' => $category->getId(),
                            'count' => $count,
                            'active' => $active
                        );
                    }
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    public function getUrlValue($value, $var_name)
    {
        $result = array();
        $oldQuery = array();
        $query = array();

        if (Mage::registry('query_request')) {
            $oldQuery = Mage::registry('query_request');
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            $oldQuery = Mage::app()->getRequest()->getParams();
        } else {
            $oldQuery = Mage::app()->getRequest()->getParams();
            /**
             * AttributePage fix
             */
            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }
        }

        if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled')
            && !Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            $seoCat = Mage::registry('seo_categories');
            $value = $seoCat[$value];
            /*
             * Remove SEO sufix
             */
            if (count($oldQuery)) {
                end($oldQuery);
                $key = key($oldQuery);
                reset($oldQuery);
                $oldQuery[$key] = rtrim(
                    $oldQuery[$key],
                    Mage::getStoreConfig('catalog/seo/category_url_suffix')
                );
            }
        }
        if (Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
            if (array_key_exists($var_name, $oldQuery)) {
				$oldQuery[$var_name][0] = $value;
            } else {
                $oldQuery[$var_name][] = $value;
            }

            return $oldQuery;
        } else {
            if (array_key_exists($var_name, $oldQuery)) {
                $oldQuery[$var_name] = $value;
            } else {
                $query = array(
                    $var_name => $value
                );
            }
        }

        $result = array_merge($query, $oldQuery);

        return $result;
    }

    public function getResetValue($currentValue = null, $varName = null)
    {
        $result = array();
        $oldQuery = array();

        if (Mage::registry('query_request')) {
            $oldQuery = Mage::registry('query_request');
        } else {
            $oldQuery = Mage::app()->getRequest()->getParams();
            /**
             * AttributePage fix
             */
            if (array_key_exists("id", $oldQuery)) {
                unset($oldQuery["id"]);
            }
            if (array_key_exists("parent_id", $oldQuery)) {
                unset($oldQuery["parent_id"]);
            }
        }

        if (array_key_exists($varName, $oldQuery)) {
	    if (Mage::getStoreConfig('ajaxlayerednavigation/seo/enabled') && !Mage::helper('ajaxlayerednavigation')->isAdvancedSearchPage()) {
	        $del = ',';
	    } else {
	        $del = '-';
	    }
            if (count(explode($del, $oldQuery[$varName])) > 1) {
                $oldQuery[$varName] = str_replace(array($del . $currentValue, $currentValue . $del), array('', ''), $oldQuery[$varName]);
            } else {
                $oldQuery[$varName] = null;
            }
        }
        $result = $oldQuery;

        return $result;
    }
}
