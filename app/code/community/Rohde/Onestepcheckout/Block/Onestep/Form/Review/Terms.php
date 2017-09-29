<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Onestep_Form_Review_Terms extends Mage_Checkout_Block_Agreements {

    public function canShow() {
        if (count($this->getAgreements()) === 0) {
            return false;
        }
        return true;
    }

}
