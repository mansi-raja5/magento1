<?php
class Cybercom_Import_Adminhtml_TypeController extends Mage_Adminhtml_Controller_Action
{	
    public function indexAction() 
    {   
        $this->loadLayout();
        $this->_setActiveMenu('cybercom/import/items');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('import/adminhtml_type'));
        $this->renderLayout();
    }

    public function uploadcsvAction() 
    {   
        $id  =  $this->getRequest()->getParam('type',0);
        $type  =  Mage::getModel('import/import_type')->load($id);
        if ($type->getId() || $id == 0) 
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) 
            {
                $type->setData($data);
            }
            Mage::register('current_type', $type);
        }
        $this->loadLayout();
        $this->_setActiveMenu('cybercom/import/items');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('import/adminhtml_upload_edit'))
        ->_addLeft($this->getLayout()->createBlock('import/adminhtml_upload_edit_tabs'));

        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id  =  $this->getRequest()->getParam('type',0);
        $type  =  Mage::getModel('import/import_type')->load($id);
        if ($type->getId() || $id == 0) 
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) 
            {
                $type->setData($data);
            }
            Mage::register('current_type', $type);
        }
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('import/adminhtml_type_edit'))
        ->_addLeft($this->getLayout()->createBlock('import/adminhtml_type_edit_tabs'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        try
        {
            $data = $this->getRequest()->getPost();
            if ($data) {
                $id         = $this->getRequest()->getParam('type');
                $typeModel  = Mage::getModel('import/import_type');
                $typeData   = $this->getRequest()->getPost('import_type',array());
                if($id)
                {
                    $typeModel->load($id);
                    $data = $typeModel->getData();
                    $data["name"]                   = (isset($typeData["name"])) ? trim($typeData["name"]) : '';
                    $data["type"]                   = Cybercom_Import_Model_Import_Type::TYPE_IMPORT;
                    $data["cron_time"]              = (isset($typeData["cron_time"])) ? $typeData["cron_time"]:'';
                    $data["record_load_interval"]   = (isset($typeData["record_load_interval"])) ? $typeData["record_load_interval"] : '';
                    $data["per_load_item"]          = (isset($typeData["per_load_item"])) ? $typeData["per_load_item"] : '';
                    $data["is_processing"]          = (isset($typeData["is_processing"])) ? $typeData["is_processing"] : '';
                    $data["class_name"]             = (isset($typeData["class_name"])) ? trim($typeData["class_name"]) : '';
                    $data["import_file_name"]       = (isset($typeData["import_file_name"])) ? trim($typeData["import_file_name"]) : '';
                    $data["note"]                   = (isset($typeData["note"])) ? trim($typeData["note"]) : '';

                    $typeModel->setData($data);
                }
                else
                {
                    $typeData["type"]           = Cybercom_Import_Model_Import_Type::TYPE_IMPORT;
                    $typeData["created_date"]   = date("Y-m-d H:i:s");
                    $typeModel->setData($typeData);
                }

                if($typeModel->validateClassName($typeModel))
                {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Class Name should be unique')); 

                    if($this->getRequest()->getParam('back'))
                    {
                        $this->_forward('edit');
                    }
                    else
                    {
                        $this->_forward('index');    
                    }
                    return;
                }

                $typeModel=$typeModel->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Import Type was successfully saved')); 
                if($this->getRequest()->getParam('back'))
                {
                    //$this->_forward('edit');
                    $this->_redirect('*/*/edit',array('type'=>$typeModel->getTypeId()));
                }
                else
                {
                    $this->_redirect('*/*/index');   
                }
            }

            
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage())
            ->setProductData($data);
            $redirectBack = true;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $redirectBack = true;
        }
        
         $this->_redirect('*/*/index'); 
    }

    public function deleteAction()
    {
        $id=$this->getRequest()->getParam('type');
        $typeModel=Mage::getModel('import/import_type')->load($id);
        if($typeModel->getId())
        {
            $typeModel->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__(
            'Record were successfully deleted.'));
        }
        $this->_forward('index');
    }

    public function massDeleteAction()
    {
        $Ids = $this->getRequest()->getParam('id');      
        if(!is_array($Ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please select Import Type.'));
        } else {
            try {
                $importTypeModel = Mage::getModel('import/import_type');
                foreach ($Ids as $id) {
                    $importTypeModel->load($id)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('import')->__(
                'Total of %d record(s) were deleted.', count($Ids)
                )
                );
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    //

    public function readAction()
    {

        header('Content-Type: text/html; charset=utf-8');
        try
        {
            Mage::getModel("import/process")->setProcessType($this->getRequest()->getParam('type'))->processCsv();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Csv Read Process has completed Successfully.'));

            $this->_redirect('*/*/index');
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index', array('type' => $this->getRequest()->getParam('type')));

            return;
        }
    }

    public function uploadAction()
    {
        try
        {
            $id  =  $this->getRequest()->getParam('type',0);

            $type  =  Mage::getModel('import/import_type')->load($id);

            if ($type->getId() || $id == 0) 
            {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) 
                {
                    $type->setData($data);
                }
            }
            if(isset($_FILES['import_csv']['name']) && $_FILES['import_csv']['name'] != '')
            { 
                $_extension = explode(".",$_FILES['import_csv']['name']);

                $uploader = new Varien_File_Uploader('import_csv');
                $uploader->setAllowedExtensions(array('csv'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $path = Mage::getBaseDir('media').DS."importcsv";
                $fileName = $type->getImportFileName();

                $result = $uploader->save($path, $fileName);
                chmod($result["path"]."/".$result["file"], 0777);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Csv Upload Process has completed Successfully.'));

                $this->_redirect('*/*/index');
            }
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/index', array('type' => $this->getRequest()->getParam('type')));

            return;
        }

    }

    public function emptyRecordsAction() 
    {
        try
        {
            if(!(int)$typeId = $this->getRequest()->getParam('type'))
            {
                throw new Exception("invalid data posted.");
            }
            elseif(!array_key_exists($typeId,Mage::getModel("import/process")->getTypeToProcess()))
            {
                throw new Exception("invalid type_id.");
            }

            $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
            $query = "DELETE FROM `".Mage::getSingleton("core/resource")->getTableName('import/import_process')."` WHERE `end_time` IS NOT NULL AND `type` = ".$typeId;
            $writeConnection->query($query);

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Unused Data has removed Successfully.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            $this->_redirect('*/*/index'); 
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData(array());
            $this->_redirect('*/*/index'); 
            return;
        }
    }

    public function saveeAction() 
    {   
        try
        {  
            if((int)$typeId = $this->getRequest()->getParam('type'))
            {
                $data['type'] = $typeId;
            }

            Mage::getModel("import/process")->setProcessType($data["type"])->processCsv();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Csv Read and Process has completed Successfully.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) 
            {
                $this->_redirect('import/adminhtml_type/index', array('id' => $model->getId()));
            }

            $this->_redirect('import/adminhtml_type/index');
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('import/adminhtml_type/index', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function removeAction() 
    {
        try
        {
            if(!(int)$typeId = $this->getRequest()->getParam('type'))
            {
                throw new Exception("invalid data posted.");
            }
            elseif(!array_key_exists($typeId,Mage::getModel("import/process")->getTypeToProcess()))
            {
                throw new Exception("invalid type_id.");
            }

            $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
            $query = "DELETE FROM `".Mage::getSingleton("core/resource")->getTableName('import/import_process')."` WHERE `end_time` IS NOT NULL AND `type` = ".$typeId;
            $writeConnection->query($query);

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Unused Data has removed Successfully.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) 
            {
                $this->_redirect('import/adminhtml_type/index', array('id' => $model->getId()));
            }

            $this->_redirect('import/adminhtml_type/index');
        }
        catch(Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData(array());
            $this->_redirect('import/adminhtml_type/index', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function massEmptyAction()
    {
        $Ids = $this->getRequest()->getParam('id');     
        if(!is_array($Ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please select Import Type.'));
        } else {
            try {

                $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
                $query = "DELETE FROM `".Mage::getSingleton("core/resource")->getTableName('import/import_process')."` WHERE `end_time` IS NOT NULL AND `type` IN ('".implode("','",$Ids)."') ";
                $writeConnection->query($query);

                foreach ($Ids as $id) 
                {
                    $path =  Mage::getBaseDir().DS. "media". DS ."importcsv". DS . Mage::getModel('import/import_type')->load($id)->getImportFileName();
                    if(file_exists($path))
                    {
                        @unlink($path);
                    }

                }


                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('import')->__(
                'Total of %d record(s) were deleted.', count($Ids)
                )
                );
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massRemoveProcessReportAction()
    {
        $Ids = $this->getRequest()->getParam('id');     
        if(!is_array($Ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please select Import Type.'));
        } 
        else 
        {
            try {

                foreach ($Ids as $id) 
                {
                    $path =  Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_PROCESS_".Mage::getModel('import/import_type')->load($id)->getImportFileName();
                    if(file_exists($path))
                    {
                        @unlink($path);
                    }

                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('import')->__(
                'Total of %d record(s) were deleted.', count($Ids)
                )
                );
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }


    public function massRemoveImportReportAction()
    {
        $Ids = $this->getRequest()->getParam('id');     
        if(!is_array($Ids)) 
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please select Import Type.'));
        } 
        else 
        {
            try {

                foreach ($Ids as $id) 
                {
                    $path =  Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_IMPORT_".Mage::getModel('import/import_type')->load($id)->getImportFileName();
                    if(file_exists($path))
                    {
                        @unlink($path);
                    }

                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('import')->__(
                'Total of %d record(s) were deleted.', count($Ids)
                )
                );
            } 
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massRemoveImportDataAction()
    {
         Mage::getSingleton('admin/session')->setNewImportProductColumns(array());
         
        $Ids = $this->getRequest()->getParam('id');     
        if(!is_array($Ids))
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('import')->__('Please select Import Type.'));
        }
        else
        {
            try
            {
                $writeConnection = Mage::getSingleton("core/resource")->getConnection("core_write");
                $query = "DELETE FROM `".Mage::getSingleton("core/resource")->getTableName('import/import_process')."` WHERE `type` IN ('".implode("','",$Ids)."') ";
                $writeConnection->query($query);
                
                foreach ($Ids as $id) 
                {
                    $importType = Mage::getModel('import/import_type')->load($id);
                    $processPath =  Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_PROCESS_".$importType->getImportFileName();
                    if(file_exists($processPath))
                    {
                        @unlink($processPath);
                    }
                    
                    $importPath =  Mage::getBaseDir().DS. "media". DS ."importcsv". DS ."report". DS ."REPORT_IMPORT_".$importType->getImportFileName();
                    if(file_exists($importPath))
                    {
                        @unlink($importPath);
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('import')->__('Total of %d record(s) were deleted.', count($Ids)));
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}