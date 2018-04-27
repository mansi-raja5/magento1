<?php
$installer = $this;
$installer->startSetup();
//echo $installer->getTable('cybercom_vendor/price');exit;
	
$table = $installer->getConnection()
    ->newTable($installer->getTable('cybercom_vendor/price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
        ), 'Product Id')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
        'nullable'  => false,
        ), 'Id from vendordetail table')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_FLOAT, 11, array(
        'nullable'  => false,
        ), 'Vendor wise product price');
$installer->getConnection()->createTable($table);
		
$installer->endSetup();