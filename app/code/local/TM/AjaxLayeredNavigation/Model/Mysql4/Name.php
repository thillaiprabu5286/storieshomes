<?php
class TM_AjaxLayeredNavigation_Model_Mysql4_Name
    extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        return $this;
    }

    public function applyFilterToCollection($filter, $search)
    {
        $searchItems = explode(' ', $search);
        $items = array();
        foreach ($searchItems as $searchItem) {
            $items[] = array('like' => '%' . $searchItem . '%');
        }

        $collection = $filter->getLayer()->getProductCollection();
        $collection->addAttributeToFilter('name', $items);

        return $this;
    }
}