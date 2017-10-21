<?php
/* @category    Mango
 * @package     Mango_Attributesmapping
 */
class Mango_Attributemapping_Block_Catalog_Layer_Filter_Parent extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_filterModelName = 'attributemapping/catalog_layer_filter_parent';
    }
    
    protected function _prepareFilter()
    {
        $this->_filter->setAttributeModel($this->getAttributeModel());
        return $this;
    }
}
