<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('attributemapping')};
CREATE TABLE {$this->getTable('attributemapping')} (
  `attributemapping_id` int(11) unsigned NOT NULL auto_increment,
  `parent_attribute_id` smallint(5) NOT NULL,
  `parent_attribute_value_id` int(11) NOT NULL,
  `child_attribute_id` smallint(5) NOT NULL,
  `child_attribute_value_id` int(11) NOT NULL,
  `created_time` datetime,
  `update_time` datetime,
  PRIMARY KEY (`attributemapping_id`),
  UNIQUE KEY (`parent_attribute_id`,`parent_attribute_value_id`,`child_attribute_id`,`child_attribute_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup(); 