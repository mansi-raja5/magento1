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
     
        $this->_updateButton('save', 'label', $this->__('Save Vendor'));
        $this->_updateButton('delete', 'label', $this->__('Delete Vendor'));
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