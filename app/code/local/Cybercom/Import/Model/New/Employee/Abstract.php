<?php
class Cybercom_Import_Model_New_Employee_Abstract
{
    protected $_reportProcessFileHandler = '';
    protected $_reportImportFileHandler = '';
    protected $_importFileHandler = '';

    protected $_data = array();
    protected $_header = array();
    protected $_currentSKU = array();
    protected $_currentRow = array();
    protected $_dataFinal = array();
    protected $_typeData = array();

    public function setCurrentRow($row)
    {
        if(!$row)
        {
            throw new Exception(__FUNCTION__." : Row is not valid.");
        }

        $this->_currentRow = $row;
        return $this;
    }

    public function getCurrentRow()
    {
        return $this->_currentRow;
    }
    
    public function setTypeData($typeData)
    {
        if(!$typeData)
        {
            throw new Exception(__FUNCTION__." : Import Type is not valid.");
        }

        $this->_typeData = $typeData;
        return $this;
    }

    public function getTypeData()
    {
        return $this->_typeData;
    }

    public function setCurrentSKU($SKU)
    {
        if(!$SKU)
        {
            throw new Exception(__FUNCTION__." : Product SKU is not valid.");
        }

        $this->_currentSKU = $SKU;
        
        return $this;
    }

    public function getCurrentSKU()
    {
        return $this->_currentSKU;
    }
    
    protected function _getReportProcessFile()
    {
        $file = Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_PROCESS_".$this->_reportProcessFile;
        if(file_exists($file))
        {
            unlink($file);
        }
        return $file;
    }

    public function openReportProcessFile()
    {
        if(!$this->_reportProcessFileHandler)
        {
            $this->_reportProcessFileHandler = fopen($this->_getReportProcessFile(), "a");
        }
        return $this;
    }

    public function writeInReportProcessFile($reportData)
    {
        if(!$this->_reportProcessFileHandler)
        {
            throw new Exception('Unable to get report process handler.');
        }

        fputcsv($this->_reportProcessFileHandler, $reportData);

        return $this;
    }

    public function closeReportProcessFile()
    {
        if($this->_reportProcessFileHandler)
        {
            fclose($this->_reportProcessFileHandler);
        }

        return $this;
    }

    protected function _getReportImportFile()
    {
        return Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_IMPORT_".$this->_reportImportFile;
    }

    public function openReportImportFile()
    {
        if(!$this->_reportImportFileHandler)
        {
            $this->_reportImportFileHandler = fopen($this->_getReportImportFile(), "a");
        }

        return $this;
    }

    public function writeInReportImportFile($reportData)
    {
        Mage::log($this->_reportImportFileHandler,null,"importhandler.log");
        if(!$this->_reportImportFileHandler)
        {
            throw new Exception('Unable to get report import handler.');
        }

        fputcsv($this->_reportImportFileHandler, $reportData);

        return $this;
    }

    public function closeReportImportFile()
    {
        if($this->_reportImportFileHandler)
        {
            fclose($this->_reportImportFileHandler);
        }

        return $this;
    }

    protected function _getImportFile()
    {
        return Mage::getBaseDir().DS.$this->_importFile;
    }

    public function openImportFile()
    {
        if(!$this->_importFileHandler)
        {   
            $this->_importFileHandler = fopen($this->_getImportFile(), "r");
        }

        return $this;
    }

    public function closeImportFile()
    {
        if($this->_importFileHandler)
        {
            fclose($this->_importFileHandler);
        }

        return $this;
    }

    public function processCsv()
    {
        $this->openReportProcessFile();

        $this->loadAllEmployee();

        $this->readCSV();

        $this->verifyCSV();
        
        $this->closeReportProcessFile();
        
        $this->insertCSVIntoTable();

        return $this;
    }
    
    public function insertCSVIntoTable()
    {   
        if(!$this->_dataFinal) {
            throw new Exception(__FUNCTION__." : No data found to insert.");
        }

        $processModel =  Mage::getModel("import/process");
        $readConnection = Mage::getSingleton("core/resource")->getConnection("core_read");
        $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");

        $query = "SELECT `entity_code`,`entity_code` From `".$processModel->getResource()->getTable('import/import_process')."` WHERE `type` = ".$this->_typeData['type_id']." AND (`end_time` IS NULL)"; 
        $existingEntityCodes = $readConnection->fetchPairs($query);

        $insertRecords = array_diff_key($this->_dataFinal, $existingEntityCodes);
        if(!$insertRecords)
        {
            throw new Exception(__FUNCTION__." : Duplicate Records.");
        }
        
        $q = array() ;
        foreach($insertRecords as $uniqueEntityCode => $_data)
        {
            $q[] = '(NULL,"'.addslashes($uniqueEntityCode).'","'.addslashes(json_encode($_data)).'","'.$this->_typeData['type_id'].'",NULL,NULL)';
            
        }

        if(count($q))
        { 
            $_q = array_chunk($q, 1000);
            foreach ($_q as $key => $value)
            {
                $writeConnection->query('INSERT INTO `import_process`(`id`, `entity_code`, `data`, `type`, `start_time`, `end_time`) VALUES '.implode(",", $value));                    
            }
        }

        return $this;
    }
    
    public function validateOptionAndGetUpdatedOption($optionValue)
    {
        $optionValue = trim($optionValue);
        $optionValue = explode("-", $optionValue);
        $optionValue = array_map("trim",$optionValue);
        $optionValue = trim(implode(" - ", $optionValue), " - ");
        $optionValue = explode("/", $optionValue);
        $optionValue = array_map("trim",$optionValue);
        $optionValue = trim(implode(" / ", $optionValue), " / ");
        $optionValue = ucwords($optionValue);
        $optionValue = preg_replace('/\s+/', ' ',$optionValue);
        return $optionValue;
    }
    
    public function loadExistingCategories()
    {
        if(!$this->_existingCategories)
        {
            $query = "SELECT `e`.`entity_id` ,  `e`.`path`
                    FROM `catalog_category_entity` AS `e` 
                    WHERE (`e`.`entity_type_id` = '3')";

            $allcategoriesPathFromSystem = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchPairs($query);
            foreach($allcategoriesPathFromSystem as $categoryId => $path)
            {
                if($categoryId == 1 || $categoryId == 2) { continue; }
                
                $path = explode("/",$path);
                
                $coll = Mage::getModel('catalog/category')->getResourceCollection();
                $coll->addAttributeToSelect('name');
                $coll->addAttributeToFilter('entity_id', array('in' => $path));
                
                $result = array();
                foreach ($coll as $cat)
                {
                    if($cat->getName() == "Root Category") { continue; }
                    $result[$cat->getId()] = $cat->getName();
                }
                
                $data = "";
                foreach($path as $path)
                {
                    if(!isset($result[$path])){ continue; }
                    $data .= $result[$path]."/";
                }
                
                $this->_existingCategories[rtrim($data,'/')] = $categoryId;
            }
        }
        
        return $this;
    }
    
    public function loadAttributes()
    {
        if(!$this->_attributes)
        {
            $this->_attributes  = Mage::getSingleton("core/resource")->getConnection("core_read")->fetchPairs("SELECT `attribute_code`,`attribute_id` FROM `eav_attribute`");        
        }
        return $this->_attributes; 
    }

    public function loadAllEmployee()
    {
        if(!$this->_employees)
        {
            $this->_employees = Mage::getSingleton("core/resource")->getConnection("core_read")->fetchPairs("SELECT * from employee");
        }
        return $this->_employees;
    }
}
?>