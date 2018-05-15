<?php

class Cybercom_Import_Model_Mysql4_Process_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('import/import_process');
    }
}