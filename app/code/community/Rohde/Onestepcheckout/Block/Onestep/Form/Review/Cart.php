<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Onestep_Form_Review_Cart extends Mage_Checkout_Block_Onepage_Review_Info {

    public function getUrlToUpdateBlocksAfterACP() {
        return Mage::getUrl('onestepcheckout/ajax/updateBlocksAfterACP', array('_secure' => true));
    }

    public function isCartEditable() {
        return Mage::helper('onestepcheckout/config')->getIsCartEditable();
    }

}
