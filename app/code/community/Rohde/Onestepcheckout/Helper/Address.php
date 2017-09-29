<?php

/**
 *
 * @category   Rohde
 * @package    Rohde_Onestepcheckout
 * @author     Support <j.rohde@nederland.live>
 */
class Rohde_Onestepcheckout_Helper_Address extends Mage_Core_Helper_Data {

    public function saveBilling($data = array(), $customerAddressId = null) {

        $address = $this->getQuote()->getBillingAddress();
        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array(
                        'error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.'),
                    );
                }
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            //fix for mage 1702
            if (@class_exists('Mage_Customer_Model_Form')) {
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_address_edit')
                        ->setEntityType('customer_address')
                        ->setIsAjaxRequest(Mage::app()->getRequest()->isAjax());
                $addressForm->setEntity($address);
                $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
                $addressForm->compactData($addressData);
                //unset billing address attributes which were not shown in form
                foreach ($addressForm->getAttributes() as $attribute) {
                    if (!isset($data[$attribute->getAttributeCode()])) {
                        $address->setData($attribute->getAttributeCode(), NULL);
                    }
                }
                $address->setCustomerAddressId(null);
                // Additional form data, not fetched by extractData (as it fetches only attributes)
                $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            } else {
                $address->addData($data);
            }
        }
        $address->implodeStreetAddress();
        if (!$this->getQuote()->isVirtual()) {

            $useBillingForShipping = isset($data['use_for_shipping']) ? 1 : 0;
            $shipping = $this->getQuote()->getShippingAddress();
            // clear session shipping method
            $shipping->setShippingMethod('');
            switch ($useBillingForShipping) {
                case 0:
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shippingMethod = $shipping->getShippingMethod();
                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');
                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setSaveInAddressBook(0)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    break;
            }
        }
        $this->getQuote()->collectTotals()->save();
        if (!$this->getQuote()->isVirtual()) {
            //Recollect Shipping rates for shipping methods
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        }
        return array();
    }


    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  Mage_Checkout_Model_Type_Onepage
     */
    public function saveShipping($data = array(), $customerAddressId = null) {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array(
                        'error' => 1,
                        'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                    );
                }
                $address->importCustomerAddress($customerAddress)->setSaveInAddressBook(0);
            }
        } else {
            $address->addData($data);
            $address->setCustomerAddressId(null);
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);
        $this->getQuote()->collectTotals()->save();
        return array();
    }


    public function initAddress() {

        $data = [];
        if($this->getQuote()->getShippingAddress()->getData('same_as_billing')) {
            $data['use_for_shipping'] = true;
        }

        if ($this->getQuote()->getBillingAddress()->getCustomerAddressId()) {
            $this->saveBilling($data, $this->getQuote()->getBillingAddress()->getCustomerAddressId());
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->getQuote()->collectTotals();
            $this->getQuote()->save();
            return;
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($primaryBillingAddress = $customer->getPrimaryBillingAddress()) {
            $customerAddressId = $primaryBillingAddress->getId();
            $this->saveBilling($data, $customerAddressId);
        } else {
            $data['country_id'] = Mage::getStoreConfig('general/country/default');
            $this->saveBilling($data);
        }
    }

    public function getQuote() {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function getOnepage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function validateAddressData($data) {
        $validationErrors = array();
        $requiredFields = array(
            'country_id',
            'city',
            'postcode',
            'region_id',
        );
        foreach ($requiredFields as $requiredField) {
            if (!isset($data[$requiredField])) {
                $validationErrors[] = $this->__("Field %s is required", $requiredField);
            }
        }
        return $validationErrors;
    }

}
