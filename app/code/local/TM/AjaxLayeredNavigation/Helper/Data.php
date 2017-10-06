<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * Ajax Layered Navigation module for Magento
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getFiltersDisplayValues()
    {
        return array(
            array('value' => '1' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Labels')),
            array('value' => '2' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Images')),
            array('value' => '3' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Drop-down'))
        );
    }

    public function getFiltersYesNoOptions()
    {
        return array(
            array('value' => '0' , 'label' => Mage::helper('ajaxlayerednavigation')->__('No')),
            array('value' => '1' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Yes'))
        );
    }

    public function getFiltersSortOptions()
    {
        return array(
            array('value' => '1' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Position')),
            array('value' => '2' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Quatities')),
            array('value' => '3' , 'label' => Mage::helper('ajaxlayerednavigation')->__('Name'))
        );
    }

    public function getFiltersSortOrder()
    {
        return array(
            array('value' => '1' , 'label' => Mage::helper('ajaxlayerednavigation')->__('SORT_ASC')),
            array('value' => '2' , 'label' => Mage::helper('ajaxlayerednavigation')->__('SORT_DESC')),
        );
    }

    public function isAdvancedSearchPage()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch'
            && Mage::app()->getFrontController()->getRequest()->getControllerName() == 'advanced');
    }

    public function getMaxPrice($request)
    {
        if ($request->getParam('price')) {
            $price = explode(',', $request->getParam('price'));
            return $price[1];
        }
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMaxRangeInt();
    }

    public function getMinPrice($request)
    {
        if ($request->getParam('price')) {
            $price = explode(',', $request->getParam('price'));
            return $price[0];
        }
        return (int)Mage::getModel('ajaxlayerednavigation/price')->getMinRangeInt();
    }
}
