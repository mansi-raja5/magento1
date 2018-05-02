<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 //echo $installer->getTable('cybercom_banner/bannerdetail');exit
/**
 * Create table 'cybercom_banner_bannerdetail'
 */

$table = $installer->getConnection()
      ->newTable($installer->getTable('cybercom_banner/bannerdetail'))
      ->addColumn('banner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'identity'  => true,
          'unsigned'  => true,
          'nullable'  => false,
          'primary'   => true,
          ), 'ID')
      ->addColumn('name', Varien_Db_Ddl_Table::TYPE_CLOB, 0, array(
          'nullable'  => false,
          ), 'Name')
      ->addColumn('url', Varien_Db_Ddl_Table::TYPE_TEXT, 0, array(
          'nullable'  => false,
          ), 'Image URL')
      ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
          'nullable'  => false,
          ), 'Sort Order')
      ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, 0, array(
          'nullable'  => true,
          ), 'Image')
      ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
          'nullable'  => false,
          ), 'Status') 
      ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_DATETIME, 0, array(
          'nullable'  => false,
          ), 'Created Date') 
      ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, 0, array(
          'nullable'  => false,
          ), 'Updated Date')                                    
                ;
$installer->getConnection()->createTable($table);
 
$installer->endSetup();