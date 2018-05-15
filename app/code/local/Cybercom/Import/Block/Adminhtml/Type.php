<?php
class Cybercom_Import_Block_Adminhtml_Type extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
        $this->_controller = 'adminhtml_type';
        $this->_blockGroup = 'import';
        $this->_headerText = Mage::helper('import')->__('Manage Import Type');
        parent::__construct();
  }
}