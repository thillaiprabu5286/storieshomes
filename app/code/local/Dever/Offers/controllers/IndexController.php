<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 15/12/17
 * Time: 6:41 PM
 */
class Dever_Offers_IndexController extends Mage_Core_Controller_Front_Action
{
    public function postAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            if ($post) {
                $model = Mage::getModel('dever_offers/offers');
                $model->addData($post);
                //Generate uniq coupon code
                $currDateTime = date('Y-m-d H:i:s');
                $couponCode = 'ST' . strtotime($currDateTime);
                $model->setCouponCode($couponCode)
                    ->setCreatedAt($currDateTime)
                    ->setProductName($post['product']);
                if ($model->save()) {

                    /** @var Mage_Catalog_Model_Product $product */
                    $product = Mage::getModel('catalog/product')->load($model->getProductId());

                    $options = array (
                        'name' => $model->getName(),
                        'email' => $model->getEmail(),
                        'phone' => $model->getTelephone(),
                        'product_name' => $product->getName(),
                        'sku'   => $product->getSku(),
                        'coupon_code' => $model->getCouponCode(),
                        'price' => $model->getPrice()
                    );

                    //Trigger Sms
                    /** @var Dever_Sms_Helper_Data $helper */
                    $helper = Mage::helper('dever_sms');
                    $helper->sendSms('deals', $options);

                    //Trigger Email
                    $this->_notifyUsersByEmail($options);

                    Mage::getSingleton('core/session')->addSuccess(
                        Mage::helper('dever_offers')->__('<h4>Thanks for registration. Promo code will be shared in sms shortly.</h4><p>You may not receive the message when DND serice is activated in your phone. For any assistance please contact us at: 9072345014.</p>')
                    );
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('dever_offers')->__('You have already registered for offline deals. Please check for online offers.')
            );
        }
        $this->_redirectUrl(Mage::getUrl('deals'));
        return $this;
    }

    protected function _notifyUsersByEmail($options)
    {
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('offers_template');

        //Variables for Confirmation Mail.
        $emailTemplateVariables = array();
        $emailTemplateVariables['product_name'] = $options['product_name'];
        $emailTemplateVariables['coupon_code'] = $options['coupon_code'];
        $emailTemplateVariables['price'] = $options['price'];

        //Appending the Custom Variables to Template.
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

        //Subject
        $message = "Thanks {$options['name']} for your interest in {$options['product_name']}";
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom1/name'))
            ->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom1/email'))
            ->setTemplateSubject($message)
            ->setBody($processedTemplate)
            ->setType('html');

        try{
            //Confimation E-Mail Send
            $emailTemplate->send($options['email'], $options['name']);
        }
        catch(Exception $error)
        {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            return false;
        }
    }
}