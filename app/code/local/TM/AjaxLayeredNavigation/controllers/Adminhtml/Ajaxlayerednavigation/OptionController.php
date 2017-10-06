<?php

class TM_AjaxLayeredNavigation_Adminhtml_Ajaxlayerednavigation_OptionController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ajaxlayerednavigation/filters')
            ->_addBreadcrumb(Mage::helper('ajaxlayerednavigation')->__('Manage Options'), Mage::helper('ajaxlayerednavigation')->__('Manage Options'));
        return $this;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('ajaxlayerednavigation/filters');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ajaxlayerednavigation')->__('This filter not exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        Mage::register('ajaxlayerednavigation_filters', $model);

        $this->_initAction();

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true)
        ;

        $this
            ->_addBreadcrumb($id ? Mage::helper('ajaxlayerednavigation')->__('Edit Filters') : Mage::helper('ajaxlayerednavigation')->__('New Filter'), $id ? Mage::helper('ajaxlayerednavigation')->__('Edit Filter') : Mage::helper('ajaxlayerednavigation')->__('New Proframe'))
            ->_addContent(
                $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters_edit')
                    ->setData('action', $this->getUrl('*/*/save'))
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->_addLeft($this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters_edit_tabs'))
            ->renderLayout();

    }

    public function optionAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('ajaxlayerednavigation/options');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ajaxlayerednavigation')->__('This option not exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        Mage::register('ajaxlayerednavigation_option', $model);

        $this->_initAction();

        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true)
        ;

        $this
            ->_addBreadcrumb($id ? Mage::helper('ajaxlayerednavigation')->__('Edit Option') : Mage::helper('ajaxlayerednavigation')->__('New Option'), $id ? Mage::helper('ajaxlayerednavigation')->__('Edit Option') : Mage::helper('ajaxlayerednavigation')->__('New Option'))
            ->_addContent(
                $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_option_edit')
                    ->setData('action', $this->getUrl('*/*/save'))
                    ->setData('form_action_url', $this->getUrl('*/*/save'))
            )
            ->renderLayout();

    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('ajaxlayerednavigation/options');

                if ($data['foption_id'] != '') {
                    $model->load($data['foption_id']);
                } else {
                    unset($data['foption_id']);
                }
                $model->addData($data);
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ajaxlayerednavigation')->__('Option was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/option', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/adminhtml_filters/edit', array('id' => $model->getFiltersId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/option', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters_edit_tab_options')->toHtml()
        );
    }

}
