<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `ccc_order_mail` ADD `no_ack` INT(11) NOT NULL AFTER `is_downloaded`;");


$installer->endSetup();