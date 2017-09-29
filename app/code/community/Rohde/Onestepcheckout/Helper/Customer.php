<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Helper_Customer extends Mage_Core_Helper_Abstract {

    //TODO: check on 1.5 and 1.6
    public function sendForgotPasswordForCustomer(Mage_Customer_Model_Customer $customer) {
        if (method_exists(Mage::helper('customer'), 'generateResetPasswordLinkToken')) {
            $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
            $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
            $customer->sendPasswordResetConfirmationEmail();
        } else {
            $newPassword = $customer->generatePassword();
            $customer->changePassword($newPassword, false);
            $customer->sendPasswordReminderEmail();
        }
    }

}
