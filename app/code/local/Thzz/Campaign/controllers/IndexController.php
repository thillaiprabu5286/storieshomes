<?php
/**
 * Created by PhpStorm.
 * User: prabu
 * Date: 02/11/16
 * Time: 2:02 PM
 */
class Thzz_Campaign_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function postAction()
    {
        $debug = true;
        $data = $this->getRequest()->getPost();
        try {
            if ($data) {
                $model = Mage::getModel('thzz_campaign/campaign');
                $data['created_at'] = date('Y-m-d H:i:s');
                $model->addData($data)
                    ->save();

                // Send Email to Customer
                $this->_notifyCustomerByEmail($data);

                // Send Email to Admin
                $this->_notifyAdminByEmail($data);

            }
        } catch (Exception $e) {
            echo (string)$e->getMessage();
        }

        $this->_redirect('emailerCampaign/index/success');
    }

    protected function _notifyAdminByEmail($data)
    {
        $debug = true;

        $dataObject = new Varien_Object();
        $dataObject->setData($data);

        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $mailTemplate = Mage::getModel('core/email_template');

        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
            ->setReplyTo(Mage::getStoreConfig('trans_email/ident_custom1/email'))
            ->sendTransactional(
                'new_campaign_register_template',
                Mage::getStoreConfig('trans_email/ident_custom1/email'),
                'StorieshomeBangalore@gmail.com',
                null,
                array('data' => $dataObject)
            );

        if (!$mailTemplate->getSentSuccess()) {
            throw new Exception();
        }
    }

    protected function _notifyCustomerByEmail($data)
    {
        $debug = true;
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->loadDefault('campaign_success_template');

        //$emailTemplateVariables = array();

        $processedTemplate = $emailTemplate->getProcessedTemplate();

        $emailTemplate
            ->setSenderName(Mage::getStoreConfig('trans_email/ident_custom1/name'))
            ->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom1/email'))
            ->setTemplateSubject("Welcome {$data['name']}")
            ->setBody($processedTemplate)
            ->setType('html');

        try{
            //Confimation E-Mail Send
            $emailTemplate->send(
                $data['email'],
                $data['name']
            );
        }
        catch(Exception $error)
        {
            Mage::getSingleton('core/session')->addError($error->getMessage());
            return false;
        }
    }

    public function successAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}