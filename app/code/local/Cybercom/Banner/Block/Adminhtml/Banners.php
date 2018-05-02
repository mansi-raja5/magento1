	<?php
 
class Cybercom_Banner_Block_Adminhtml_Banners extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {

        $this->_blockGroup = 'cybercom_banner';
        $this->_controller = 'adminhtml_banners';
        $this->_headerText = "Cybercom Banner Details";

        parent::__construct();
        //$this->_removeButton('add');
    }      
}