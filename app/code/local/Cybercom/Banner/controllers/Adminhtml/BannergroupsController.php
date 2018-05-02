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

        // Get id if available
        $bannerId  = $this->getRequest()->getParam('banner_id');
        $model = Mage::getModel('cybercom_banner/bannerdetail');
     
        if ($bannerId) {

            // Load record
            $model->load($bannerId);     
            // Check if record is loaded
            if (!$model->getId()) {                    
                Mage::getSingleton('adminhtml/session')->addError($this->__('This Banner no longer exists.'));
                $this->_redirect('*/*/');     
                return;
            }  
        }  
             
        $this->_title($model->getId() ? $model->getName() : $this->__('New Banner'));
     
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }  
     
        Mage::register('cybercom_banner', $model);
        Mage::getSingleton('core/session')->setBannerdata($model);
        //$this->_initAction()
        $this->loadLayout();
        $this->_addBreadcrumb($id ? $this->__('Edit Banner') : $this->__('New Banner'), $id ? $this->__('Edit Banner') : $this->__('New Banner'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit_tab_form'));
        $this->_addLeft($this->getLayout()->createBlock('cybercom_banner/adminhtml_bannergroups_edit_tab_categories'));
        $this->renderLayout();        
     
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