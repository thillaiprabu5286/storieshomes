<?php

/*
  Mygateway Payment Controller
  By: Junaid Bhura
  www.junaidbhura.com
 */

class Aufy_Hdfc_PaymentController extends Mage_Core_Controller_Front_Action {

    protected $_methods = array(
        'hdfc' => '1',
        'hdfc_debit' => '2',
        'hdfc_net' => '3'
    );

    // The redirect action is triggered when someone places an order
    public function redirectAction() {
        $_order = new Mage_Sales_Model_Order();
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $session = Mage::getSingleton('customer/session');
        if (empty($orderId)) {
            $session->addError($this->__('Invalid order'));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            return;
        }
        $_order->loadByIncrementId($orderId);
        $type = $_order->getPayment()->getMethod();
        if (empty($type) || empty($this->_methods[$type])) {
            $session->addError($this->__('Invalid payment method'));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            return;
        }

        $address = $_order->getBillingAddress();
        $ship_address = $_order->getShippingAddress();

        $model = Mage::getModel('hdfc/credit');

        $customerid = $model->getConfigData('customer_id');
        $secret = $model->getConfigData('secret');
        if (empty($secret) || empty($customerid)) {
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            $session->addError($this->__('Gateway is not configured'));
            return;
        }
        $post = array(
            'payment_mode' => $this->_methods[$type],
            'channel' => '10',
            'account_id' => $customerid,
            'reference_no' => 'STRORD' . $orderId, //'STRORD'.$orderId,
            'mode' => 'LIVE',
            'currency' => 'INR',
            'display_currency' => 'INR',
            'display_currency_rates' => '1',
            'description' => "Stories life order #" . $orderId . ', INR.' . number_format($_order->getBaseGrandTotal(), 2) . " for customer " . $_order->getCustomerEmail(),
            'return_url' => Mage::getUrl('hdfc/payment/response', array('_secure' => true)),
            'card_brand' => '',
            'payment_option' => '',
            'bank_code' => '',
            'emi' => '',
            'page_id' => '',
            'name' => $address->getFirstname() . ' ' . $address->getLastname(),
            'ship_name' => $ship_address->getFirstname() . ' ' . $ship_address->getLastname(),
            'address' => $address->getStreet1(),
            'ship_address' => $ship_address->getStreet1(),
            'city' => $address->getCity(),
            'ship_city' => $ship_address->getCity(),
            'state' => $address->getRegion(),
            'ship_state' => $ship_address->getRegion(),
            'country' => 'IND',
            'ship_country' => 'IND',
            'postal_code' => $address->getPostcode(),
            'ship_postal_code' => $ship_address->getPostcode(),
            'phone' => $address->getTelephone(),
            'ship_phone' => $ship_address->getTelephone(),
            'email' => $_order->getCustomerEmail(),
            'amount' => number_format($_order->getBaseGrandTotal(), 2, '.', '')
        );
        $hashData = $secret;
        ksort($post);
        foreach ($post as $key => $value) {
            if (strlen($value) > 0) {
                $hashData .= '|' . $value;
            }
        }
        if (strlen($hashData) > 0) {
            $post['secure_hash'] = strtoupper(hash('sha512', $hashData));
        }
        Zend_Registry::set('post', $post);
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'hdfc', array('template' => 'hdfc/redirect.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    // The response action is triggered when your gateway sends back a response after processing the customer's payment
    public function responseAction() {
        $order = new Mage_Sales_Model_Order();
        $session = Mage::getSingleton('customer/session');
        $model = Mage::getModel('hdfc/credit');
        $secret = $model->getConfigData('secret');
        if (!$this->getRequest()->isPost()) {
            $session->addError($this->__('Unknown error occured'));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            return;
        }
        $post=$this->getRequest()->getPost();
        $hash=$post['SecureHash'];
        unset($post['SecureHash']);
        ksort($post);
        $hashData=$secret;
        $genHash='';
        foreach ($post as $key => $value) {
            if (strlen($value) > 0) {
                $hashData .= '|' . $value;
            }
        }
        if (strlen($hashData) > 0) {
            $genHash = strtoupper(hash('sha512', $hashData));
        }
        if($hash!==$genHash){
            $session->addError($this->__('Unknown error'));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            return;
        }
        $order_id=  str_replace('STRORD', '', $post['MerchantRefNo']);
        $order->loadByIncrementId($order_id);
        $pay=$order->getPayment();
        $pay->setTransactionAdditionalInfo('hdfcTransactionID', $post['TransactionID']);
        $pay->setTransactionAdditionalInfo('hdfcRequestID', $post['RequestID']);
        $pay->setTransactionAdditionalInfo('hdfcPaymentID', $post['PaymentID']);
        
        // mujeeb added
        $bank_data = array();
        $bank_data['hdfcTransactionID'] = $post['TransactionID'];
        $bank_data['hdfcRequestID'] = $post['RequestID'];
        $bank_data['hdfcPaymentID'] = $post['PaymentID'];
        $pay->setAdditionalInformation(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $bank_data
        );

        $pay->save();
        if($post['ResponseCode']!=='0'){
            $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            Mage::getSingleton('checkout/session')->unsLastRealOrderId();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
            return;
        }
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();
        Mage::getSingleton('checkout/session')->unsQuoteId();
        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => true));
    }

}
