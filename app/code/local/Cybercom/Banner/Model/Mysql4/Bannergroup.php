<?php
class Cybercom_Banner_Model_Mysql4_Bannergroup extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('cybercom_banner/bannergroup', 'group_id');
    }    
}