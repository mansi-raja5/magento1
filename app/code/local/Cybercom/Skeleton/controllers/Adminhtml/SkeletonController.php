<?php
class Cybercom_Skeleton_Adminhtml_SkeletonController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('cybercom/skeleton')
            ->_title($this->__('cybercom'))->_title($this->__('skeleton'))
            ->_addBreadcrumb($this->__('cybercom'), $this->__('cybercom'))
            ->_addBreadcrumb($this->__('skeleton'), $this->__('skeleton'));

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
        $this->_title($this->__('cybercom'))->_title($this->__('Cybercom Skeleton'));

        //$layout = Mage::getModel('core/layout');
        //$block  = $layout->createBlock('cybercom_skeleton/adminhtml_myblocks');

        $this   ->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('cybercom_skeleton/adminhtml_myblocks'))
                //->_addContent($block)
                ->renderLayout();
    } 

    public function gridcontainerAction(){
        // $products_collection = Mage::getModel('catalog/product');
        // echo "<pre>";
        // print_r(get_class_methods($products_collection));

        $this   ->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('cybercom_skeleton/adminhtml_gridcontainer'))
                ->renderLayout();        
        
    }

    public function jsdemoAction(){
        $this   ->loadLayout()
                ->_addContent($this->getLayout()->createBlock('cybercom_skeleton/adminhtml_jsdemo'))
                ->renderLayout();        
    }
}