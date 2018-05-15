<?php
class Cybercom_Import_Model_New_Abstract extends Mage_Core_Model_Abstract 
{
    protected $_type = array();
    protected $_reportExportFileHandler = null;
    protected $_reportExportFile = null;
    
    public function _getReportExportFile()
    {
        if(!$this->_reportExportFile)
        {
            $file = Mage::getBaseDir().DS. "media". DS ."exportcsv". DS .$this->_reportExportFileName;
            if(file_exists($file))
            {
                unlink($file);
            }
            $this->_reportExportFile = $file;
        }                                                              

        return $this->_reportExportFile;
    }

    public function openReportExportFile()
    {
        if(!$this->_reportExportFileHandler)
        {
            $file = $this->_getReportExportFile();
            $dir = str_replace(basename($file), "", $file);
            
            $this->_reportExportFileHandler = new Varien_Io_File();
            $this->_reportExportFileHandler->open(array('path' => $dir));
            $this->_reportExportFileHandler->streamOpen(basename($file), "a");

        }

        return $this;
    }

    public function writeInReportExportFile($reportData)
    {
        if(!$this->_reportExportFileHandler)
        {
            throw new Exception('Unable to get report export handler.');
        }

        $this->_reportExportFileHandler->streamWriteCsv($reportData);

        return $this;
    }

    public function closeReportExportFile()
    {
        if($this->_reportExportFileHandler)
        {
            $this->_reportExportFileHandler->streamClose();
        }

        return $this;
    }

}
?>
