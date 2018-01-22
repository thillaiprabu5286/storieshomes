<?php
class Thzz_Filterproducts_Block_New_Home_List extends Mage_Catalog_Block_Product_List
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
            $products->setPageSize($this->getCount());
        }

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        $products->getSelect()->joinInner(
            array('stock' => 'cataloginventory_stock_item'),
            "stock.product_id = e.entity_id",
            array('is_in_stock' => 'stock.is_in_stock')
        );

        $products->getSelect()->where('is_in_stock = 1');

        $this->_productCollection = $products;
        return $this->_productCollection;
    }

    public function getToolbarHtml()
    {

    }
}