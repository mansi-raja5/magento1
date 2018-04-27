<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
        	->addColumn(
         	$installer->getTable('cybercom_vendor/vendordetail'),
          	'status1',
	            array(
			        'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
			        'nullable'  => true,
			        'comment'   => '0=>Disabled, 1 => Enabled'
			    )
			);
			
$installer->endSetup();