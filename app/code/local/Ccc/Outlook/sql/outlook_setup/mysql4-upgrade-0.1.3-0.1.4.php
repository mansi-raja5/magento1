<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `ccc_order_mail` ADD `status_processed` TINYINT NOT NULL DEFAULT '0' AFTER `automail`;");

$installer->endSetup();