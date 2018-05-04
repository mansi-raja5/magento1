<?php
class cybercom_banner_Adminhtml_BannersController extends Mage_Adminhtml_Controller_Action
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
            ->_title($this->__('Cybercom'))->_title($this->__('Banner'))
            ->_addBreadcrumb($this->__('Cybercom'), $this->__('Cybercom'))
            ->_addBreadcrumb($this->__('Banner'), $this->__('Banner'));
         
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
        $this->_title($this->__('Cybercom'))->_title($this->__('Cybercom Banner'));
        $this->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('cybercom_banner/adminhtml_banners'))
                ->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_banners_grid')->toHtml()
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
        $this->_initAction();

        // Get id if available
        $bannerId  = $this->getRequest()->getParam('banner_id');
        $bannerModel = Mage::getModel('cybercom_banner/bannerdetail');
     
        if ($bannerId) {

            // Load record
            $bannerModel->load($bannerId);
     
            // Check if record is loaded
            if (!$bannerModel->getId()) {
                    
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Banner no longer exists.'));
                $this->_redirect('*/*/');     
                return;
            }  
        }  

        
     
        $this->_title($bannerModel->getId() ? $bannerModel->getName() : $this->__('New Banner'));
     
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $bannerModel->setData($data);
        }  
     
        Mage::register('cybercom_banner', $bannerModel);
        Mage::getSingleton('core/session')->setBannerdata($bannerModel);
        //$this->_initAction()
        $this->loadLayout();
        $this->_addBreadcrumb($id ? $this->__('Edit Banner') : $this->__('New Banner'), $id ? $this->__('Edit Banner') : $this->__('New Banner'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('cybercom_banner/adminhtml_banners_edit'));
        $this->_addLeft($this->getLayout()->createBlock('cybercom_banner/adminhtml_banners_edit_tabs'));
        $this->renderLayout();        
     
    }  
     
    public function saveAction()
    {
        // echo "<pre>";
        // print_r($this->getRequest()->getPost());
        // print_r($_FILES['image']);
        // exit;
        if ($postData = $this->getRequest()->getPost()) 
        {
            try 
            {
                $bannerModel = Mage::getSingleton('cybercom_banner/bannerdetail');
                if($postData)
                {
                    //Delete Image when selected checkbox
                    $imgAry = $postData['image'];
                    if(isset($imgAry['value']))
                        $postData['image'] = $imgAry['value'];  //Get Image Name

                    if($imgAry['delete'] == 1)
                    {
                        $postData['image']  = "";  // Update in DB

                        $io                 = new Varien_Io_File();
                        $removeThis         = Mage::getBaseDir('media') . DS . $imgAry['value'];
                        $io->rm($removeThis);  
                    }
               
                    $postData['created_date']=date('Y-m-d H:i:s');
                    $postData['updated_date']=date('Y-m-d H:i:s');
                    $bannerModel->setData($postData);
                    $bannerModel->save();  
                }

                $bannerId  = $bannerModel->getId();
                if (isset($_FILES) && $_FILES['image']['name']) 
                {
                    //Remove file from media if already uploaded
                    // if ($bannerId && isset($postData['image'])) 
                    if ($bannerId && isset($postData['image'])) 
                    {             
                        $io         = new Varien_Io_File();
                        if(is_array($postData['image']))
                            $iname = $postData['image']['value'];
                        else
                            $iname = $postData['image'];
                       $removeThis = Mage::getBaseDir('media')."/banner/".$iname; 
                        $io->rm($removeThis);
                    }

                    //Start Image Uploading
                    $path = Mage::getBaseDir('media').'/banner/';
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg', 'png', 'gif'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);


                    $destFile = $path . $_FILES['image']['name'];
                    $filename = $uploader->getNewFileName($destFile);

                    $ext = pathinfo($filename, PATHINFO_EXTENSION); //getting image extension
                    $filename = "banner_".$bannerId.".".$ext; //change custom name here
                    $uploader->save($path, $filename);

                    $imageUrl = $path."/".$filename;
                        
                    //Upload Thumbnail
                    if(file_exists($imageUrl)) 
                    {

                        $imageObj = new Varien_Image($imageUrl);
                        $imageObj->constrainOnly(true);
                        $imageObj->keepAspectRatio(true);
                        $imageObj->keepFrame(false);
                        $imageObj->resize(100, 100);
                        //$thumbnailImage = $path."/banner/".$filename;
                        $imageObj->save($path."/thumbnail/",$filename);
                    }                    

                    //Store Uploaded Image Path into DB
                    $postImage['banner_id'] = $bannerId;
                    $postImage['image'] = $filename;
                    $bannerModel->setData($postImage);
                    $bannerModel->save();  
                }
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cybercom_banner')->__('Banner was successfully saved'));
                //Mage::getSingleton('adminhtml/session')->setFormData(false);
                                      
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit',array('id' => $bannerModel->getId()));
                    return;
                }                
                $this->_redirect('*/*/');
 
                return;
            }  
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this Banner.'));
            }
 
            Mage::getSingleton('adminhtml/session')->setBazData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {  

        $this->_initAction();
     
        // Get id if available
        $bannerId  = $this->getRequest()->getParam('banner_id');
        $bannerModel = Mage::getModel('cybercom_banner/bannerdetail');

        try {
            if ($bannerId) {
                //load banner details
                $bannerModel->load($bannerId);

                //remmove banner image from media
                $postData['image']  = $bannerModel->getImage();
                $io                 = new Varien_Io_File();
                $removeThis         = Mage::getBaseDir('media') . DS . $postData['image'];
                $io->rm($removeThis);

                //Delete record from Banner Table
                $bannerModel->delete();  
 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Banner has been deleted.'));
                $this->_redirect('*/*/');
 
                return;
            }  
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this Banner.'));
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
                    $RequestData = Mage::getModel('cybercom_banner/Bannerdetail')->load($requestId);                    
                    $RequestData->delete(); 

                    //Delete all records from Banner_price table having same Banner
                    $BannerPriceModel = Mage::getModel('cybercom_banner/price')
                                        ->getCollection()
                                        ->addFieldToFilter('Banner_id',$requestId)->delete();
                    $BannerPriceModel->delete();                                        
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
                    $RequestData = Mage::getModel('cybercom_banner/Bannerdetail')->load($requestId);                    
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
        $data  = Mage::getModel('cybercom_banner/Bannerdetail')->load($this->getRequest()->getParam('id'));
        echo $data->getContent();
    }
     
    public function BannerDetailsAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_edit')
            ->setUseAjax(true)
            ->setData('action', $this->getUrl('*/*/save'))
        );
    } 
    public function customAction()
    {

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_edit_tab_form')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    public function BannerPriceAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_edit_tab_grid')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    public function gridPriceAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_edit_tab_grid')->toHtml()
        );
    }   

    public function exportCsvAction(){
        $fileName   = 'BannerDetails.csv'; 
        $content    = $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_gridexport')->getCsvFile(); 
        //$content    = $this->getLayout()->createBlock('cybercom_banner/adminhtml_Banners_edit_tab_grid');  
          
        $this->_prepareDownloadResponse($fileName, $content); 
    }
}