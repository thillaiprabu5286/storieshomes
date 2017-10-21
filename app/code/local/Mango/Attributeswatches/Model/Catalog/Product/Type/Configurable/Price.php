<?php

class Mango_Attributeswatches_Model_Catalog_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price
{
    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        
        
        
        if (is_null($qty) || !Mage::getStoreConfig("attributeswatches/settings/use_child_product_price")  ){/* display product price in catalog pages */
            return parent::getFinalPrice($qty, $product);
        }
        /* added new code to get price of the child product... */
        $_child_product = Mage::getModel("catalog/product")->loadByAttribute("sku", $product->getSku() );
        if($_child_product!==null){
            $product->setFinalPrice($_child_product->getFinalPrice($qty));
        }else{
            $product->setFinalPrice(0); /* todo: return a nicer value.... */
        }
        /* eof custom code... */
        return max(0, $product->getData('final_price'));
    }
    
}
