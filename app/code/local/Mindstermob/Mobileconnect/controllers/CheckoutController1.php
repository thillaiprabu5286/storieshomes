<?php
class Mindstermob_Mobileconnect_CheckoutController extends Mage_Core_Controller_Front_Action
{

     public function indexAction()
    {
        
        
    }
    
  
    
    public function checkoutcartAction(){
        
        $params =json_decode(file_get_contents('php://input'));

        $data=$params->data;
        $logindata=$params->logindata;
         $email=$logindata->email;
         $password=$logindata->password;
         $model = Mage::getModel('mobileconnect/checkout');             
         $out=$model->Checkout_cart($data,$email,$password);
          $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
          $this->getResponse()->setBody($out);
         
    }//====end of action======//
    
    public function checkoutpaymentAction(){
        
        $params =json_decode(file_get_contents('php://input'));

        $cartitems=$params->cart;
        $email=$params->email;
        $addressId1=$params->id_address;
        $payment_method=$params->payment_method;
         
        $model = Mage::getModel('mobileconnect/checkout');             
         $out=$model->Checkout_payment($cartitems,$email,$addressId1,$payment_method);
         $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true);
         $this->getResponse()->setBody($out);
         
    }//====end of action======//
    
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
        Mage::log('response= '.json_encode($post));
        exit;
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
        
        $pay->save();
        if($post['ResponseCode']!=='0'){
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
    
    
}//=============end of class===========//


