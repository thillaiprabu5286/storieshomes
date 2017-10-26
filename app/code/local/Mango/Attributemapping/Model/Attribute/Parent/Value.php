<?php

class Mango_Attributemapping_Model_Attribute_Parent_Value {

    static public function getOptionArray() {
        $_fields = Mage::helper("attributemapping")->getAttributesWithMapping();
        $_children = array();
        foreach ($_fields as $_field) {
            $_children[] = $_field[0];
        }
        if (count($_children)) {
            $_filter = join("','", $_children);
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = "SELECT label.option_id as option_id, value FROM " . $resource->getTableName('eav/attribute') . " att "
                    . " inner join " . $resource->getTableName('eav/entity_type') . " ent "
                    . " on ent.entity_type_id = att.entity_type_id "
                    . " inner join " . $resource->getTableName('eav/attribute_option') . " opt "
                    . " on opt.attribute_id = att.attribute_id "
                    . " inner join " . $resource->getTableName('eav/attribute_option_value') . " label "
                    . " on label.option_id = opt.option_id "
                    . " where entity_type_code =  'catalog_product' "
                    . " and label.store_id = 0 "
                    . " and attribute_code in ( '" . $_filter . "' );";
            $results = $readConnection->fetchAll($query);
            $_x = array();
            foreach ($results as $result) {
                $_x[$result["option_id"]] = $result["value"];
            }
            return $_x;
        }
        return array();
    }

}
