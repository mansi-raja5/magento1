<?php
class Ccc_Outlook_Model_Mysql4_Mailattachment extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('outlook/mailattachment', 'attachment_id');
    }  
}