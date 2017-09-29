<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Onestep_Form_Review_Comments_Simple extends Mage_Core_Block_Template {

    public function getComments() {
        $data = Mage::getSingleton('checkout/session')->getData('onestepcheckout_form_values');
        if (isset($data['comments'])) {
            return $data['comments'];
        }
        return '';
    }

}
