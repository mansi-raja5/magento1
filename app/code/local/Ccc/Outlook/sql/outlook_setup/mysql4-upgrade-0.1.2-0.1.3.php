<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE `ccc_order_mail` ADD `mfr_id` INT(11) NULL DEFAULT NULL AFTER `order_status`;");

$installer->run("ALTER TABLE `ccc_order_mail` ADD `automail` DATETIME NULL DEFAULT NULL COMMENT 'IF no replys within 3 days then auto mail will be generated' AFTER `mfr_id`;");

$installer->endSetup();