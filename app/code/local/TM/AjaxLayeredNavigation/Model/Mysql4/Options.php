<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible partner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Model_Mysql4_Options extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ajaxlayerednavigation/options', 'foption_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $categoryImage = $object->getCategoryImage();
        $productPageImage = $object->getProductPageImage();
        $layeredImage = $object->getLayeredImage();

        $categoryFlag = false;
        $productPageFlag = false;
        $layeredFlag = false;

        $path = Mage::getBaseDir('media') . DS . 'ajaxlayerednavigation' . DS;

        $i = 0;
        if (is_array($categoryImage) && !empty($categoryImage['delete'])) {
            @unlink($path . $categoryImage['value']);
            $object->setCategoryImage('');
            $i++;
            $categoryFlag = true;
        }

        if (is_array($productPageImage) && !empty($productPageImage['delete'])) {
            @unlink($path . $productPageImage['value']);
            $object->setProductPageImage('');
            $i++;
            $productPageFlag = true;
        }

        if (is_array($layeredImage) && !empty($layeredImage['delete'])) {
            @unlink($path . $layeredImage['value']);
            $object->setLayeredImage('');
            $i++;
            $layeredFlag = true;
        }

        if ($i == 3) {
            return $this;
        }

        $j = 0;
        if (empty($_FILES['category_image']['name']) && !$categoryFlag) {
            if (is_array($categoryImage)) {
                $object->setCategoryImage($categoryImage['value']);
            }
            $j++;
        }

        if (empty($_FILES['product_page_image']['name']) && !$productPageFlag) {
            if (is_array($productPageImage)) {
                $object->setProductPageImage($productPageImage['value']);
            }
            $j++;
        }

        if (empty($_FILES['layered_image']['name']) && !$layeredFlag) {
            if (is_array($layeredImage)) {
                $object->setLayeredImage($layeredImage['value']);
            }
            $j++;
        }

        if ($j == 3) {
            return $this;
        }

        if ($_FILES['category_image']['name']) {
            try {
                $uploader = new Varien_File_Uploader('category_image');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png', 'bmp'));
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path);
                $object->setCategoryImage($uploader->getUploadedFileName());
            } catch (Exception $e) {
                $object->unsCategoryImage();
                throw $e;
            }
        }

        if ($_FILES['product_page_image']['name']) {
            try {
                $uploader = new Varien_File_Uploader('product_page_image');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png', 'bmp'));
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path);
                $object->setProductPageImage($uploader->getUploadedFileName());
            } catch (Exception $e) {
                $object->unsProductPageImage();
                throw $e;
            }
        }

        if ($_FILES['layered_image']['name']) {
            try {
                $uploader = new Varien_File_Uploader('layered_image');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png', 'bmp'));
                $uploader->setAllowRenameFiles(true);
                $uploader->save($path);
                $object->setLayeredImage($uploader->getUploadedFileName());
            } catch (Exception $e) {
                $object->unsLayeredImage();
                throw $e;
            }
        }

        return $this;
    }

    public function getOptionsData($options)
    {
        $getOptionsData = $this->_getReadAdapter()->select()
            ->from(array('alno' => $this->getTable('ajaxlayerednavigation/options')), array('category_title', 'category_description', 'category_image'))
            ->where('alno.option_id in (?)', $options);

        $result = $this->_getReadAdapter()->fetchAll($getOptionsData);

        return $result;
    }
}