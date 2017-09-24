<?php
class TM_AjaxLayeredNavigation_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{
    /**
     * Get attribute filter block name
     *
     * @return string
     */
    protected function _getAttributeFilterBlockName()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::_getAttributeFilterBlockName();
        }
        return 'ajaxlayerednavigation/layer_filter_attribute';
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    protected function _prepareLayout()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::_prepareLayout();
        }
        $stateBlock = $this->getLayout()->createBlock('ajaxlayerednavigation/layer_state')
            ->setLayer($this->getLayer());

        $this->setChild('layer_state', $stateBlock);

        $categryBlock = $this->getLayout()->createBlock('ajaxlayerednavigation/layer_filter_category')
            ->setLayer($this->getLayer())
            ->init();
        $this->setChild('ajaxlayerednavigation_filter', $categryBlock);

        /*
        * Filter by name
         */
        if ($this->canShowFilterByName()) {
            $filterByNameBlock = $this->getLayout()->createBlock('ajaxlayerednavigation/layer_filter_name')
                ->setLayer($this->getLayer())
                ->init();
            $this->setChild('ajaxlayerednavigation_name_filter', $filterByNameBlock);
        }

        /*
        * Filter by stock
         */
        if ($this->canShowFilterByStock()) {
            $filterByStockBlock = $this->getLayout()->createBlock('ajaxlayerednavigation/layer_filter_stock')
                ->setLayer($this->getLayer())
                ->init();
            $this->setChild('ajaxlayerednavigation_stock_filter', $filterByStockBlock);
        }

        $filterableAttributes = $this->_getFilterableAttributes();
        foreach ($filterableAttributes as $attribute) {
            $filterBlockName = $this->_getAttributeFilterBlockName();

            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = 'ajaxlayerednavigation/layer_filter_price';
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = 'ajaxlayerednavigation/layer_filter_decimal';
            }

            $this->setChild($attribute->getAttributeCode().'_filter',
                $this->getLayout()->createBlock($filterBlockName)
                    ->setLayer($this->getLayer())
                    ->setAttributeModel($attribute)
                    ->init());
        }

        $this->getLayer()->apply();
        return $this;
    }

    /**
     * Get all fiterable attributes of current category
     *
     * @return array
     */
    protected function _getFilterableAttributes()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::_getFilterableAttributes();
        }
        $attributes = $this->getData('_filterable_attributes');
        if (is_null($attributes)) {
            $attributes = $this->getLayer()->getFilterableAttributes();
            $this->setData('_filterable_attributes', $attributes);
        }

        return $attributes;
    }

    /**
     * Get layered navigation state html
     *
     * @return string
     */
    public function getStateHtml()
    {
        return $this->getChildHtml('layer_state');
    }

    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = array();

        if (!Mage::getStoreConfig('ajaxlayerednavigation/category_sort/enabled')) {
            if ($categoryFilter = $this->_getCategoryFilter()) {
                if (!Mage::getStoreConfig('ajaxlayerednavigation/general/hide_category')) {
                    $filters[] = $categoryFilter;
                }
            }
        }

        if ($nameFilter = $this->_getNameFilter()) {
            $filters[] = $nameFilter;
        }

        if ($stockFilter = $this->_getStockFilter()) {
            $filters[] = $stockFilter;
        }

        $configurableSwatches = Mage::helper('core')->isModuleEnabled('Mage_ConfigurableSwatches');

        $filterableAttributes = $this->_getFilterableAttributes();
        $prev = 0;
        $categoryAdd = false;
        $prev = false;
        foreach ($filterableAttributes as $attribute) {
            if (Mage::getStoreConfig('ajaxlayerednavigation/category_sort/enabled')) {
                if ($categoryAdd) {
                    if ($configurableSwatches && Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                        $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter')
                            ->setTemplate('tm/ajaxlayerednavigation/layer/swatches.phtml');
                    } else {
                        $filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
                    }
                } else {
                    if ($this->checkCategoryFilterPosition($attribute->getFPosition(), $prev)) {
                        $categoryAdd = true;
                        if ($categoryFilter = $this->_getCategoryFilter()) {
                            if (!Mage::getStoreConfig('ajaxlayerednavigation/general/hide_category')) {
                                $filters[] = $categoryFilter;
                            }
                        }
                        if ($configurableSwatches && Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                            $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter')
                                ->setTemplate('tm/ajaxlayerednavigation/layer/swatches.phtml');
                        } else {
                            $filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
                        }
                    } else {
                        if ($configurableSwatches && Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                            $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter')
                                ->setTemplate('tm/ajaxlayerednavigation/layer/swatches.phtml');
                        } else {
                            $filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
                        }
                    }
                }
                $prev = $attribute->getFPosition();
            } else {
                if ($configurableSwatches && Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                    $filters[] = $this->getChild($attribute->getAttributeCode() . '_filter')
                        ->setTemplate('tm/ajaxlayerednavigation/layer/swatches.phtml');
                } else {
                    $filters[] = $this->getChild($attribute->getAttributeCode().'_filter');
                }
            }
        }

        if (Mage::getStoreConfig('ajaxlayerednavigation/category_sort/enabled')) {
            if (!$categoryAdd) {
                if ($categoryFilter = $this->_getCategoryFilter()) {
                    if (!Mage::getStoreConfig('ajaxlayerednavigation/general/hide_category')) {
                        $filters[] = $categoryFilter;
                    }
                }
            }
        }

        return $filters;
    }

    public function checkCategoryFilterPosition($current, $prev)
    {
        $catPosition = Mage::getStoreConfig('ajaxlayerednavigation/category_sort/sort_order');

        if (!$prev) {
            if ($catPosition < $current) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($catPosition >= $prev && $catPosition <= $current) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get category filter block
     *
     * @return Mage_Catalog_Block_Layer_Filter_Category
     */
    protected function _getCategoryFilter()
    {
        if (!Mage::getStoreConfig('ajaxlayerednavigation/general/enabled')) {
            return parent::_getCategoryFilter();
        }
        return $this->getChild('ajaxlayerednavigation_filter');
    }

    protected function _getNameFilter()
    {
        return $this->getChild('ajaxlayerednavigation_name_filter');
    }

    protected function _getStockFilter()
    {
        return $this->getChild('ajaxlayerednavigation_stock_filter');
    }

    /**
     * Check availability display layer options
     *
     * @return bool
     */
    public function canShowOptions()
    {
        foreach ($this->getFilters() as $filter) {
            if ($filter->getItemsCount()) {
                return true;
    }
        }
        return false;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->canShowOptions() || count($this->getLayer()->getState()->getFilters());
    }

    /**
     * Retrieve Price Filter block
     *
     * @return Mage_Catalog_Block_Layer_Filter_Price
     */
    protected function _getPriceFilter()
    {
        return $this->getChild('_price_filter');
    }

    public function canShowFilterByName()
    {
        $byNameEnable =Mage::getStoreConfig('ajaxlayerednavigation/additional_filters/by_name');

        return $this->isCatalogPage() && $byNameEnable;
    }

    public function canShowFilterByStock()
    {
        $stockEnabled = Mage::getStoreConfig('ajaxlayerednavigation/additional_filters/stock');
        $outProductsInCatalog = Mage::getStoreConfig('cataloginventory/options/show_out_of_stock');

        return $stockEnabled && $outProductsInCatalog;
    }

    public function isCatalogPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalog'
            && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'category');
    }
}