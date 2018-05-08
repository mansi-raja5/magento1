<?php
class Cybercom_Item_Adminhtml_ItemsController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('cybercom/item')
            ->_title($this->__('cybercom'))->_title($this->__('item'))
            ->_addBreadcrumb($this->__('cybercom'), $this->__('cybercom'))
            ->_addBreadcrumb($this->__('item'), $this->__('item'));
         
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
        $this->_title($this->__('cybercom'))->_title($this->__('Cybercom Item'));
        $this->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('cybercom_item/adminhtml_items'))
                ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_item/adminhtml_items_grid')->toHtml()
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
        $itemModel = Mage::getModel('cybercom_item/itemdetail');
     
        if ($id) {

            // Load record
            $itemModel->load($id);
     
            // Check if record is loaded
            if (!$itemModel->getId()) {
                    
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Item no longer exists.'));
                $this->_redirect('*/*/');     
                return;
            }  
        }  

        
     
        $this->_title($itemModel->getId() ? $itemModel->getName() : $this->__('New Item'));
     
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $itemModel->setData($data);
        }  
     
        Mage::register('cybercom_item', $itemModel);
        Mage::getSingleton('core/session')->setItemdata($itemModel);
        
        $this->loadLayout();
        $this->_addBreadcrumb($id ? $this->__('Edit Item') : $this->__('New Item'), $id ? $this->__('Edit Item') : $this->__('New Item'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('cybercom_item/adminhtml_items_edit'));

        //$uploadblock = $this->getLayout()->createBlock('adminhtml/media_uploader');
        //$uploadblock->getUploaderConfig()->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/items/upload', array()));
        //$uploadblock->getUploaderConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/items/upload', array()));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/media_uploader'));

        $this->_addLeft($this->getLayout()->createBlock('cybercom_item/adminhtml_items_edit_tabs'));
        $this->renderLayout();        
     

    }  
     
    public function saveAction()
    {
        // echo "<pre>";
        // print_r($this->getRequest()->getPost());
        // exit;
        if ($postData = $this->getRequest()->getPost()) {
            $itemModel = Mage::getSingleton('cybercom_item/itemdetail');
            $itemModel->setData($postData);

            try {

                $itemModel->save();  
                $itemId = $itemModel->getId();

                if(isset($postData['item']['media_gallery']['images'])){

                    $imgeAry = json_decode($postData['item']['media_gallery']['images']);

                    $imageData = array();
                    foreach ($imgeAry as $key => $img) {
                        $imageData[$key]['item_id']       = $itemId;
                        $imageData[$key]['image']         = "item/".$img->file;
                        $imageData[$key]['label']         = $img->label;
                        $imageData[$key]['sort_order']    = $img->position;
                    }
                    $images = Mage::getModel('cybercom_item/itemimages')->getCollection();
                    foreach ($images as $image) {
                         $image->delete();
                    }

                    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $connection->insertMultiple('cybercom_item_itemimages', $imageData);

                }
                //$imageData['small_image']   = $img[''];
                //$imageData['thumbnail']     = $img[''];
                //$imageData['exclude']       = $img[''];


                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cybercom_item')->__('Item was successfully saved'));
                //Mage::getSingleton('adminhtml/session')->setFormData(false);
                                      
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit',array('id' => $itemModel->getId()));
                    return;
                }                
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this item.'));
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
        $itemModel = Mage::getModel('cybercom_item/itemdetail');

        try {
            if ($id) {
                $itemModel->load($id);
                $itemModel->delete();   
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The item has been deleted.'));
                $this->_redirect('*/*/');
 
                return;
            }  
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this Item.'));
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
                    $RequestData = Mage::getModel('cybercom_item/itemdetail')->load($requestId);                    
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


    public function messageAction()
    {
        $data  = Mage::getModel('cybercom_item/itemdetail')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
     

    public function generalAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_item/adminhtml_items_edit_tab_form')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    
    public function imagesAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_item/adminhtml_items_edit_tab_images')
            ->setUseAjax(true)
            ->toHtml()
        );
    }

    public function exportCsvAction(){
        $fileName   = 'itemDetails.csv'; 
        $content    = $this->getLayout()->createBlock('cybercom_item/adminhtml_items_grid')->getCsvFile();           
        $this->_prepareDownloadResponse($fileName, $content); 
    }

    public function uploadAction()
    {
        try {
            $path = Mage::getBaseDir('media').'/item/';

            $uploader = new Mage_Core_Model_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            /*$uploader->addValidateCallback('catalog_product_image',
                Mage::helper('catalog/image'), 'validateUploadFile');*/
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            
            $destFile   = $path . $_FILES['image']['name'];
            $filename   = $uploader->getNewFileName($destFile);
            $result     = $uploader->save($path, $filename);            




            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);

            $result['url'] = $this->getItemMediaUrl($result['file']);
            $result['file'] = $result['file'];
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );

        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    } 


    public function getItemMediaUrl($file)
    {
        $file = str_replace(DS, '/', $file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return Mage::getBaseUrl('media'). 'item/' . $file;
    }    
}