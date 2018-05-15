<?php
class Cybercom_Import_Block_Adminhtml_Upload extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
        $this->_controller = 'adminhtml_upload';
        $this->_blockGroup = 'import';
    
        $this->_headerText = Mage::helper('import')->__('Import Process');
        parent::__construct();
        $this->_removeButton('add');
  }
}