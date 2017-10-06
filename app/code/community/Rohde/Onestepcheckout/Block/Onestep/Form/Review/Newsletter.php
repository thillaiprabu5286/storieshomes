<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Onestep_Form_Review_Newsletter extends Mage_Checkout_Block_Onepage_Abstract {

    public function canShow() {
        if (!Mage::helper('onestepcheckout/config')->isNewsletter()) {
            return false;
        }
        return true;
    }

}
