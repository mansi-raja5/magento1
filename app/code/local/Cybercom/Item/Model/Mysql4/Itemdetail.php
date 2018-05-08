<?php
class Cybercom_Item_Model_Mysql4_Itemdetail extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('cybercom_item/itemdetail', 'entity_id');
    }  
}