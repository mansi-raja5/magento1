<?php 
class Cybercom_Item_Block_Adminhtml_Items_Edit_Tab_Images
        extends Mage_Adminhtml_Block_Widget_Form_Container 
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId    = 'entity_id';
        $this->_controller  = '';
        $this->_mode        = 'edit';

        $this->setId('cybercom_item_image_grid');
        $this->setTemplate('cybercom_item/images/images.phtml');
    }           
}
?>