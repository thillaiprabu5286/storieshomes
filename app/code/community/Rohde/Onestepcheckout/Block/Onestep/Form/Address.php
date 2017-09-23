<?php
/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */


class Rohde_Onestepcheckout_Block_Onestep_Form_Address extends Mage_Checkout_Block_Onepage_Abstract
{
    public function isUseBillingAsShipping()
    {
        return $this->getConfig()->isUseBillingAsShipping();
    }

    public function getConfig()
    {
        return Mage::helper('onestepcheckout/config');
    }

    public function getAddressChangedUrl()
    {
        return Mage::getUrl('onestepcheckout/ajax/saveAddress');
    }

    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getBlockNumber($isIncrementNeeded = true)
    {
        return Mage::helper('onestepcheckout')->getBlockNumber($isIncrementNeeded);
    }

    public function getSaveFormValuesUrl()
    {
        return Mage::getUrl('onestepcheckout/ajax/saveFormValues');
    }
}