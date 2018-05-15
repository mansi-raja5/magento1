<?php

class Cybercom_Import_Block_Adminhtml_Type_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {   
        parent::__construct();
        
        $this->_objectId    = 'type_edit';
        $this->_blockGroup  = 'import';
        $this->_controller  = 'adminhtml_type';
        
        $this->_updateButton('save', 'label', Mage::helper('import')->__('Save Import Type'));
        $this->_updateButton('delete', 'delete', Mage::helper('import')->__('Delete Import Type'));
        
        if($this->getRequest()->getParam('type')){
        $this->_addButton('delete', array(
            'label' => Mage::helper('import')->__('Delete Import Type'),
            'onclick' => 'setLocation(\''.$this->getDeleteUrl().'\')',
            'class' => 'delete',
                ), -100);
        }
        
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('import')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
            'class' => 'save',
                ), -100);
        
        

        $this->_formScripts[] = "
           function toggleEditor() {
                if (tinyMCE.getInstanceById('type_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'type_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'type_content');
                }
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }		
			
        "; 
		 
		
    }
    
    public function getDeleteUrl(){
        return $this->getUrl('*/*/delete', array(
            'type'    => $this->getRequest()->getParam('type')));
    }
    
	public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            'type_id'    => $this->getRequest()->getParam('type'),
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getHeaderText() {

        if (Mage::registry('current_type') && Mage::registry('current_type')->getId()) {
            return Mage::helper('import')->__("Edit Import Type '%s'", $this->htmlEscape(Mage::registry('current_type')->getName()));
        } else {
            return Mage::helper('import')->__('Add Import Type');
        }
    }
    

}