<?php

class Cybercom_Import_Model_Import extends Mage_Core_Model_Abstract
{   
    public function _construct()
    {
        parent::_construct();
        $this->_init('import/import');
    }
}