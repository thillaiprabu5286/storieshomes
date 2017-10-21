<?php

class Mango_Attributemapping_Adminhtml_AttributemappingController extends Mage_Adminhtml_Controller_action {

    protected $_row_data = array();

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('attributemapping/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    public function indexAction() {
        Mage::getModel("attributemapping/attributemapping")->refresh();
        $this->_initAction()
                ->renderLayout();
    }

    public function addAction() {
        $_json_response = array();

        try {
            if ($this->_checkParamValues()) {
                Mage::getModel('attributemapping/attributemapping')
                        ->setData($this->_row_data)
                        ->save();
                $_json_response["result"] = "success";
                $_json_response["message"] = $this->__("Saved Successfully");
                $_json_response["html"] = $this->_getCellHtml();
            } else {
                $_json_response["result"] = "error";
                $_json_response["message"] = $this->__("Missing parameters");
            }
        } catch (Exception $e) {
            $_json_response["result"] = "error";
            $_json_response["message"] = $e->getMessage();
        }
        $this->_sendJsonResponse($_json_response);
    }

    public function deleteAction() {
        $_json_response = array();
        try {
            if ($this->_checkParamValues()) {
                $id = $this->getRequest()->getParam('attributemapping_id');
                Mage::getModel('attributemapping/attributemapping')->load($id)->delete();
                $_json_response["result"] = "success";
                $_json_response["message"] = $this->__("Deleted Successfully");
                $_json_response["html"] = $this->_getCellHtml();
            } else {
                $_json_response["result"] = "error";
                $_json_response["message"] = $this->__("Missing parameters");
            }
        } catch (Exception $e) {
            $_json_response["result"] = "error";
            $_json_response["message"] = $e->getMessage();
        }
        $this->_sendJsonResponse($_json_response);
    }

    protected function _checkParamValues() {
        /* we need 4 integer values */
        $_parameters = array(
            'child_attribute_id',
            'child_attribute_value_id',
            'parent_attribute_id',
            'parent_attribute_value_id'
        );
        foreach ($_parameters as $_field) {
            $_field_value = $this->getRequest()->getParam($_field);

            if (!$_field_value || (int) $_field_value == 0) {
                return false;
            }
            $this->_row_data[$_field] = (int) $this->getRequest()->getParam($_field);
        }
        return true;
    }

    protected function _getCellHtml() {
        /* get row data */
        $this->_row_data;
        /* filter collection */
        $_data = Mage::getModel("attributemapping/attributemapping")->getGridCollection()
                ->addFieldToFilter('child_attribute_id', $this->_row_data['child_attribute_id'])
                ->addFieldToFilter('child_attribute_value_id', $this->_row_data['child_attribute_value_id'])
                ->addFieldToFilter('parent_attribute_id', $this->_row_data['parent_attribute_id'])
                ->getFirstItem();
        /* render cell data... try using the same grid function... */
        $_html = $this->getLayout()->createBlock("attributemapping/adminhtml_attributemapping_grid_render_list")->render($_data);
        return $_html;
    }

    private function _sendJsonResponse($_json_response) {
        $this->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'application/json')
                ->setHeader('Access-Control-Allow-Origin', '*')
                ->setBody(json_encode($_json_response))
                ->sendResponse();
        exit;
    }

    public function exportCsvAction() {
        $fileName = 'attributemapping.csv';
        $content = $this->getLayout()->createBlock('attributemapping/adminhtml_attributemapping_grid')
                ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'attributemapping.xml';
        $content = $this->getLayout()->createBlock('attributemapping/adminhtml_attributemapping_grid')
                ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}
