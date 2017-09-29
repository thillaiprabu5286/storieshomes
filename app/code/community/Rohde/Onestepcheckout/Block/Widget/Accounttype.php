<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Block_Widget_AccountType extends Mage_Customer_Block_Widget_Abstract {

    /**
     * Initialize block
     */
    public function _construct() {
        parent::_construct();
        $this->setTemplate('onestepcheckout/customer/widget/account_type.phtml');
    }

    /**
     * Check if account_type attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled() {
        return (bool) $this->_getAttribute('account_type')->getIsVisible();
    }

    /**
     * Check if account_type attribute marked as required
     *
     * @return bool
     */
    public function isRequired() {
        return (bool) $this->_getAttribute('account_type')->getIsRequired();
    }

    /**
     * Get current customer from session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer() {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

}
