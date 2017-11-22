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
class Thzz_Filterproducts_Block_Bestsellers_Home_List extends Mage_Catalog_Block_Product_List
{

	protected function _getProductCollection()
    {
        $storeId = Mage::app()->getStore()->getId();
        $category_id = $this->getCategoryId();
        $products = Mage::getResourceModel('reports/product_collection')->addAttributeToSelect('*');
        $adapter              = $products->getConnection();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');
        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),
        );
        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', 'grouped'),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $products->getProductEntityTypeId())
        );
        $products->getSelect()->reset()
            ->from(
                array('order_items' => $products->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => $products->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array()
            )
            ->joinLeft(
                array('e' => $products->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->joinInner(
                array('stock' => 'cataloginventory_stock_item'),
                "stock.product_id = e.entity_id",
                array('is_in_stock' => 'stock.is_in_stock')
            )
            ->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
            
        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);
            $products->addCategoryFilter($category);
            $products->getSelect()->where('cat_index.position > ?', $this->getCount());
        }

        $products->setStoreId($storeId)->addStoreFilter($storeId);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $products->getSelect()->order('if(is_in_stock = 0,1,0)');
        $products->getSelect()->order('ordered_qty DESC');

        $page = ($_GET['p']) ? $_GET['p'] : 1;
        $products->setPageSize($this->getCount())->setCurPage($page);

        $this->_productCollection = $products;
        return $this->_productCollection;
    }

    /**
     * Load only 10 products for every scroll
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getMobileProductCollection()
    {
        $storeId = Mage::app()->getStore()->getId();
        $category_id = $this->getCategoryId();
        $products = Mage::getResourceModel('reports/product_collection')->addAttributeToSelect('*');
        $adapter              = $products->getConnection();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');
        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),
        );
        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', 'grouped'),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $products->getProductEntityTypeId())
        );
        $products->getSelect()->reset()
            ->from(
                array('order_items' => $products->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => $products->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            ->joinLeft(
                array('e' => $products->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->joinInner(
                array('stock' => 'cataloginventory_stock_item'),
                "stock.product_id = e.entity_id",
                array('is_in_stock' => 'stock.is_in_stock')
            )
            ->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);

        if($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);
            $products->addCategoryFilter($category);
            $products->getSelect()->where('cat_index.position > ?', 10);
        }
        $products->setStoreId($storeId)->addStoreFilter($storeId);

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
        $store = Mage::app()->getStore();
        $code  = $store->getCode();
        if(!Mage::getStoreConfig("cataloginventory/options/show_out_of_stock", $code))
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

        $products->getSelect()->order('if(is_in_stock = 0,1,0)');
        $products->getSelect()->order('ordered_qty DESC');

        $page = ($_GET['p']) ? $_GET['p'] : 1;
        $products->setPageSize(10)->setCurPage($page);

        $this->_productCollection = $products;
        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getMobileLoadedProductCollection()
    {
        return $this->_getMobileProductCollection();
    }


    public function getToolbarHtml()
    {}
}