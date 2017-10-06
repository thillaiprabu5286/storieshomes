<?php

class TM_AjaxLayeredNavigation_Adminhtml_Ajaxlayerednavigation_RangeController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ajaxlayerednavigation/range')
            ->_addBreadcrumb(Mage::helper('ajaxlayerednavigation')->__('Manage Ranges'), Mage::helper('ajaxlayerednavigation')->__('Manage Ranges'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_range'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('ajaxlayerednavigation/range');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ajaxlayerednavigation')->__('This range not exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        Mage::register('ajaxlayerednavigation_range', $model);

        $this->_initAction();

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true)
        ;

        $this
            ->_addBreadcrumb($id ? Mage::helper('ajaxlayerednavigation')->__('Edit Range') : Mage::helper('ajaxlayerednavigation')->__('New Range'), $id ? Mage::helper('ajaxlayerednavigation')->__('Edit Filter') : Mage::helper('ajaxlayerednavigation')->__('New Proframe'))
            ->_addContent(
                $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_range_edit')
                    ->setData('action', $this->getUrl('*/*/save'))
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->renderLayout();

    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_range_grid')->toHtml()
        );
    }

    public function massDeleteAction()
    {
        $rangeIds = $this->getRequest()->getParam('ranges');

        if(!is_array($rangeIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select range(s).'));
        } else {
            try {
                $range = Mage::getModel('ajaxlayerednavigation/range');
                $deleteCount = count($rangeIds);
                foreach ($rangeIds as $rangeId) {
                    $range->load($rangeId)
                        ->delete();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('ajaxlayerednavigation')->__(
                    'Total of %d record(s) were deleted.', $deleteCount
                    )
                );

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function refreshAction()
    {
        try {
            $rangeModel = Mage::getModel('ajaxlayerednavigation/range');
            $result = $rangeModel->refreshAttributes();

            Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('ajaxlayerednavigation')->__(
                'Total of %d category(s) were added.', $result
                )
            );

            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                    'completed' => true)
            ));

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('ajaxlayerednavigation/range');

                if ($data['range_id'] != '') {
                    $model->load($data['range_id']);
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ajaxlayerednavigation')->__('Range not found.'));
                    $this->_redirect('*/*/index');
                    return;
                }

                $model->addData($data);
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ajaxlayerednavigation')->__('Range was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);

                $this->_redirect('*/*/index');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('templates_master/ajaxlayerednavigation/range');
    }
}
