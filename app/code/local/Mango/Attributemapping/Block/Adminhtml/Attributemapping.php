<?php
class Mango_Attributemapping_Block_Adminhtml_Attributemapping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_attributemapping';
    $this->_blockGroup = 'attributemapping';
    $this->_headerText = Mage::helper('attributemapping')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('attributemapping')->__('Add Item');
    parent::__construct();
    $this->removeButton("add");
  }
}