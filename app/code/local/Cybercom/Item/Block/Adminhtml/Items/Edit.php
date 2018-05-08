<?php
class Cybercom_Item_Block_Adminhtml_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  


        $this->_blockGroup = 'cybercom_item';
        $this->_controller = 'adminhtml_items';
     
        parent::__construct();
        $this->setId('cybercom_item_itemdetail_edit');
        $this->_updateButton('save', 'label', $this->__('Save Item'));
        $this->_updateButton('delete', 'label', $this->__('Delete Item'));
        
        $this->_addButton('save_and_continue', array(
             'label' => Mage::helper('cybercom_item')->__('Save And Continue Edit Item'),
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
        if (Mage::registry('cybercom_item')->getId()) {
            return $this->__('Edit Item');
        }  
        else {

            return $this->__('New Item');
        }  
    }  
}