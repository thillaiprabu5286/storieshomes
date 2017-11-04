<?php

class Mango_Attributemapping_Model_Observer {

    public function templateForLayeredNavigationFilter(Varien_Event_Observer $observer) {
        /* get parent->child associations from config section . done
         * check if block is a layered navigation filter block . done
         * check if attribute has a parent attribute associated in the configuration area : done
         * also check if parent attribute is set a filterable  ( needed for swatches configuration... )
         * process filters items to replace with parent filter items...
         * templates: if attributeswatches is enabled and parent attribute is set in swatches layered navigation configuration, show template for attributeswatches
         * otherwise, show template for regular attributes
         *           */
        $block = $observer->getBlock();
        if ($block instanceof Mage_Catalog_Block_Layer_View) {
            $_config = $this->_getConfig();
            foreach ($_config as $_attribute => $_parent) {
                $_filter_block = $block->getChild($_attribute . '_filter');
                if ($_filter_block) {
                    /* we need to calculate the parent filter items and then override the items list in the block template */
                    $_attribute_model = $_filter_block->getAttributeModel();
                    $_template = $block->getChild($_attribute . '_filter')->getTemplate();
                    $block->unsetChild($_attribute . '_filter');
                    $_parent_filter_block = Mage::app()->getLayout()->createBlock('attributemapping/catalog_layer_filter_parent')
                            ->setLayer($block->getLayer())
                            ->setAttributeModel($_attribute_model)
                            ->setTemplate($_template)
                            ->init();
                    $block->setChild($_attribute . '_filter', $_parent_filter_block);
                }
            }
        }
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

}
