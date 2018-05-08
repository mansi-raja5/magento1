<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
 
/**
 * Create table 'cybercom_item_itemimages'
 */

$table = $installer->getConnection()
    ->newTable($installer->getTable('cybercom_item/itemimages'))
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'RefId of idemdetails')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 0, array(
        'nullable'  => false,
        ), 'Image Path')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 0, array(
        'nullable'  => false,
        ), 'Image label')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, 0, array(
        'nullable'  => false,
        ), 'sort order')
    ->addColumn('small_image', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
        'nullable'  => false, 'default'  => 0
        ), 'small_image')
    ->addColumn('thumbnail', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
        'nullable'  => false, 'default'  => 0
        ), 'thumbnail')
    ->addColumn('exclude', Varien_Db_Ddl_Table::TYPE_BOOLEAN, 0, array(
        'nullable'  => false, 'default'  => 0
        ), 'exclude');

$installer->getConnection()->createTable($table);
 
$installer->endSetup();