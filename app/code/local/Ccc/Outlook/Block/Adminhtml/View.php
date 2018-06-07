<?php
class Ccc_Outlook_Block_Adminhtml_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->_headerText = "Order Mails";
        parent::__construct();
        $this->setTemplate('outlook/view.phtml');
    }          
}