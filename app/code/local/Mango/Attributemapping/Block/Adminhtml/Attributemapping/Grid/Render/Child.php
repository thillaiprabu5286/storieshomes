<?php

class Mango_Attributemapping_Block_Adminhtml_Attributemapping_Grid_Render_Child extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_attributeswatches_enabled = null;

    protected function _isAttributeswatchesEnabled() {
        if ($this->_attributeswatches_enabled == NULL) {
            $this->_attributeswatches_enabled = Mage::helper('core')->isModuleEnabled('Mango_Attributeswatches');
        }
        return $this->_attributeswatches_enabled;
    }

    public function renderExport(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        return $value;
    }

    
    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());

        /* check if attributeswatches extension is enabled... */
        if ($this->_isAttributeswatchesEnabled()) {
            $_swatch = "";
            if ($row->getData("mode") == 2)/* hex code */ {
                $color = $row->getData("color");
                $_swatch = '&nbsp;&nbsp;<span style="border:solid 1px #000;background-color:#' . $color . ';display:inline-block;width:30px;height:15px;"></span>&nbsp;';
            } else {
                $filename = $row->getData("filename");
                $_swatch = '&nbsp;&nbsp;<span style="border:solid 1px #000;background-image:url(' . Mage::getBaseUrl('media') . 'attributeswatches/' . $filename . ');display:inline-block;width:30px;height:15px;"></span>&nbsp;';
            }

            $value = $value . $_swatch;
        }
        return $value;
    }

}

?>