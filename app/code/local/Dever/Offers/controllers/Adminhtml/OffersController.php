<?php

class Dever_Offers_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $booking = $this->_initBooking();
        $id = $this->getRequest()->getParam('id', null);
        if ($id) {
            $booking->load((int) $id);
            if ($booking->getId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
                    $booking->setData($data)->setId($id);
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dever_offers')->__('Booking doesn\'t exist'));
                $this->_redirect('*/*/');
            }
        }

        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    public function saveAction()
    {
        $booking = $this->_initBooking();
        if ($data = $this->getRequest()->getPost())
        {
            $id = $this->getRequest()->getParam('id');
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try {

                $currentDateTime = date('Y-m-d H:i:s');
                if ($id) {
                    $booking->setId($id)->setUpdatedAt($currentDateTime);
                }

                $booking->setStatus($data['status'])->save();
                if (!$booking->getId()) {
                    Mage::throwException(Mage::helper('zodiya_affiliates')->__('Error saving bookings'));
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dever_offers')->__('Booking saved successfully'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $booking->getId()));
                } else {
                    $this->_redirect('*/*/');
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                if ($booking && $booking->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $booking->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
            }

            return;
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dever_offers')->__('No data found to save'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('dever_offers');
    }

    protected function _initBooking()
    {
        $offers = Mage::getModel('dever_offers/offers');
        if ($id = $this->getRequest()->getParam('id')) {
            $offers->load($id);
            if ($offers->getId()) {
                Mage::register('current_booking', $offers);
            }
        }

        return $offers;
    }

    public function massDeleteAction()
    {
        $bookingIds = $this->getRequest()->getParam('booking');
        if (!is_array($bookingIds)) {
            $this->_getSession()->addError($this->__('Please select record(s).'));
        } else {
            if (!empty($bookingIds)) {
                try {
                    foreach ($bookingIds as $bookingId) {
                        $booking = Mage::getSingleton('dever_offers/offers')->load($bookingId);
                        $booking->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($bookingIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
}