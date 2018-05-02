<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  
        $this->_blockGroup = 'cybercom_banner';
        $this->_controller = 'adminhtml_bannergroups';
     
        parent::__construct();
        $this->setId('cybercom_banner_bannerdetail_edit');
        $this->_updateButton('save', 'label', $this->__('Save Banner'));
        
        $this->_addButton('save_and_continue', array(
             'label' => Mage::helper('adminhtml')->__('Save And Continue Edit Banner'),
             'onclick' => 'saveAndContinueEdit()',
             'class' => 'save' 
         ), -100);   
        //$this->_updateButton('delete', 'label', $this->__('Delete Banner'));
        $this->_addButton('delete', array(
            'label'     => Mage::helper('adminhtml')->__('Delete Banner'),
            'class'     => 'delete',
            'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                .'\', \'' . $this->getDeleteUrl() . '\')',
        ));      

        $this->_formScripts[] = "
        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action + 'back/edit/');
        }";
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
         return $this->__('New Banner');  
    }  
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['banner_id' => $this->getId()]);
    }    
    public function getId()
    {
        return $this->getRequest()->getParam('banner_id');
    }      
}