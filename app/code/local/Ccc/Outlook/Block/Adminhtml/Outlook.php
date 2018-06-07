<?php
 
class Ccc_Outlook_Block_Adminhtml_Outlook extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
	    $this->_controller = 'adminhtml_outlook';
	    $this->_blockGroup = "outlook";
	    $this->_headerText = "Outlook Order Emails";

	    

	    $this->_addButton('login', array(
	        'label'     => Mage::helper('outlook')->__('Login'),
	        'onclick'   => "location.href='".$this->getUrl('*/*/outlooklogin')."'",
	        'class'     => '',
	    ));    

		$this->_addButton('refreshtoken', array(
	        'label'     => Mage::helper('outlook')->__('Refresh Token'),
	        'onclick'   => "location.href='".$this->getUrl('*/*/refreshtoken')."?reload=true'",
	        'class'     => '',
	    ));   

		$this->_addButton('readmail', array(
	        'label'     => Mage::helper('outlook')->__('Read Mail'),
	        'onclick'   => "location.href='".$this->getUrl('*/*/readmail')."?reload=true'",
	        'class'     => '',
	    ));  

		$this->_addButton('download-attachment', array(
	        'label'     => Mage::helper('outlook')->__('Download Attachment'),
	        'onclick'   => "location.href='".$this->getUrl('*/*/downloadattchment')."?reload=true'",
	        'class'     => '',
	    )); 	  	       

        parent::__construct();  
        $this->_removeButton('add');

    }      

}