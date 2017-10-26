<?php

class Mango_Attributemapping_Block_Adminhtml_Attributemapping_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    protected $_defaultLimit = 100;

    public function __construct() {
        parent::__construct();
        $this->setId('attributemappingGrid');
        $this->setDefaultSort('child_attribute_value_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setRowClickCallback(null);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('attributemapping/attributemapping')->getGridCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('child_attribute_value_id', array(
            'header' => Mage::helper('attributemapping')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'child_attribute_value_id',
        ));
        $this->addColumn('child_attribute_id', array(
            'header' => Mage::helper('attributemapping')->__('Child Attribute'),
            'align' => 'left',
            'index' => 'child_attribute_id',
            'type' => 'options',
            'options' => Mage::getModel('attributemapping/attribute_child')->getOptionArray(),
        ));
        $this->addColumn('parent_attribute_id', array(
            'header' => Mage::helper('attributemapping')->__('Parent Attribute'),
            'align' => 'left',
            'index' => 'parent_attribute_id',
            'type' => 'options',
            'options' => Mage::getModel('attributemapping/attribute_parent')->getOptionArray(),
        ));
        $this->addColumn('value', array(
            'header' => Mage::helper('attributemapping')->__('Child Value'),
            'align' => 'right',
            'index' => 'value',
            'renderer' => 'Mango_Attributemapping_Block_Adminhtml_Attributemapping_Grid_Render_Child',
        ));
        $this->addColumn('parent_attribute_value_ids', array(
            'header' => Mage::helper('attributemapping')->__('Parent Value'),
            'align' => 'left',
            'index' => 'parent_attribute_value_ids',
            'type' => 'options',
            'sortable' => false,
            'column_css_class' => 'parent_attribute_value_ids_column',
            'options' => Mage::getModel('attributemapping/attribute_parent_value')->getOptionArray(),
            'renderer' => 'Mango_Attributemapping_Block_Adminhtml_Attributemapping_Grid_Render_List',
            'filter_condition_callback' => array($this, '_parentAttributeValueFilter')
        ));
         $this->addExportType('*/*/exportCsv', Mage::helper('attributemapping')->__('CSV'));
         $this->addExportType('*/*/exportXml', Mage::helper('attributemapping')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _parentAttributeValueFilter($collection, $column) {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $this->getCollection()->getSelect()->where(
                "parent_attribute_value_id = ?"
                , $value);
        return $this;
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('attributemapping_id');
        $this->getMassactionBlock()->setFormFieldName('attributemapping');
        return $this;
    }
}
