<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Onestep_Title extends Mage_Checkout_Block_Onepage_Abstract {

    public function getTitle() {
        $helper = Mage::helper('onestepcheckout/config');
        return $this->escapeHtml($helper->getCheckoutTitle());
    }

    public function getDescription() {
        $helper = Mage::helper('onestepcheckout/config');
        return $helper->getCheckoutDescription();
    }

}
