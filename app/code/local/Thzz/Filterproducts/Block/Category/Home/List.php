<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of List
 *
 * @author om
 */
class Thzz_Filterproducts_Block_Category_Home_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        $storeId = Mage::app()->getStore()->getId();

        $products = Mage::getResourceModel('catalog/product_collection');
        $categoryId = $this->getCategoryId();
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $products = $this->_addProductAttributesAndPrices($products)
                ->addCategoryFilter($category)
                ->setStoreId($storeId);
            $products->addAttributeToSort('position', 'ASC');
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $store = Mage::app()->getStore();
        $code = $store->getCode();
        if (!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        if ($this->getProductCount()) {
            $products->setPageSize($this->getProductCount());
        }

        $this->_productCollection = $products;
        return $this->_productCollection;
    }

    public function getToolbarHtml()
    {}
}