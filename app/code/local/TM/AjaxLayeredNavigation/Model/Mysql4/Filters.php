<?php
/**
 * DO NOT REMOVE OR MODIFY THIS NOTICE
 *
 * AffiliateSuite module for Magento - flexible partner management
 *
 * @author Templates-Master Team <www.templates-master.com>
 */

class TM_AjaxLayeredNavigation_Model_Mysql4_Filters extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ajaxlayerednavigation/filters', 'filters_id');
    }

    public function getFilterableAttributes()
    {
        $attributeData = $this->_getReadAdapter()->select()
            ->from(array('ea'=>$this->getTable('eav/attribute')), array('ea.attribute_id' ,'ea.frontend_label'))
            ->joinInner(array('cea' => $this->getTable('catalog/eav_attribute')), 'ea.attribute_id = cea.attribute_id', array())
            ->where('cea.is_filterable > 0');

        $attributeIds = $this->_getReadAdapter()->fetchAll($attributeData);

        return $attributeIds;
    }

    public function refreshAttributes()
    {
        $attributeData = $this->_getReadAdapter()->select()
            ->from(array('ea'=>$this->getTable('eav/attribute')), array('ea.attribute_id'))
            ->joinLeft(array('alnf' => $this->getTable('ajaxlayerednavigation/filters')), 'ea.attribute_id = alnf.attribute_id', array())
            ->where('ea.frontend_input IN (?)', array('select', 'multiselect', 'price'))
            ->where('alnf.filters_id IS NULL')
            ->joinInner(array('cea' => $this->getTable('catalog/eav_attribute')), 'ea.attribute_id = cea.attribute_id', array())
            ->where('cea.is_filterable > 0');

        $attributeIds = $this->_getReadAdapter()->fetchAll($attributeData);

        $result = array();
        $result['attributes'] = count($attributeIds);
        if (count($attributeIds) > 0) {
            $filtersModel = Mage::getModel('ajaxlayerednavigation/filters');

            foreach ($attributeIds as $attributeId) {
                $filtersModel->setId(null)
                    ->setData('attribute_id', $attributeId['attribute_id'])
                    ->save();
            }
        }

        $optionData = $this->_getReadAdapter()->select()
            ->from(array('eao' => $this->getTable('eav/attribute_option')), array())
            ->joinInner(array('alnf' => $this->getTable('ajaxlayerednavigation/filters')), 'eao.attribute_id = alnf.attribute_id', array('alnf.filters_id'))
            ->joinInner(array('eaov' => $this->getTable('eav/attribute_option_value')), 'eao.option_id = eaov.option_id', array('eaov.option_id'))
            ->joinLeft(array('alno' => $this->getTable('ajaxlayerednavigation/options')), 'alno.option_id = eao.option_id', array())
            ->where('eaov.store_id = 0')
            ->where('alno.foption_id IS NULL');

        $optionIds = $this->_getReadAdapter()->fetchAll($optionData);

        $optionsModel = Mage::getModel('ajaxlayerednavigation/options');

        foreach ($optionIds as $optionId) {
            $optionsModel->setId(null)
                ->setData($optionId)
                ->save();
        }

        $result['options'] = count($optionIds);

        return $result;
    }

    public function getSeoOptionsValue()
    {
        $optionData = $this->_getReadAdapter()->select()
            ->from(array('eaov' => $this->getTable('eav/attribute_option_value')), array('option_id', 'value'))
            ->where('eaov.store_id = ?', 0);

        $optionIds = $this->_getReadAdapter()->fetchPairs($optionData);

        return $optionIds;
    }
}