<?php

class Cybercom_Import_Adminhtml_ProcessController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() 
    {  
        try
        {
            if(!$type = $this->getRequest()->getParam("type", 0))
            {
                throw new Exception("invalid data posted.");
            }
            
            $object = Mage::getModel('import/process')->setProcessType($type);
            
            $typeData = Mage::getModel('import/import_type')->load($type)->getData();

            $totalRecordPendings = $object->getCountForPendingRecords();

            if(!$totalRecordPendings)
            {
                Mage::getSingleton('admin/session')->setIsDeleteExecuted(false);
                throw new Exception("There are no products pending to process.");
            }
            
            $_SESSION["current_process_type"] = $type;
            $_SESSION["type_data"] = $typeData;
            $_SESSION["totalpendignRecords"] = $totalRecordPendings;

            // echo "<pre>";
            // print_r($_SESSION);exit;
            
            $this->loadLayout();
            $this->_setActiveMenu('import/items');
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->getLayout()->getBlock('head')->addJs('import/process.js');
            $this->_addContent($this->getLayout()->createBlock('import/adminhtml_process'));
            $this->renderLayout();
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__($e->getMessage()));
            $this->_redirect("import/adminhtml_type/index");
        }
    }

    public function importAction()
    {
        try
        {  
            header('Content-Type: text/html; charset=utf-8');
            if(!$this->getRequest()->isPost()) 
            {
                throw new Exception("invalid request. no data posted.");
            }
            
            $currentRequest = (int)$this->getRequest()->getParam("current_request", 0);
           
            $object = Mage::getModel('import/process')->setProcessType($_SESSION["current_process_type"]);
            $typeData = Mage::getModel('import/import_type')->load($_SESSION["current_process_type"])->getData();  
            $object->setTypeData($typeData);
            $object->importCsv();
            
            $content = $this->loadLayout()->getLayout()->createBlock('import/adminhtml_process')->toHtml();
            $response = array(
                'responseType' => 0,
                'responseofCurrentRequest' => '<li class="success-msg"><ul><li><span> Completed Execution of '.$currentRequest.'/'.$this->getRequest()->getParam("totalRequest", 0).' Import process with Success.<span></li></ul></li>',
                'content' => '<div id="Process-request-count-'.($currentRequest+1).'"><li class="processing-msg"><ul><li><img height="25px" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/ajax-loader-tr.gif'.'"/><span> Start Execution of '.($currentRequest+1).'/'.$this->getRequest()->getParam("totalRequest", 0).' Import process.<span></li></ul></li>'
            );
            
            if($currentRequest >= $this->getRequest()->getParam("totalRequest", 0))
            {
                unset($_SESSION["current_process_type"]);
                Mage::getSingleton('adminhtml/session')->addSuccess("Import Process Completed");
                $response["redirectToBackUrl"] = Mage::helper("adminhtml")->getUrl("import/adminhtml_type/index");
            }
        } 
        catch (Exception $e)
        {
            $response = array(
                'responseType' => 1,
                'message' => $e->getMessage(),
                'responseofCurrentRequest' => '<li class="error-msg"><ul><li><span> Completed Execution of '.$this->getRequest()->getParam("current_request", 0).'/'.$this->getRequest()->getParam("totalRequest", 0).' Import process with Exception: '.$e->getMessage().$e->getLine().$e->getFile().'.<span></li></ul></li>',
                'content' => '<div id="Process-request-count-'.($this->getRequest()->getParam("current_request", 0)+1).'"><li class="success-msg"><ul><li><span> Start Execution of '.($this->getRequest()->getParam("current_request", 0)+1).'/'.$this->getRequest()->getParam("totalRequest", 0).' Import process.<span></li></ul></li>'
            );
            
            $response["redirectToBackUrl"] = Mage::helper("adminhtml")->getUrl("import/adminhtml_type/index");
        }
            
        $this->getResponse()->setBody(json_encode($response));
    }

    public function pendingDataAction()
    {
        try
        {
            $type = (int)$this->getRequest()->getParam("type", 0);
            $csvFileToDownload = Mage::getModel('import/process')->setProcessType($type)->exportPendingProcess();
            $dir = str_replace(basename($csvFileToDownload), "", $csvFileToDownload);
            
            $fh = new Varien_Io_File();
            $fh->open(array('path' => $dir));
            $fh->streamOpen(basename($csvFileToDownload), "r");

            $this->_prepareDownloadResponse(basename($csvFileToDownload), $fh->read(basename($csvFileToDownload)));
        }
        catch(Exception $e)
        {
            $file = Mage::getModel('import/process')->_getReportExportFile();
             
            if(file_exists($file) && !is_dir($file))
            {
                @unlink($file);
            }
            
            $dir = str_replace(basename($file), "", $file);
            
            $handler = new Varien_Io_File();
            $handler->setAllowCreateFolders(true);
            $handler->open(array('path' => $dir));
            $handler->streamOpen(basename($file), "a");
            
            if($handler)
            {   
                $handler->streamWriteCsv(array($e->getMessage()));
                
                $handler->streamClose();
                $fh = new Varien_Io_File();
                $fh->open(array('path' => $dir));
                $fh->streamOpen(basename($file), "r");

                $this->_prepareDownloadResponse(basename($file), $fh->read(basename($file)));
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addError("unable to write");
                $this->_redirect('*/*/index');
                return;
            }
        }
    }

    public function clearPendingProcessAction()
    {
        try
        {
            $id  =  $this->getRequest()->getParam('type',0);
            $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
            $query = "UPDATE `import_process` SET `start_time` = NULL  WHERE `type` = ".$id." AND `end_time` IS NULL";
            $result = $writeConnection->query($query);
            $affectedRows = $result->rowCount();
            if($affectedRows > 0)
            {
                Mage::getSingleton('adminhtml/session')->addSuccess("Clear Pending Process Success");
            }
            else
            {
                throw new Exception("No data found for clear pending process.");
            }
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect("import/adminhtml_type/index");
    }

}
