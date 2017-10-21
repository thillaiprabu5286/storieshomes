<?php

class Mango_Attributemapping_Block_Adminhtml_Attributemapping_Grid_Render_List extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected $_values = null;

    protected function _getValues($_attribute_id) {
        if ($this->_values == NULL) {
            $this->_values = array();
            /* get attribute codes 
             * and list of options 
             * for each attribute */
            $_fields = Mage::helper("attributemapping")->getAttributesWithMapping();
            foreach ($_fields as $_field) {
                $_parent_attribute_id = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $_field[0]);
                $attribute = Mage::getSingleton('eav/config')
                        ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $_field[0]);
                if ($attribute->usesSource()) {
                    $options = $attribute->getSource()->getAllOptions(true);
                }
                $_options_array = array();
                foreach ($options as $info) {
                    if ($info['value']) {
                        $_options_array[$info['value']] = $info['label'];
                    }
                }
                $this->_values[$_parent_attribute_id] = $_options_array;
            }
        }
        if (isset($this->_values[$_attribute_id])) {
            return $this->_values[$_attribute_id];
        }
        return array();
    }

    public function renderExport(Varien_Object $row){
        //$_row_key = $row->getData("parent_attribute_id") . "_" . $row->getData("child_attribute_id") . "_" . $row->getData("child_attribute_value_id");
        /*$_row_array = array(
            "parent_attribute_id" => $row->getData("parent_attribute_id"),
            "child_attribute_id" => $row->getData("child_attribute_id"),
            "child_attribute_value_id" => $row->getData("child_attribute_value_id")
        );*/
        $value = $row->getData('parent_attribute_value_ids');
        $_values = array();
        $_select_values = array();
        /* explode contactenated values  attribute_mapping_id:parent_attribute_value_id */
        $_concat_values = explode(",", $value);
        foreach ($_concat_values as $_string) {
            $_x = explode(":", $_string);
            if (count($_x) == 2 && $_x[1] > 0) {/* values array only for items w parent_attribute_value_id > 0 */
                $_values[$_x[0]] = $_x[1];
                $_select_values[] = $_x[1];
            }
        }
        $_values_string = "";
        $_attribute_id = $row->getData("parent_attribute_id");
        $_parent_values = $this->_getValues($_attribute_id);
        /* existing values */
        foreach ($_values as $_id => $_value_id) {
            if (isset($_parent_values[$_value_id])) {
                $_values_string[] = $_parent_values[$_value_id];
            }
        }
        return join(",",$_values_string);
    }
    

    public function render(Varien_Object $row) {
        $_row_key = $row->getData("parent_attribute_id") . "_" . $row->getData("child_attribute_id") . "_" . $row->getData("child_attribute_value_id");
        $_row_array = array(
            "parent_attribute_id" => $row->getData("parent_attribute_id"),
            "child_attribute_id" => $row->getData("child_attribute_id"),
            "child_attribute_value_id" => $row->getData("child_attribute_value_id")
        );
        $value = $row->getData('parent_attribute_value_ids');
        $_values = array();
        $_select_values = array();
        /* explode contactenated values  attribute_mapping_id:parent_attribute_value_id */
        $_concat_values = explode(",", $value);
        foreach ($_concat_values as $_string) {
            $_x = explode(":", $_string);
            if (count($_x) == 2 && $_x[1] > 0) {/* values array only for items w parent_attribute_value_id > 0 */
                $_values[$_x[0]] = $_x[1];
                $_select_values[] = $_x[1];
            }
        }
        $_values_string = "";
        $_attribute_id = $row->getData("parent_attribute_id");
        $_parent_values = $this->_getValues($_attribute_id);
        /* existing values */
        foreach ($_values as $_id => $_value_id) {
            if (isset($_parent_values[$_value_id])) {
                $_values_string.= '<span class="parent-button">' . $_parent_values[$_value_id] . '<a href="#" class="delete-attribute-mapping" data-url="' . $this->getUrl('*/*/delete', array_merge($_row_array, array('parent_attribute_value_id' => $_value_id, 'attributemapping_id' => $_id))) . '" data-value="' . $_value_id . '" title="' . $this->__("Remove") . '">x</a></span>&nbsp;&nbsp;';
            }
        }
        /* select box for new values... */
        $_select = "<select class='attribute_mapping_add_value_select' id='select-new-" . $_row_key . "' style='min-width:100px;'>";
        $_select.='<option value=""> - - -</option>';
        foreach ($_parent_values as $_id => $_label) {
            if (in_array($_id, $_select_values))
                continue;
            $_select.='<option value="' . $_id . '" ';
            $_select.='">' . $_label . '</option>';
        }
        $_select.="</select>";
        $_select.= '&nbsp;&nbsp;<button data="" class="add-new-row-submit" data-select="#select-new-' . $_row_key . '" id="button-new-save-' . $_row_key . '" style="" data-add-url="' . $this->getUrl('*/*/add', $_row_array) . '">' . $this->__("Save") . '</button>';
        $_select.="<span class='attributemapping-loader-adding' style='opacity:0;'>" . $this->__("Adding...") . "</span>";

        $_loader_html = "<div class='cell-ajax-loader' style='display:none;'><div>" . $this->__("Please wait...") . "</div></div>";

        return $_values_string . $_select . $_loader_html;
    }

}

?>