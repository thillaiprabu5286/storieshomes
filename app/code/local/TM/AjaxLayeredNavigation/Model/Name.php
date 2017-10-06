<?php
class TM_AjaxLayeredNavigation_Model_Name
    extends Mage_Catalog_Model_Layer_Filter_Abstract
{
    protected $_resource;
    protected $_nameFilter;

    public function __construct()
    {
        parent::__construct();
        $this->_requestVar = 'n';
        $this->_nameFilter = '';
    }

    protected function _getResource()
    {
        $this->_resource = Mage::getResourceModel('ajaxlayerednavigation/name');
        return $this->_resource;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $this->_getResource()->applyFilterToCollection($this, $filter);
        $this->getLayer()->getState()->addFilter(
            $this->_createItem($filter, $filter)
        );

        $this->_nameFilter = $filter;

        return $this;
    }

    public function getItemsCount()
    {
        return 1;
    }

    public function getName()
    {
        return __('Name');
    }

    public function getActiveValue()
    {
        return $this->_nameFilter;
    }
}
