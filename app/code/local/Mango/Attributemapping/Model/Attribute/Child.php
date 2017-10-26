<?php

class Mango_Attributemapping_Model_Attribute_Child {

    static public function getOptionArray() {
        $_fields = Mage::helper("attributemapping")->getAttributesWithMapping();
        $_children = array();
        foreach ($_fields as $_field) {
            $_children[] = $_field[1];
        }
        if (count($_children)) {
            $_filter = join("','", $_children);
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = "SELECT attribute_id, frontend_label FROM " . $resource->getTableName('eav/attribute') . " att "
                    . " inner join " . $resource->getTableName('eav/entity_type') . " ent "
                    . " on ent.entity_type_id = att.entity_type_id "
                    . " where entity_type_code =  'catalog_product' "
                    . " and attribute_code in ( '" . $_filter . "' );";
            $results = $readConnection->fetchAll($query);
            $_x = array();
            foreach ($results as $result) {
                $_x[$result["attribute_id"]] = $result["frontend_label"];
            }
            return $_x;
        }
        return array();
    }

}
