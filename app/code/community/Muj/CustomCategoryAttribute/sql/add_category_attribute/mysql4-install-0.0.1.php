<?php
$this->startSetup();
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'custom_attribute', array(
    'group'    => 'General Information',
    'type'     => 'int',
    'label'    => 'Popular Category',
    'input'    => 'select',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => 0,
    'source' => 'eav/entity_attribute_source_boolean'
));
 
$this->endSetup();