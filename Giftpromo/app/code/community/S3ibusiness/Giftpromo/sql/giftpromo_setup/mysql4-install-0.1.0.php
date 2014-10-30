<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('giftpromo')};
CREATE TABLE {$this->getTable('giftpromo')} (
  `gift_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default 0,
  `gift_name` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 