<?php

class Cybercom_Import_Model_Import_Process1111111111111111 extends Mage_Core_Model_Abstract
{
    protected $_type = null;
    
    const IMPORT_NUMBER_OF_PRODUCT_PER_REQUEST = 100;
    const TIME_INTERVAL_IN_SECONDS_FOR_PROCESS_RECORDS = 100; // 10 mins
    
    const CATALOG_PRODUCT_SIMPLE_INSERT = 1;
    const CATALOG_PRODUCT_SIMPLE_INSERT_TEXT = "Insert Simple Products";
    const CATALOG_PRODUCT_SIMPLE_INSERT_CLASS = "import/product_simple";
    
    const CATALOG_PRODUCT_BUNDLE_INSERT = 2;
    const CATALOG_PRODUCT_BUNDLE_INSERT_TEXT = "Insert Bundle Products";
    const CATALOG_PRODUCT_BUNDLE_INSERT_CLASS = "import/product_bundle";
    
    const CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE = 3;
    const CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_TEXT = "Save Category Product Mapping";
    const CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_CLASS = "import/product_categoryProductMapping";
    
    const CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE       = 4;
    const CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_TEXT  = "Update Bundle Items";
    const CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_CLASS = "import/product_update_bundleItems";
    
    const CATALOG_PRODUCT_ATTRIBUTE_UPDATE          = 5;
    const CATALOG_PRODUCT_ATTRIBUTE_UPDATE_TEXT     = "Update Product Attribute";
    const CATALOG_PRODUCT_ATTRIBUTE_UPDATE_CLASS    = "import/product_update_attribute";
    
    const CATALOG_PRODUCT_IMAGE_INSERT          = 6;
    const CATALOG_PRODUCT_IMAGE_INSERT_TEXT     = "Insert Product Images";
    const CATALOG_PRODUCT_IMAGE_INSERT_CLASS    = "import/product_image";
    
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('import/process');
    }
   
    public function importCSV()
    {
        if(!$this->_type)
        {
            throw new Exception("Please set Process Type before Import");
        }
        
        if($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT_CLASS)->importCSV();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT_CLASS)->importCSV();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_CLASS)->importCSV();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_CLASS)->importCSV();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE_CLASS)->importCSV();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT_CLASS)->importCSV();
        }
        
        return $this;
    }
    
    public function processCsv()
    {
        if(!$this->_type)
        {
            throw new Exception("Please set Entity Type to process import.");
        }
        
        if($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT_CLASS)->processCsv();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT_CLASS)->processCsv();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_CLASS)->processCsv();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_CLASS)->processCsv();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE_CLASS)->processCsv();
        }
        elseif($this->_type == Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT)
        {
            Mage::getModel(Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT_CLASS)->processCsv();
        }
        return $this;
    }
    
    public function setProcessType($entityType)
    {
        $entityType = (int)$entityType;
        if(!$entityType)
        {
            throw new Exception("Entity Type is not set.");
        }
        elseif(!array_key_exists($entityType, $this->getTypeToProcess()))
        {
            throw new Exception("Entity Type is not valid.");
        }
        
        echo $this->_type = (int)$entityType; exit;
        
        return $this;
    }
    
    public function getTypeToProcess()
    {
        return array(
            Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT => Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_SIMPLE_INSERT_TEXT,
            Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT => Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_INSERT_TEXT,
            Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE => Ccc_Import_Model_Import_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_TEXT,
            Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE => Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_TEXT,
            Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE => Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE_TEXT,
            Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT => Ccc_Import_Model_Import_Process::CATALOG_PRODUCT_IMAGE_INSERT_TEXT
        );
    }
    
    public function getCountForPendingRecords()
    {
        $readConnection = Mage::getSingleton("core/resource")->getConnection("core_read");
        $importDataQuery = "SELECT `entity_code` From `".$this->getResource()->getTable('import/import_process')."` WHERE (`start_time` IS NULL OR `start_time` <= '".date("YmdHis", time()-(Ccc_Import_Model_Import_Process::TIME_INTERVAL_IN_SECONDS_FOR_PROCESS_RECORDS))."') AND (`end_time` IS NULL) AND `type` = ".$this->_type;
        return count($readConnection->fetchCol($importDataQuery));
    }
}
