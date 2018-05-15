<?php

class Cybercom_Import_Model_Import_Type extends Mage_Core_Model_Abstract
{
    const IS_PROCCESSING_YES = 1;
    const IS_PROCCESSING_YES_TEXT = 'Yes';
    const IS_PROCCESSING_NO = 2;
    const IS_PROCCESSING_NO_TEXT = 'No'; 
    
    const TYPE_IMPORT = 'import';
    const TYPE_EXPORT = 'export';
    const TYPE_FORMAT = 'format';
    
    const TYPE_IMPORT_TEXT = 'Import';
    const TYPE_EXPORT_TEXT = 'Export';
    const TYPE_FORMAT_TEXT = 'Format';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('import/import_type');
    }
    
    public function getProcessingOption()
    {
        return array(
            self::IS_PROCCESSING_NO => self::IS_PROCCESSING_NO_TEXT,
            self::IS_PROCCESSING_YES => self::IS_PROCCESSING_YES_TEXT
        );
    }
    public function getTypeOption()
    {
        return array(
            self::TYPE_IMPORT => self::TYPE_IMPORT_TEXT,
            self::TYPE_EXPORT=> self::TYPE_EXPORT_TEXT
        );
    }
    
    public function getAllTypesOptions($type=null){
        $option=array();
        $collections=$this->getCollection();
        /*if($type){
            $collections->addFieldToFilter('type',array('eq'=>$type));
        }*/
        if(count($collections->getData()))
        {
            foreach($collections as $collection)
            {
                $option[$collection->getTypeId()]=$collection->getName();
            }
        }
        return $option;
    }
    
    public function validateClassName($model)
    {
        if(!is_array($model->getData()) && count($model->getData())<1)
        {
            return false;
        }
        if($model->getClassName()){
            $collection=$this->getCollection()->addFieldToFilter('class_name',array('eq'=>$model->getClassName()));
            
            if($model->getTypeId()){
                $collection->addFieldToFilter('type_id',array('neq'=>$model->getTypeId()));
            }
        
            if(count($collection)){
                return true;
            }
            else{
                return false;
            }
        }
        return false;
    }
}
