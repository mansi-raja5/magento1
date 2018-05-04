<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
          ->addColumn(
          $installer->getTable('cybercom_banner/bannergroup'),
            'parent_id',
              array(
              'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
              'nullable'  => true,
              'comment'   => 'parent id of banner group'
          )
      );     
 
$installer->endSetup();