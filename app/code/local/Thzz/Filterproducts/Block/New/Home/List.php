<?php
class Thzz_Filterproducts_Block_New_Home_List extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        $products = Mage::getResourceModel('catalog/product_collection');
        $products = $this->_addProductAttributesAndPrices($products)
            ->addAttributeToSort("entity_id","DESC")
            ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds());

        $product_count = $this->getProductCount();

        if($product_count) {
            $products->setPageSize($product_count);
        }

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $this->_productCollection = $products;

        return $this->_productCollection;
    }

    public function getToolbarHtml()
    {

    }
}