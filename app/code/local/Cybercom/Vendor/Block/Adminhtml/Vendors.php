<?php
 
class Cybercom_Vendor_Block_Adminhtml_Vendors extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cybercom_vendor';
        $this->_controller = 'adminhtml_vendors';
        $this->_headerText = Mage::helper('cybercom_vendor')->__('Cybercom Vendor Details');
		$this->_addButton('custom_button', array(
		'label'     => Mage::helper('cybercom_vendor')->__('Custom Button'),
		'onclick'   => "location.href='".$this->getUrl('*/*/custombutton')."'",
		'class'     => '',
		));
        parent::__construct();
        //$this->_removeButton('add');
    }      
}