<?php

class Cybercom_Import_Model_Process extends Cybercom_Import_Model_New_Abstract
{
    protected $_type = null;
    protected $_typeData = null;
    protected $_reportExportFileName = "export-pending-process.csv";
  
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
        
        $this->_loadProcessType(); 
        
        if(!$this->_typeData || !isset($this->_typeData['class_name']))
        {
            throw new Exception("Please set valid Entity Type to process import.");
        }
        
        Mage::getModel($this->_typeData['class_name'])->setTypeData($this->_typeData)->importCSV();
        
        return $this;
    }
    
    public function processCsv()
    {

        if(!$this->_type)
        {
            throw new Exception("Please set Entity Type to process import.");
        }
        $this->_loadProcessType();
        
        
        if(!$this->_typeData || !isset($this->_typeData['class_name']))
        {
            throw new Exception("Please set valid Entity Type to process import.");
        }
            
        // echo "<pre>";
        // print_r($this->_typeData);
        // exit;
        Mage::getModel($this->_typeData['class_name'])->setTypeData($this->_typeData)->processCsv();
        
        return $this;
    }
    
    public function _loadProcessType()
    {
       if(!$this->_typeData) 
       {
            $this->_typeData = Mage::getModel('import/import_type')->load($this->_type )->getData();
       }
       
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
        
        $this->_type = (int)$entityType;
        
        return $this;
    }
    
    public function getTypeToProcess()
    {   
        $readConnection = Mage::getSingleton("core/resource")->getConnection("core_read");
        $query = "SELECT `type_id`,`name` From `".$this->getResource()->getTable('import/import_type')."` ";
        
        return $readConnection->fetchPairs($query); 
        /*return array(
            Ccc_Import_Model_Process::CATALOG_PRODUCT_SIMPLE_INSERT => Ccc_Import_Model_Process::CATALOG_PRODUCT_SIMPLE_INSERT_TEXT,
            Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_INSERT => Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_INSERT_TEXT,
            Ccc_Import_Model_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE => Ccc_Import_Model_Process::CATALOG_CATEGORY_PRODUCT_MAPPING_SAVE_TEXT,
            Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE => Ccc_Import_Model_Process::CATALOG_PRODUCT_BUNDLE_ITEMS_UPDATE_TEXT,
            Ccc_Import_Model_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE => Ccc_Import_Model_Process::CATALOG_PRODUCT_ATTRIBUTE_UPDATE_TEXT,
            Ccc_Import_Model_Process::CATALOG_PRODUCT_IMAGE_INSERT => Ccc_Import_Model_Process::CATALOG_PRODUCT_IMAGE_INSERT_TEXT
        );*/                                                                                                                                                                                
    }
    
    public function getCountForPendingRecords()
    {
        $this->_loadProcessType();
        $readConnection = Mage::getSingleton("core/resource")->getConnection("core_read");
        $importDataQuery = "SELECT `entity_code` From `".$this->getResource()->getTable('import/import_process')."` WHERE (`start_time` IS NULL OR `start_time` <= '".date("YmdHis", time()-($this->_typeData['record_load_interval']))."') AND (`end_time` IS NULL) AND `type` = ".$this->_type;
        return count($readConnection->fetchCol($importDataQuery));
    }

    public function exportPendingProcess()
    {
        if(!$this->_type)
        {
            throw new Exception("Please set Entity Type to process import.");
        }
        
        $csvContent = $this->getPendingImportProcess();
        if(!$csvContent)
        {
            throw new Exception("No pending data to process");
        }

        $this->openReportExportFile();

        $cnt = 0;
        foreach($csvContent as $key => $_data)
        {
            if($cnt == 0)
            {
                $this->writeInReportExportFile(array_keys($_data));
                $cnt++;
            }

            $this->writeInReportExportFile($_data);
        }

        $this->closeReportExportFile();
        return $this->_reportExportFile;
    }

    public function getPendingImportProcess()
    {
        $finalData = array();
        $query = "SELECT * FROM `import_process` WHERE `type` = ".$this->_type." AND `end_time` IS NULL";
        
        $processList = Mage::getSingleton("core/resource")->getConnection("core_read")->fetchAll($query);

        $i = 0;
        foreach ($processList as $row) 
        {
            $data = json_decode($row['data'],true);
            foreach ($data as $key => $value) 
            {
                $finalData[$i][$key] = $value;
            }
            $i++;
        }
        return $finalData;
    }
}
