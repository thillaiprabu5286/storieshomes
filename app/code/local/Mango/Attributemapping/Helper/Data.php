<?php

class Mango_Attributemapping_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAttributesWithMapping() {
        $_config = Mage::getStoreConfig("attributemapping/settings/attributes");
        $_data = str_getcsv($_config, "\n");
        foreach ($_data as &$_row) {
            $_info = str_getcsv($_row);
            if ( is_array($_info) && count($_info)==2)
                $_row = $_info;
        }
        if (count($_data))
            return $_data;
        return false;
    }

}
