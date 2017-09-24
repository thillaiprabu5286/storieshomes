<?php
class TM_AjaxLayeredNavigation_Adminhtml_Model_System_Config_Source_Category
{
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
         
         foreach ($arr as $key => $value) {
             if (null === $value) {
                 unset($arr[$key]);
             }
         }

         $arr = array_flip($arr);
         
         return $arr;
     }
         
     public function toOptionArray()
     {
        $allCategory = $this->getAllCategory();
        $result = array();

        foreach ($allCategory as $name => $id) {
            $result[] = array('value' => $id, 'label' => Mage::helper('ajaxlayerednavigation')->__($name));
        }

        return $result;
     }
}
