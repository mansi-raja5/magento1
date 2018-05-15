<?php 
class Cybercom_Import_Block_Adminhtml_Type_Grid_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
            $str = '<a href="'.$this->getUrl('/adminhtml_type/edit', array('type' => $row->getId())).'">Edit</a> | 
                <a href="'.$this->getUrl('/adminhtml_type/uploadcsv', array('type' => $row->getId())).'">Upload CSV</a> | 
                <a href="'.$this->getUrl('/adminhtml_type/read', array('type' => $row->getId())).'">Read CSV</a> | 
                <a href="'.$this->getUrl('*/adminhtml_process/index', array('type' => $row->getId())).'">Import CSV</a>';

           
           if(file_exists( Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."sample". DS .$row->getImportFileName()))
           {
              $str .= '| <a target="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). DS ."importcsv". DS ."sample". DS .$row->getImportFileName().'">Download Sample</a>'; 
           }

           $str .= '| <a href="'.$this->getUrl('*/adminhtml_process/pendingData', array('type' => $row->getId())).'">Download Pending Data</a>';

           $str .= '| <a href="'.$this->getUrl('*/adminhtml_process/clearPendingProcess', array('type' => $row->getId())).'">Clear Pending Process</a>';
                
           if(file_exists( Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_PROCESS_".$row->getImportFileName()))
           {  
              $str .= '| <a target="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). DS ."importcsv". DS ."report". DS ."REPORT_PROCESS_".$row->getImportFileName().'">Download Process Report</a>';
           }
           
           if(file_exists( Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_IMPORT_".$row->getImportFileName()))
           {
              $str .= '| <a target="_blank" href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). DS ."importcsv". DS ."report". DS ."REPORT_IMPORT_".$row->getImportFileName().'">Download Import Report</a>'; 
           }
          return $str;
    }
}

?>

