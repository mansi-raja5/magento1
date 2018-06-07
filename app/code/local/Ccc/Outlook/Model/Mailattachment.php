<?php
class Ccc_Outlook_Model_Mailattachment extends Mage_Core_Model_Abstract
{
	
	const ATTACHMENT_PATH      = "outlook";

    protected function _construct()
    {  
        $this->_init('outlook/mailattachment');
    }  

    public function getAttachmentpath()
    {
    	return Mage::getBaseDir('media') . DS .self::ATTACHMENT_PATH;
    }

    public function getAttachmentUrl()
    {
        // return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)."var/". self::ATTACHMENT_PATH;
    	return Mage::getBaseUrl("media").self::ATTACHMENT_PATH;
    }
}
?>