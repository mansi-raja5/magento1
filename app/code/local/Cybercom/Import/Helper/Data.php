<?php

class Cybercom_Import_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getIntervalTime($type)
    {
        $readConnection  = Mage::getSingleton("core/resource")->getConnection("core_read");
        $importDataQuery = "SELECT * From `".$processModel->getResource()->getTable('import/import_type')."` WHERE `type` = ".$type;

       return $readConnection->fetchAll($importDataQuery);
    }
}