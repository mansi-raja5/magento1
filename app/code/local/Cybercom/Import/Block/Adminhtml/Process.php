<?php
class Cybercom_Import_Block_Adminhtml_Process extends Mage_Adminhtml_Block_Abstract
{
      public function __construct()
      {
          $this->setTemplate('cybercom_import/process.phtml');
      }
      
      public function getEntityTypeForImportProcess()
      {
          $types = Mage::getModel("import/process")->getTypeToProcess();
          return $types[$_SESSION["current_process_type"]];
      }
      
      public function getBackUrl()
      {
        return Mage::helper("adminhtml")->getUrl("import/adminhtml_upload/index");
      }  
      
      public function getImportUrl()
      {
        return Mage::helper("adminhtml")->getUrl("*/*/import");
      }
      
      public function getTotalChunkCount()
      {

          $remainder = $_SESSION["totalpendignRecords"] % ( isset($_SESSION['type_data']['per_load_item'])  ? $_SESSION['type_data']['per_load_item'] : 500 ) ;
          if($remainder > 0)
          {
            return floor($_SESSION["totalpendignRecords"]/( isset($_SESSION['type_data']['per_load_item'])  ? $_SESSION['type_data']['per_load_item'] : 500 ) ) + 1; 
          }          
          return floor($_SESSION["totalpendignRecords"]/  (isset($_SESSION['type_data']['per_load_item'])  ? $_SESSION['type_data']['per_load_item'] : 500) );
      }
      
      public function getCurrentRequestCount()
      {
          return 1;
      }
}