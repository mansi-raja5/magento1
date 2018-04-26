<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  


        $this->_blockGroup = 'cybercom_vendor';
        $this->_controller = 'adminhtml_vendors';
     
        parent::__construct();
        $this->setId('cybercom_vendor_vendordetail_edit');
        $this->_updateButton('save', 'label', $this->__('Save Vendor'));
        $this->_updateButton('delete', 'label', $this->__('Delete Vendor'));
        
        $this->_addButton('save_and_continue', array(
             'label' => Mage::helper('cybercom_vendor')->__('Save And Continue Edit Vendor'),
             'onclick' => 'saveAndContinueEdit()',
             'class' => 'save' 
         ), -100);   

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
        if (Mage::registry('cybercom_vendor')->getId()) {
            return $this->__('Edit Vendor');
        }  
        else {

            return $this->__('New Vendor');
        }  
    }  
}