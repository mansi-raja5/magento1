<?php

class Cybercom_Import_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('import/import', 'import_id');
    }
}