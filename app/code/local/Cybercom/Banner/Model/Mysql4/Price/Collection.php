<?php
class Cybercom_Vendor_Model_Mysql4_Price_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {  
        $this->_init('cybercom_vendor/price');
    }  

	public function delete()
	{
	    foreach ($this as $object) {
	        $object->delete();
	    }
	    return $this;
	}  
   
}