<?php
class Cybercom_Vendor_Model_Mysql4_Vendordetail extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('cybercom_vendor/vendordetail', 'id');
    }  
}