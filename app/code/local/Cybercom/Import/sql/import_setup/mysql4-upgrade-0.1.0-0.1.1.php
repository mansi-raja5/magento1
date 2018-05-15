<?php
 
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL  ,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
");


$installer->endSetup(); 