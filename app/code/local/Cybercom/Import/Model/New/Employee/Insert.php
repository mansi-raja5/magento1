<?php
class Cybercom_Import_Model_New_Employee_Insert extends Cybercom_Import_Model_New_Employee_Abstract
{
    protected $_reportProcessFile   = 'employee.csv';
    protected $_reportImportFile    = 'employee.csv';
    protected $_importFile          = 'media/importcsv/employee.csv';
    
    protected $_existingAttributes = array();
    protected $_entityValues = array();
    protected $_writeData = array();
    protected $_productType =  array(
        Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
    );

    protected $_inputType = array(
        'text'          => 'text',
        'textarea'      => 'textarea',
        'date'          => 'date',
        'boolean'       => 'boolean',
        'multiselect'   => 'multiselect',
        'select'        => 'select',
        'price'        => 'price',
    );
     
    public function readCSV()
    {
        $this->openImportFile();

        $cnt = 0;
        while($row = fgetcsv($this->_importFileHandler, 4096, ",", "\""))
        {
            $row = array_map("trim", $row);
            if($cnt == 0)
            {
                $this->_header = array_combine($row, $row);
                
                if(!isset($this->_header["INDEX"]) || !isset($this->_header["NAME"]))
                {
                    throw new Exception("CSV file must contain 'INDEX','name' columns in header.");
                }
                $cnt++;
            }
            else
            {
                $row = array_combine($this->_header, $row);
                $this->_writeData[] = $row; 

            }
        }

        // echo "<pre>";
        // print_r($this->_writeData);
        // exit;
        $this->closeImportFile();

        if(!$this->_writeData)
        {
            throw new Exception(__FUNCTION__." : CSV file is empty.");
        }

        return $this;
    }
    
    public function verifyCSV()
    {
        
        $this->loadAllEmployee();
        //$this->loadExistingAttributes();
        //$this->loadEntityValues();
        
        $this->writeInReportProcessFile(array("INDEX", "NAME", "Status", "Message"));
        foreach($this->_writeData as $row)
        {
            try
            {
                if(!$row['INDEX'])
                {
                    continue;
                }

                $this->setCurrentRow($row);
                
                if(!trim($row['NAME']))
                {
                    throw new Exception('NAME is not valid');
                }
                
                $result = array(
                    $row['INDEX'],
                    $row['NAME'],
                    'success',
                    ''
                );

                $this->writeInReportProcessFile($result);
                $this->_dataFinal[] = $row;
            }
            catch(Exception $e)
            {   
                $result = array(
                   $row['INDEX'],
                   $row['NAME'],
                    'failure',
                    $e->getMessage()
                );
                $this->writeInReportProcessFile($result);
            }
        }
        return $this;
    }

    public function createAttribute()
    {
        $row = $this->getCurrentRow();
        
        if(!count($row))
        {
            return $this;
        }
        
        $installer = Mage::getResourceModel('catalog/setup', 'catalog_setup'); 
        $installer->startSetup();
        
        if($row['input'] == 'text')
        {
            $this->_createTextAttribute($installer,$row['attribute_code'],$row);              
        }
        elseif($row['input'] == 'textarea')
        {
            $this->_createTextareaAttribute($installer,$row['attribute_code'],$row);
            
        }
        elseif($row['input'] == 'date')
        {
            $this->_createDateAttribute($installer,$row['attribute_code'],$row);
        }
        elseif($row['input'] == 'boolean')
        {
            $this->_createBooleanAttribute($installer,$row['attribute_code'],$row);
        }
        elseif($row['input'] == 'multiselect')
        {
            $this->_createMultiselectAttribute($installer,$row['attribute_code'],$row);
        }
        elseif($row['input'] == 'select')
        {
            $this->_createSelectAttribute($installer,$row['attribute_code'],$row);
        }
        elseif($row['input'] == 'price')
        {
            $this->_createPriceAttribute($installer,$row['attribute_code'],$row);
        }
  
        
        $installer->endSetup();
        
        return $this;
    }
    
    public function createEmployeeRecord()
    {
        $row = $this->getCurrentRow();

        
        if(!count($row))
        {
            return $this;
        }
        
        $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");    

        $insertQuery = "INSERT INTO `employee` (`name`) VALUES ('".$row['NAME']."')"; 
        
        

        $writeConnection->query($insertQuery);
        
        return $this;        

    }

    protected function _createDateAttribute($installer,$attributeCode,$attributeData)
    {  
        $attributeData['group']                     = isset($attributeData['group'])?$attributeData['group']:'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'eav/entity_attribute_backend_datetime';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = 'date';
        $attributeData['type']                      = 'datetime';
        $attributeData['class']                     = 'validate-date';
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && (int)$attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && (int)$attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }

        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData); 
        return $this;
    }
    
    protected function _createBooleanAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = isset($attributeData['group'])?$attributeData['group']:'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = 'select';
        $attributeData['type']                      = 'int';
        $attributeData['length']                    = 11;
        $attributeData['source']                    = (isset($attributeData['source']) & $attributeData['source'])?$attributeData['source']:'eav/entity_attribute_source_boolean';
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && (int)$attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && (int)$attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }
                
        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $attribute_set_id   = $setup->getAttributeSetId('catalog_product', 'Default');
            $attribute_group_id = $setup->getAttributeGroupId('catalog_product', $attribute_set_id, $attributeData['group']);
            $attribute_id       = $setup->getAttributeId('catalog_product', $attributeCode);
            $setup->addAttributeToSet('catalog_product',$attribute_set_id, $attribute_group_id, $attribute_id);
        }
    
        return $this;
    }
    
    protected function _createMultiselectAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = isset($attributeData['group'])?$attributeData['group']:'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'eav/entity_attribute_backend_array';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = isset($attributeData['input'])?$attributeData['input']:'multiselect';
        $attributeData['type']                      = 'varchar';
        $attributeData['length']                    = 255;
        $attributeData['source']                    = (isset($attributeData['source']) & $attributeData['source'])?$attributeData['source']:'';
        $attributeData['option']                    = (isset($attributeData['option']) & $attributeData['option'])?$attributeData['option']:array();
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && (int)$attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && (int)$attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }
                
        
       $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        
        return $this;
    }
    
    protected function _createSelectAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = isset($attributeData['group'])?$attributeData['group']:'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = isset($attributeData['input'])?$attributeData['input']:'select';
        $attributeData['type']                      = 'int';
        $attributeData['length']                    = 11;
        $attributeData['source']                    = (isset($attributeData['source']) & $attributeData['source'])?$attributeData['source']:'';
        $attributeData['option']                    = (isset($attributeData['option']) & $attributeData['option'])?$attributeData['option']:array();
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && (int)$attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && (int)$attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }       
        
        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        
        return $this;
    }
    
    protected function _createTextareaAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = 'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = isset($attributeData['input'])?$attributeData['input']:'textarea';
        $attributeData['type']                      = 'varchar';
        $attributeData['length']                    = 255;
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && $attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && $attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }

        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        
        return $this;
    }
    
    protected function _createTextAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = 'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = isset($attributeData['input'])?$attributeData['input']:'text';
        $attributeData['type']                      = 'varchar';
        $attributeData['length']                    = 255;
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && $attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && $attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }

        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        
        return $this;
    }
    
    protected function _createPriceAttribute($installer,$attributeCode,$attributeData)
    {
        $attributeData['group']                     = 'General';
        $attributeData['backend']                   = isset($attributeData['backend'])?$attributeData['backend']:'';
        $attributeData['frontend']                  = isset($attributeData['frontend'])?$attributeData['frontend']:'';
        $attributeData['label']                     = isset($attributeData['label'])?$attributeData['label']:$attributeCode;
        $attributeData['input']                     = isset($attributeData['input'])?$attributeData['input']:'price';
        $attributeData['type']                      = 'decimal';
        $attributeData['length']                    = 11;
        $attributeData['source']                    = (isset($attributeData['source']) & $attributeData['source'])?$attributeData['source']:'';
        $attributeData['option']                    = (isset($attributeData['option']) & $attributeData['option'])?$attributeData['option']:array();
        $attributeData['global']                    = Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL;
        $attributeData['visible']                   = (isset($attributeData['visible']) && (int)$attributeData['visible'] == 1) ? true:false;
        $attributeData['required']                  = (isset($attributeData['required']) && (int)$attributeData['required'] == 1) ? true:false;
        $attributeData['user_defined']              = true;
        if($attributeData['entity_type'] == 'catalog_product')
        {
            $attributeData['apply_to'] = join(",",$this->_productType);
        }
        $attributeData['input_renderer']            = '';
        $attributeData['visible_on_front']          = (isset($attributeData['visible_on_front']) && (int)$attributeData['visible_on_front'] == 1) ? true:false;
        if($attributeData['entity_type'] == 'catalog_category')
        {
            $attributeData['used_in_product_listing']   = (isset($attributeData['used_in_product_listing']) && (int)$attributeData['used_in_product_listing'] == 1) ? true:false;
        }

        $installer->addAttribute($attributeData['entity_type'], $attributeCode, $attributeData);
        
        return $this;
    }
    
    public function importCsv()
    {
        $processModel =  Mage::getModel("import/process");
        $uniqueTime   = date("Y-m-d H:i:s");

        $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
        $readConnection = Mage::getSingleton("core/resource")->getConnection("core_read");

        $updateQuery = "UPDATE `".$processModel->getResource()->getTable('import/import_process')."` SET `start_time` = '".$uniqueTime."' 
        WHERE (`start_time` IS NULL OR `start_time` <= '".date("Y-m-d H:i:s", time()-($this->_typeData['record_load_interval']))."') 
        AND `type` = ".$this->_typeData['type_id']." 
        AND (`end_time` IS NULL) 
        LIMIT ".$this->_typeData['per_load_item']; 

        $writeConnection->query($updateQuery);
 
        $importDataQuery = "SELECT * From `".$processModel->getResource()->getTable('import/import_process')."` 
        WHERE (`start_time` = '".$uniqueTime."') AND (`end_time` IS NULL) AND `type` = ".$this->_typeData['type_id'];

        $importData = $readConnection->fetchAll($importDataQuery);


        if(!count($importData))
        {
            throw new Exception("No data found for import process.");
        }
       
        $this->loadAllEmployee();
        //$this->loadExistingAttributes();
        // $this->loadEntityValues();
        $this->openReportImportFile();
        
        $this->writeInReportImportFile(array("INDEX", "NAME", "Message"));
        
        foreach($importData as $_row)
        {
            try
            {
                if(date("Y-m-d H:i:s", strtotime($_row["start_time"])) < date("Y-m-d H:i:s", time()-($this->_typeData['record_load_interval'])))
                {
                    continue;
                }
                
                $this->setCurrentRow(json_decode($_row['data'],1));

                $this->createEmployeeRecord();
                
                $updateQuery = "UPDATE `".$processModel->getResource()->getTable('import/import_process')."` SET `end_time` = '".date("Y-m-d H:i:s")."' WHERE `start_time` = '".$uniqueTime."' AND `entity_code` = '".addslashes($_row["entity_code"])."' AND `type` = ".$this->_typeData['type_id']; 
                $writeConnection->query($updateQuery);
                
                $currentRow = $this->getCurrentRow();
                $result = array(
                   
                   $currentRow['INDEX'],
                   $currentRow['NAME'],
                    "success",
                    ""
                );
                $this->writeInReportImportFile($result);
                

            }
            catch(Exception $e)
            {
                $currentRow = $this->getCurrentRow();
                $result = array(
                   $currentRow['INDEX'],
                   $currentRow['NAME'],
                    "failure",
                    $e->getMessage()
                );
                $this->writeInReportImportFile($result);
            }
        }  
        
        $this->closeReportImportFile(); 
        return $this;
    }

    public function loadExistingAttributes()
    {
        if(!$this->_existingAttributes)
        {
            $this->_existingAttributes  = Mage::getSingleton("core/resource")->getConnection("core_read")->fetchPairs("SELECT CONCAT(`attribute_code`,'||',`entity_type_id`),`attribute_id` as attribute_code FROM `eav_attribute`");      
        }
        return $this; 
    }

    public function loadEntityValues()
    {
        if(!$this->_entityValues)
        {
            $this->_entityValues  = Mage::getSingleton("core/resource")->getConnection("core_read")->fetchPairs("SELECT `entity_type_code`,`entity_type_id` FROM `eav_entity_type`");   
        }
        return $this; 
    }
}
?>