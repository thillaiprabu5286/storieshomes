<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible banner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Block_Adminhtml_Filters extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_filters';
        $this->_blockGroup = 'ajaxlayerednavigation';
        $this->_headerText = Mage::helper('ajaxlayerednavigation')->__('Manage Filterable Attributes');

        parent::__construct();

        $this->_removeButton('add');

        $url = $this->getUrl('*/*/refresh');
        $this->_addButton('refresh_data', array(
            'label'     => Mage::helper('ajaxlayerednavigation')->__('Load Filterable Attributes'),
            'onclick'   =>  "
            function sendRequest(clearSession) {
                new Ajax.Request('".$url."', {
                    method: 'post',
                    parameters: {
                        clear_session: clearSession
                    },
                    onSuccess: showResponse
                    });
                }

            function showResponse(response) {
                var response = response.responseText.evalJSON();

                if (!response.completed) {
                    sendRequest(0);
                } else {
                    window.location = '" . $this->getUrl('*/*/index') . "'
                }
            }
            sendRequest(1);
                            "
        ));

    }
}