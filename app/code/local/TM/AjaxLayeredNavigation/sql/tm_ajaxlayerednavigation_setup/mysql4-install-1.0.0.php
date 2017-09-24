<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    
    DROP TABLE IF EXISTS {$this->getTable('ajaxlayerednavigation/filters')};
    CREATE TABLE {$this->getTable('ajaxlayerednavigation/filters')} (
      `filters_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `attribute_id` smallint(5) unsigned NOT NULL,
      `f_position` int(10) unsigned DEFAULT NULL,
      `display_type` tinyint(1) DEFAULT NULL,
      `sort` tinyint(1) DEFAULT NULL,
      `order` tinyint(1) unsigned DEFAULT NULL,
      `show_quantities` tinyint(1) DEFAULT NULL,
      `show_list` tinyint(1) DEFAULT NULL,
      `show_product` tinyint(1) DEFAULT NULL,
      PRIMARY KEY (`filters_id`),
      KEY `FK_tm_ajaxlayerednavigation_filters_1` (`attribute_id`),
      CONSTRAINT `FK_tm_ajaxlayerednavigation_filters_1` 
        FOREIGN KEY (`attribute_id`)
        REFERENCES {$this->getTable('eav/attribute')} (`attribute_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('ajaxlayerednavigation/options')};
    CREATE TABLE {$this->getTable('ajaxlayerednavigation/options')} (
      `foption_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `option_id` int(10) unsigned NOT NULL,
      `filters_id` int(10) unsigned NOT NULL,
      `category_title` varchar(60) DEFAULT NULL,
      `category_description` text,
      `category_image` varchar(45) DEFAULT NULL,
      `product_page_image` varchar(45) DEFAULT NULL,
      `layered_image` varchar(45) DEFAULT NULL,
      `position` int(11) DEFAULT '0',
      PRIMARY KEY (`foption_id`),
      KEY `FK_tm_ajaxlayerednavigation_options_1` (`filters_id`),
      KEY `FK_tm_ajaxlayerednavigation_options_2` (`option_id`),
      CONSTRAINT `FK_tm_ajaxlayerednavigation_options_1`
         FOREIGN KEY (`filters_id`)
         REFERENCES {$this->getTable('ajaxlayerednavigation/filters')} (`filters_id`)
         ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_tm_ajaxlayerednavigation_options_2`
         FOREIGN KEY (`option_id`)
         REFERENCES {$this->getTable('eav/attribute_option')} (`option_id`) 
         ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    
    DROP TABLE IF EXISTS {$this->getTable('ajaxlayerednavigation/range')};
    CREATE TABLE {$this->getTable('ajaxlayerednavigation/range')} (
      `range_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `category_id` int(10) unsigned NOT NULL,
      `category_name` varchar(60) NOT NULL,
      `range` varchar(60) NOT NULL,
      PRIMARY KEY (`range_id`),
      KEY `FK_tm_ajaxlayerednavigation_range_1` (`category_id`),
      CONSTRAINT `FK_tm_ajaxlayerednavigation_range_1`
         FOREIGN KEY (`category_id`)
         REFERENCES {$this->getTable('catalog/category')} (`entity_id`)
         ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    
    
");

$installer->endSetup();
