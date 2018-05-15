<?php 
class Cybercom_Import_Block_Adminhtml_Upload_Edit_Multiple extends Mage_Adminhtml_Block_Widget_Tabs
{ 
  public function __construct()
  {    
      parent::__construct();
      $this->setId('type_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('import')->__('Import Type Information')); 
  }

  protected function _beforeToHtml()
  {   
      $this->addTab('form_section', array(
          'label'     => Mage::helper('import')->__('Import Type Information'),
          'title'     => Mage::helper('import')->__('Import Type Information'),
          'content'   => $this->getLayout()->createBlock('import/adminhtml_upload_edit_tab_multiple')->toHtml(),
      //    'content'   => $this->getLayout()->createBlock('import/adminhtml_upload_edit_tab_type')->toHtml(),
      ));
      
      return parent::_beforeToHtml();
  }
}
