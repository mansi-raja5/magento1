<?php
 
class Cybercom_Item_Block_Adminhtml_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'cybercom_item';
        $this->_controller = 'adminhtml_items';
        $this->_headerText = "Cybercom Item Details";

        parent::__construct();
        //$this->_removeButton('add');
    }      
}