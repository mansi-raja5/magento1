<?php
class Cybercom_Vendor_Adminhtml_VendorsController extends Mage_Adminhtml_Controller_Action
{
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
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }  
     
    public function editAction()
    {  
        $this->_initAction();
     
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
     
        $data = Mage::getSingleton('adminhtml/session')->getBazData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('cybercom_vendor', $model);
     
        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Vendor') : $this->__('New Vendor'), $id ? $this->__('Edit Vendor') : $this->__('New Vendor'))
            ->_addContent($this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }  
     
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('cybercom_vendor/vendordetail');
            $model->setData($postData);
 
            try {
            // print_r($postData);
            // exit;
            $postData['status'] = 1;
                $model->save();
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The vendor has been saved.'));
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
                $model->load($id);
                $model->delete();  
 
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
        return Mage::getSingleton('admin/session')->isAllowed('sales/cybercom_vendor');
    }
}