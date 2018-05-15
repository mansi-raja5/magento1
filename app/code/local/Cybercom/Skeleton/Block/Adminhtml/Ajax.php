<?php
class Cybercom_Skeleton_Block_Adminhtml_Ajax extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->_headerText = "AJAX Demo";
        parent::__construct();
        $this->setTemplate('cybercom_skeleton/adminhtml/ajax.phtml');
    }      
}