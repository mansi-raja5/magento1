<?php
class Cybercom_Skeleton_Block_Adminhtml_Gridcontainer extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->_headerText = "Cybercom Skelton Details";
        parent::__construct();
        $this->setTemplate('cybercom_skeleton/adminhtml/grid_container.phtml');
    }        
}