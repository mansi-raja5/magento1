<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 //echo $installer->getTable('cybercom_banner/bannerdetail');exit
/**
 * Create table 'cybercom_banner_bannergroup'
 */
// echo $installer->getTable('cybercom_banner/bannergroup');exit;
$table = $installer->getConnection()
      ->newTable($installer->getTable('cybercom_banner/bannergroup'))
      ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'identity'  => true,
          'unsigned'  => true,
          'nullable'  => false,
          'primary'   => true,
          ), 'ID')
      ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
          'nullable'  => false,
          ), 'Name')
      ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT,array(
          'nullable'  => false,
          ), 'Group Description')
      ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 11, array(
          'nullable'  => false,
          ), 'Group Code')
      ->addColumn('height', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
          'nullable'  => false,
          ), 'Height')
      ->addColumn('width', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
          'nullable'  => false,
          ), 'Width');      

$installer->getConnection()->createTable($table);
 
$installer->endSetup();