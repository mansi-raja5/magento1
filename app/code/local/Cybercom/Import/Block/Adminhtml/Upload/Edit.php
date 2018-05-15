<?php

class Cybercom_Import_Block_Adminhtml_Upload_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {   
        parent::__construct();
        
        $this->_objectId = 'type_edit';
        $this->_blockGroup = 'import';
        $this->_controller = 'adminhtml_upload';
        
        $this->_updateButton('save', 'label', Mage::helper('import')->__('Upload csv'));
       
	}

    public function getHeaderText() {
        return Mage::helper('import')->__('Upload Csv') ;
    }
    

}