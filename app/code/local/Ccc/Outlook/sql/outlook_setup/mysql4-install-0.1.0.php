<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `ccc_order_mail`;
    CREATE TABLE `ccc_order_mail` (
     `entity_id` int(11) NOT NULL AUTO_INCREMENT,
     `message_id` varchar(255) NOT NULL,
     `from_email` varchar(255) NOT NULL,
     `recipients` varchar(255) NOT NULL,
     `subject` varchar(255) NOT NULL,
     `content` text NOT NULL,
     `order_id` int(11) NOT NULL,
     `read` int(11) NOT NULL,
     `brand_id` int(11) NOT NULL,
     `received_date` datetime NOT NULL,
     `create_date` datetime NOT NULL,
     PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1
");

$installer->run("ALTER TABLE `ccc_order_mail` ADD `has_attachment` INT(11) NOT NULL DEFAULT '2' AFTER `create_date`, ADD `is_downloaded` INT(11) NOT NULL DEFAULT '2' AFTER `has_attachment`;");


$installer->run("
    DROP TABLE IF EXISTS `ccc_order_mail_attachment`;

    CREATE TABLE `ccc_order_mail_attachment` (
     `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
     `entity_id` int(11) NOT NULL COMMENT 'Ref Id of ccc_order_mail',
     `attachment` varchar(255) NOT NULL,
     `content_id` varchar(255) NOT NULL,
     `content_type` varchar(255) NOT NULL,
     `download_date` datetime NOT NULL,
     PRIMARY KEY (`attachment_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1
");

$installer->endSetup(); 