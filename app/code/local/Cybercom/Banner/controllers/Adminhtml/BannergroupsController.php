<?php
class Cybercom_Banner_Adminhtml_BannergroupsController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('Cybercom/bannergroup')
            ->_title($this->__('Cybercom'))
            ->_title($this->__('Banners'))
            ->_title($this->__('Manage Banner Groups'))
            ->_addBreadcrumb($this->__('Cybercom'), $this->__('Cybercom'))
            ->_addBreadcrumb($this->__('Banner'), $this->__('Banner'));
         
        return $this;
    }     
    
    public function indexAction()
    {    
        $this->_title($this->__('Cybercom'))->_title($this->__('Cybercom Banner'));
        $this->_forward('edit');
    }

    public function editAction()
    {  
        $this->_initAction();
        $groupId = $this->getRequest()->getParam('groupId');
        try
        {
            if($groupId)
            {
                $model   = Mage::getModel('cybercom_banner/bannergroup')->load($groupId);
                if(!$model->getID())
                {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('This Group is no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }

                //print_r($model->getData());

                Mage::register('bannerGroupData',$model);
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred.'));
        }        

        $this->loadLayout();

        $this->_addBreadcrumb($groupId ? $this->__('Edit Banner') : $this->__('New Banner'), $groupId ? $this->__('Edit Banner') : $this->__('New Banner'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit'));

        $this->_addLeft($this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_treegroups'));
        
        $this->renderLayout();        
     
    }

    public function saveAction()
    {
        $this->_initAction();
        $postData = $this->getRequest()->getPost();
        // echo "<pre>";
        // print_r($postData);
        // exit;
        try
        {
            $postGroupId = $postData['group_id'];  // If exist then edit
            $bannerGroupModel = Mage::getSingleton('cybercom_banner/bannergroup');
            if($postData){
                $bannerGroupModel->setData($postData);
                $bannerGroupModel->save();
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while.'));
        }           

        if (isset($postGroupId) && $postGroupId != '') {
            $this->_redirect('*/*/edit',array('groupId' => $postGroupId));
            return;
        } 
        $this->_redirect('*/*/');
        return;
    } 

    public function deleteAction()
    {
        $this->_initAction();
        $groupId = $this->getRequest()->getParam('groupId');
        try
        {
            if($groupId)
            {
                $bannerGroupModel = Mage::getModel('cybercom_banner/bannergroup')->load($groupId);
                $bannerGroupModel->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The Banner has been deleted.'));
                $this->_redirect('*/*/');
                return;                
            }        
            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while deleting this Banner Group.'));
        }        
    }
    

    public function customAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit_tab_form')
            ->setUseAjax(true)
            ->toHtml()
        );
    }
    public function categoriesAction(){
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit_tab_categories')
            ->setUseAjax(true)
            ->toHtml()
        );
    }

    public function categoriesJsonAction(){
        $this->_initAction();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Add new group form
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsActiveTabId();
        $this->_forward('edit');
    }    
    



    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cybercom_banner/bannergroups');
    }
}