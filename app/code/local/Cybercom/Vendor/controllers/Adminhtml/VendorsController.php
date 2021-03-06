<?php
class Cybercom_Vendor_Adminhtml_VendorsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {

        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('Cybercom/items')
            ->_title($this->__('Sales'))->_title($this->__('Vendor'))
            ->_addBreadcrumb($this->__('Sales'), $this->__('Sales'))
            ->_addBreadcrumb($this->__('Vendor'), $this->__('Vendor'));
         
        return $this;
    }
     
    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    
    public function indexAction()
    {    
        $this->_title($this->__('Sales'))->_title($this->__('Cybercom Vendor'));
        $this->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors'))
                ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_grid')->toHtml()
        );
    }    
     
    public function newAction()
    {  
        Mage::getSingleton('core/session')->setActiveTab('general_information');         
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }  
     
    public function editAction()
    {  
        //$this->_initAction();

        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('cybercom_vendor/vendordetail');
     
        if ($id) {

            // Load record
            $model->load($id);
     
            // Check if record is loaded
            if (!$model->getId()) {
                    
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Vendor no longer exists.'));
                $this->_redirect('*/*/');     
                return;
            }  
        }  

        
     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Vendor'));
     
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('cybercom_vendor', $model);
        Mage::getSingleton('core/session')->setVendordata($model);
        //$this->_initAction()
        $this->loadLayout();
        $this->_addBreadcrumb($id ? $this->__('Edit Vendor') : $this->__('New Vendor'), $id ? $this->__('Edit Vendor') : $this->__('New Vendor'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit'));
        $this->_addLeft($this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit_tabs'));
        $this->renderLayout();        
     

    }  
     
    public function saveAction()
    {
        // echo "<pre>";
        // print_r($this->getRequest()->getPost());
        //exit;
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('cybercom_vendor/vendordetail');
            $model->setData($postData);

           
            try {
                if($postData['name'] || $postData['status'])
                    $model->save();  
                
                $resource           = Mage::getSingleton('core/resource');
                $table              = $resource->getTableName('cybercom_vendor/price');
                $insertSql          = "";
                $updateSql          = "";
                $updateEntityIds    = "";
                $deleteEntityIds    = "";
                $vendor_id  = $model->getId();
                
                
                //Insert Vender_price entry code starts
                $insertAry  = $postData['vendor_prices']['insert'];
                if(isset($insertAry) && count($insertAry) > 0 )
                {
                    foreach ($insertAry as $product_id => $vendor_price) 
                    {
                        if($vendor_price != "")
                            $insertSql .= "(".$product_id .",".$vendor_id .",".$vendor_price ."),";
                    }                                      
                }

                //Update Vender_price code starts
                $updateAry  = $postData['vendor_prices']['update'];
                if(isset($updateAry) && count($updateAry) > 0 )
                {

                    foreach ($updateAry as $entityId => $vendorPriceAry) 
                    {
                        $vendorPrice = reset($vendorPriceAry);  // Gives you first element of array

                        if($vendorPrice == "")  //set null price when blank is passed
                            $deleteEntityIds .= $entityId.','; 
                        else{
                            $updateSql .= " WHEN  entity_id = ".$entityId." THEN ".$vendorPrice;
                            $updateEntityIds .= $entityId.',';       
                            }                 
                    }                                      
                }   

                $runQuery = ""; 
                if($insertSql != ''){

                    $runQuery  .= "INSERT INTO ".$table." (`product_id`, `vendor_id`, `price`) VALUES ".rtrim($insertSql,",").";";
                }
                if($updateSql != ''){
                    $runQuery .= "UPDATE  cybercom_vendor_price 
                                    SET  price = CASE 
                                                    ".$updateSql."
                                                 END
                                    WHERE entity_id IN (".rtrim($updateEntityIds,',').");";
                }  
                if($deleteEntityIds != ''){
                     $runQuery .= "DELETE FROM ".$table." WHERE `entity_id` IN (".rtrim($deleteEntityIds,',').");"; 
                }   
                if($runQuery != "")
                {                    
                    $writeConnection = $resource->getConnection('core_write');                    
                    $writeConnection->query($runQuery);                   
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cybercom_vendor')->__('Vendor was successfully saved'));
                //Mage::getSingleton('adminhtml/session')->setFormData(false);
                                      
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit',array('id' => $model->getId()));
                    return;
                }                
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this vendor.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setBazData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {  
        $this->_initAction();
     
        // Get id if available
        $id  = $this->getRequest()->getParam('id');
        $model = Mage::getModel('cybercom_vendor/vendordetail');

        try {
            if ($id) {
                //Delete record from Vendor Table
                $model->load($id);
                $model->delete();  

                //Delete all records from vendor_price table having same vendor
                $vendorPriceModel = Mage::getModel('cybercom_vendor/price')
                                    ->getCollection()
                                    ->addFieldToFilter('vendor_id',$id)->delete();
                $vendorPriceModel->delete();  
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The vendor has been deleted.'));
                $this->_redirect('*/*/');
 
                return;
            }  
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this vendor.'));
        }

        Mage::getSingleton('adminhtml/session')->setBazData($postData);
        $this->_redirectReferer();                 
    }  
     
    public function massDeleteAction() 
    {
        
        $requestIds = $this->getRequest()->getParam('selectIds');
         
        if(!is_array($requestIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select reqeust(s)'));
        } else {
            try {
                foreach ($requestIds as $requestId) {
                    $RequestData = Mage::getModel('cybercom_vendor/vendordetail')->load($requestId);                    
                    $RequestData->delete(); 

                    //Delete all records from vendor_price table having same vendor
                    $vendorPriceModel = Mage::getModel('cybercom_vendor/price')
                                        ->getCollection()
                                        ->addFieldToFilter('vendor_id',$requestId)->delete();
                    $vendorPriceModel->delete();                                        
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($requestIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    } 

    public function massStatusAction(){
        $requestIds = $this->getRequest()->getParam('selectIds');
        $status = $this->getRequest()->getParam('status');

        if(!is_array($requestIds)){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select reqeust(s)'));
        } else {
            try {
                foreach ($requestIds as $requestId) {
                    $RequestData = Mage::getModel('cybercom_vendor/vendordetail')->load($requestId);                    
                    $RequestData->setStatus($status)
                    ->setIsMassupdate(true)
                    ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($requestIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function messageAction()
    {
        $data  = Mage::getModel('cybercom_vendor/vendordetail')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
     
    public function vendorDetailsAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit')
            ->setUseAjax(true)
            ->setData('action', $this->getUrl('*/*/save'))
        );
    } 
    public function customAction()
    {

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit_tab_form')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    public function vendorPriceAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit_tab_grid')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    public function gridPriceAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit_tab_grid')->toHtml()
        );
    }   

    public function exportCsvAction(){
        $fileName   = 'vendorDetails.csv'; 
        $content    = $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_gridexport')->getCsvFile(); 
        //$content    = $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit_tab_grid');  
          
        $this->_prepareDownloadResponse($fileName, $content); 
    }
}