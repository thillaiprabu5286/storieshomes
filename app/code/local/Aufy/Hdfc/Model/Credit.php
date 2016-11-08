<?php
class Aufy_Hdfc_Model_Credit extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'hdfc';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
            
		return Mage::getUrl('hdfc/payment/redirect', array('_secure' => true));
	}
        
        
}
?>