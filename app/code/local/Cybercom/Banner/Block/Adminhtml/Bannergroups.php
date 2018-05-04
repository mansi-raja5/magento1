<?php
 
class Cybercom_Banner_Block_Adminhtml_Bannergroups extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {

        $this->_blockGroup = 'cybercom_bannergroups';
        $this->_controller = 'adminhtml_bannergroups';
        $this->_headerText = "Cybercom Banner Groups Detail";

        parent::__construct();
    }      
}