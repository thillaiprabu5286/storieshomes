<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Helper_Newsletter extends Mage_Core_Helper_Data {

    public function isMageNewsletterEnabled() {
        return $this->isModuleOutputEnabled('Mage_Newsletter');
    }

    public function subscribeCustomer($data = array()) {
        Mage::getModel('newsletter/subscriber')->subscribe($data['email']);
    }

}
