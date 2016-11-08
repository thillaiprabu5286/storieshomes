<?php

class Mindstermob_Mobileconnect_CheckoutController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
    }

    public function checkoutcartAction() {

        $params = json_decode(file_get_contents('php://input'));

        $data = $params->data;
        $logindata = $params->logindata;
        $email = $logindata->email;
        $password = $logindata->password;
        $model = Mage::getModel('mobileconnect/checkout');
        $out = $model->Checkout_cart($data, $email, $password);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($out);
    }

//====end of action======//

    public function checkoutpaymentAction() {

        $params = json_decode(file_get_contents('php://input'));

        $cartitems = $params->cart;
        $email = $params->email;
        $addressId1 = $params->id_address;
        $payment_method = $params->payment_method;

        $model = Mage::getModel('mobileconnect/checkout');
        $out = $model->Checkout_payment($cartitems, $email, $addressId1, $payment_method);
        
//        $curl = curl_init();
//        // Set some options - we are passing in a useragent too here
//        curl_setopt_array($curl, array(
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_URL => 'https://api.secure.ebs.in/api/mobileApp/paymentMethods-v1.php?'
//            . 'account_id=5087&email=mujeeb.r@abcd.com&secureHash=4508A08846D16C4BC5FC79432022B296648D2D43&',
////            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
//        ));
//        // Send the request & save response to $resp
//        $resp = curl_exec($curl);
//        // Close request to clear up some resources
//        curl_close($curl);

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($out);
    }
    
    public function checkoutpaymentnewAction() {

        $post = json_decode(file_get_contents('php://input'));
        
//        $post=$this->getRequest()->getPost();

//        $cartitems = $params->cart;
//        $email = $params->email;
//        $addressId1 = $params->id_address;
//        $payment_method = $params->payment_method;

        $model = Mage::getModel('mobileconnect/checkout');
        $out = $model->Checkout_payment_new($post);
        
//        $curl = curl_init();
//        // Set some options - we are passing in a useragent too here
//        curl_setopt_array($curl, array(
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_URL => 'https://api.secure.ebs.in/api/mobileApp/paymentMethods-v1.php?'
//            . 'account_id=5087&email=mujeeb.r@abcd.com&secureHash=4508A08846D16C4BC5FC79432022B296648D2D43&',
////            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
//        ));
//        // Send the request & save response to $resp
//        $resp = curl_exec($curl);
//        // Close request to clear up some resources
//        curl_close($curl);

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody($out);
    }

//====end of action======//

    public function responseAction() {
//        $order = new Mage_Sales_Model_Order();
//        $session = Mage::getSingleton('customer/session');
//        $model = Mage::getModel('hdfc/credit');
//        $secret = $model->getConfigData('secret');
//        if (!$this->getRequest()->isPost()) {
//            $session->addError($this->__('Unknown error occured'));
//            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
//            return;
//        }
//        $post=$this->getRequest()->getPost();
        $post = json_decode(file_get_contents('php://input'));
        
        $hash = $post['SecureHash'];
        unset($post['SecureHash']);
        ksort($post);
        $hashData = $secret;
        $genHash = '';
        foreach ($post as $key => $value) {
            if (strlen($value) > 0) {
                $hashData .= '|' . $value;
            }
        }
        if (strlen($hashData) > 0) {
            $genHash = strtoupper(hash('sha512', $hashData));
        }
        if ($hash !== $genHash) {
            $session->addError($this->__('Unknown error'));
            Mage_Core_Controller_Varien_Action::_redirect('checkout/cart', array('_secure' => true));
            return;
        }
        $order_id = str_replace('STRORD', '', $post['MerchantRefNo']);
        $order->loadByIncrementId($order_id);
        $pay = $order->getPayment();
        $pay->setTransactionAdditionalInfo('hdfcTransactionID', $post['TransactionID']);
        $pay->setTransactionAdditionalInfo('hdfcRequestID', $post['RequestID']);
        $pay->setTransactionAdditionalInfo('hdfcPaymentID', $post['PaymentID']);

        $pay->save();
        if ($post['ResponseCode'] !== '0') {
            $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            Mage::getSingleton('checkout/session')->unsLastRealOrderId();
            Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure' => true));
            return;
        }
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
//        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();
        Mage::getSingleton('checkout/session')->unsQuoteId();
        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure' => true));
    }

}

//=============end of class===========//


