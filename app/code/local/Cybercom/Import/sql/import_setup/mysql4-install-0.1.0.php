<?php
$installer = $this;

$installer->startSetup();


$installer->run("
DROP TABLE IF EXISTS `import_process_type`;
CREATE TABLE IF NOT EXISTS `import_process_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `per_load_item` int(11) DEFAULT NULL,
  `import_file_name` varchar(255) NOT NULL,
  `is_processing` tinyint(11) DEFAULT NULL,
  `class_name` varchar(255) NOT NULL,
  `record_load_interval` int(11) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");


$installer->run("
DROP TABLE IF EXISTS `import_process`;
CREATE TABLE `import_process` (
  `id` int(11)  NOT NULL primary key auto_increment,
  `entity_code` VARCHAR( 255 ) NOT NULL,
  `data` LONGTEXT NOT NULL,  
  `type` int(11) NOT NULL,
  `start_time` datetime NULL,
  `end_time` datetime NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
?>