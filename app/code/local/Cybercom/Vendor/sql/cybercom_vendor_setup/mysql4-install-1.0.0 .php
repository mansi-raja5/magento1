<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 
/**
 * Create table 'cybercom_vendor_vendordetail'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('cybercom_vendor/vendordetail'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
        'nullable'  => false,
        ), 'Name');
$installer->getConnection()->createTable($table);

/*$installer->run("

-- DROP TABLE IF EXISTS {$installer->getTable('cybercom_vendor/vendordetail')};
CREATE TABLE {$installer->getTable('cybercom_vendor/vendordetail')} (
  `prical_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`prical_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");*/
 
$installer->endSetup();