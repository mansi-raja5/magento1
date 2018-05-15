<?php

class Cybercom_Import_Model_Mysql4_Import_Type extends Mage_Core_Model_Mysql4_Abstract
{
    const IS_PROCCESSING_YES =1;
    const IS_PROCCESSING_YES_TEXT='Yes';
    const IS_PROCCESSING_NO=2;
    const IS_PROCCESSING_NO_TEXT='No'; 
     
    public function _construct()
    {    
        $this->_init('import/import_type', 'type_id');
    }
    
    public function getTypeOption()
    {
        return array(
            self::IS_PROCCESSING_YES => self::IS_PROCCESSING_YES_TEXT,
            self::IS_PROCCESSING_NO => self::IS_PROCCESSING_NO_TEXT
        );
    }
}