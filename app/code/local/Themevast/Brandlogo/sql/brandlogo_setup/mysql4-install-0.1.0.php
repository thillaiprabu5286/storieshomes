<?php
$installer = $this;

$installer->startSetup();

$installer->run("

        -- DROP TABLE IF EXISTS {$this->getTable('brandlogo')};
        CREATE TABLE {$this->getTable('brandlogo')} (
          `brandlogo_id` int(11) unsigned NOT NULL auto_increment,
          `title` varchar(255) NOT NULL default '',
          `link` varchar(255) NOT NULL default '',
          `image` varchar(255) NOT NULL default '',
          `description` text NOT NULL default '',
          `status` smallint(6) NOT NULL default '0',
          `stores` varchar(255) NOT NULL default '',
          `created_time` datetime NULL,
          `update_time` datetime NULL,
          PRIMARY KEY (`brandlogo_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();
