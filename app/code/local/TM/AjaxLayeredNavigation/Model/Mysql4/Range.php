<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible partner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Model_Mysql4_Range extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ajaxlayerednavigation/range', 'range_id');
    }

    public function getAllCategory()
    {
        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $arr = array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                if ($cat->getIsActive()) {
                    $arr[$id] = $cat->getName();
                }
            }
        }
        $arr = array_flip($arr);

        return $arr;
    }

    public function refreshAttributes()
    {
        $allCategoryIds = $this->getAllCategory();
        $rangeModel = Mage::getModel('ajaxlayerednavigation/range');
        $addData = array();
        $i = 0;
        foreach ($allCategoryIds as $categoryName => $categoryId)
        {
            if (!$this->categoryExist($categoryId)) {
                $addData['category_id'] = $categoryId;
                $addData['category_name'] = $categoryName;
                $rangeModel->setId(null)
                    ->addData($addData)
                    ->save();
                $i++;
                unset($addData['category_id']);
                unset($addData['category_name']);
            }
        }

        return $i;
    }

    public function categoryExist($categoryId)
    {
        $getPartnerData = $this->_getReadAdapter()->select()
            ->from(array('alnr' => $this->getTable('ajaxlayerednavigation/range')), array('COUNT(range_id) as cat_count'))
            ->where('alnr.category_id = ?', $categoryId);

        $count = $this->_getReadAdapter()->fetchAll($getPartnerData);

        if ($count[0]['cat_count'] > 0) {
            return true;
        }

        return false;
    }
}