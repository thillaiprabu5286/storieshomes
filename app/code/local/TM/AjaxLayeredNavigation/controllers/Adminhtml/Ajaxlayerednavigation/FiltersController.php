<?php

class TM_AjaxLayeredNavigation_Adminhtml_Ajaxlayerednavigation_FiltersController extends Mage_Adminhtml_Controller_Action
{

    protected $_processTime = 20;

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ajaxlayerednavigation/filters')
            ->_addBreadcrumb(Mage::helper('ajaxlayerednavigation')->__('Manage Filterable Attributes'), Mage::helper('ajaxlayerednavigation')->__('Manage Filterable Attributes'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters'));
        $this->renderLayout();
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
            ->_addBreadcrumb($id ? Mage::helper('ajaxlayerednavigation')->__('Edit Filters') : Mage::helper('ajaxlayerednavigation')->__('New Proframe'), $id ? Mage::helper('ajaxlayerednavigation')->__('Edit Filter') : Mage::helper('ajaxlayerednavigation')->__('New Proframe'))
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
                    ->setData('action', $this->getUrl('*/*/opsave'))
                    ->setData('form_action_url', $this->getUrl('*/*/opsave'))
            )
            ->renderLayout();

    }

    /**
     * Banner grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters_grid')->toHtml()
        );
    }

    public function optionsGridAction()
    {
        $this->loadLayout();
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

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ajaxlayerednavigation/adminhtml_filters_edit_tab_options')->toHtml()
        );
    }

    public function refreshAction()
    {
        try {
            $filtersModel = Mage::getModel('ajaxlayerednavigation/filters');
            $result = $filtersModel->refreshAttributes();

            Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('ajaxlayerednavigation')->__(
                'Total of %d attributes(s) were added.', $result['attributes']
                )
            );

            Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('ajaxlayerednavigation')->__(
                'Total of %d options(s) were added.', $result['options']
                )
            );

            return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                    'completed' => true)
            ));

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    public function massDeleteAction()
    {
        $filtersIds = $this->getRequest()->getParam('filters');

        if(!is_array($filtersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select attribute(s).'));
        } else {
            try {
                $filters = Mage::getModel('ajaxlayerednavigation/filters');
                $deleteCount = count($filtersIds);
                foreach ($filtersIds as $filterId) {
                    $filters->load($filterId)
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

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('ajaxlayerednavigation/filters');

                if ($data['filters_id'] != '') {
                    $model->load($data['filters_id']);
                } else {
                    unset($data['filters_id']);
                }

                $model->addData($data);
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ajaxlayerednavigation')->__('Filters was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
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

    public function opsaveAction()
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
                $this->_redirect('*/*/edit', array('id' => $model->getFiltersId()));
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('templates_master/ajaxlayerednavigation/filters');
    }
}

