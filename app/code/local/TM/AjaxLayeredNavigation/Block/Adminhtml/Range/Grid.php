<?php
class TM_AjaxLayeredNavigation_Block_Adminhtml_Range_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rangeGrid');
        // This is the primary key of the database
        $this->setDefaultSort('range_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ajaxlayerednavigation/range')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('range_id');
        $this->getMassactionBlock()->setFormFieldName('ranges');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('ajaxlayerednavigation')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('ajaxlayerednavigation')->__('Are you sure?')
        ));

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('range_id', array(
            'header'        => Mage::helper('ajaxlayerednavigation')->__('ID'),
            'type'          => 'number',
            'align'         => 'right',
            'index'         => 'range_id'
        ));

        $this->addColumn('category_name', array(
            'header'        => Mage::helper('ajaxlayerednavigation')->__('Category Name'),
            'align'         => 'left',
            'index'         => 'category_name'
        ));

        $this->addColumn('range', array(
            'header'        => Mage::helper('ajaxlayerednavigation')->__('Range'),
            'align'         => 'left',
            'index'         => 'range',
            'width'         => '350px'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRangeId()));
    }
}
