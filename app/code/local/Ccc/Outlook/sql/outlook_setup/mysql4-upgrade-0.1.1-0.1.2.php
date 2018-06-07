<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `ccc_order_mail` ADD `order_status` VARCHAR(255) NOT NULL AFTER `no_ack`;");


$installer->endSetup();