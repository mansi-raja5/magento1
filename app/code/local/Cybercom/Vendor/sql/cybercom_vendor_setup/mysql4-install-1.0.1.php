<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
        ->addColumn(
          $installer->getTable('cybercom_vendor/vendordetail'),
          'status',
             array(
        'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable'  => true,
        'comment'   => 'Status'
    ));
$installer->endSetup();