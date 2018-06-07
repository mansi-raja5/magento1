<?php
class Ccc_Outlook_Model_Ordermail extends Mage_Core_Model_Abstract
{
    const ORDERKEY = "order_email_to";

    protected function _construct()
    {  
        $this->_init('outlook/ordermail');
    }  

    public function readMail()
    {
    	try{
    		$outlook   = Mage::getModel('outlook/outlook');
            //if(isset($_SESSION["client_id"]) && $_SESSION['client_id'] != "")
            {
                $outlook->init();
            }

            $totalMail = 0;
            for ($i=0; $i < 10 ; $i++)
            { 
                $canContinue = $outlook->readmail($i);
                if($canContinue == false)
                {
                    $totalMail += $outlook->getMailCount();
                    return $totalMail;
                    // break;
                }
            }
    	}
    	catch(Exception $e)
    	{
    		return $e->getMessage();
    	}
    }

    public function refreshToken()
    {
        try{
            $outlook   = Mage::getModel('outlook/outlook');
            $hasToken  = $outlook->refreshToken();
            return $hasToken;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function downloadAttchment()
    {
        try
        {
            $query = "SELECT * FROM `ccc_order_mail` WHERE `has_attachment` = 1 and `is_downloaded` = 2";
            $orderMails = Mage::getSingleton("core/resource")->getConnection("read")->fetchAll($query);        

            $outlook   = Mage::getModel('outlook/outlook');
            $outlook->setAttachmentData($orderMails);
            $isDownloaded  = $outlook->downloadAttchment();
            return $isDownloaded;
        }
        catch(Exception $e){
            return $e->getMessage();
        }        
    }

    public function getMfr()
    {
        try
        {
            $readConnection = Mage::getSingleton("core/resource")->getConnection("read");
            $query          = "SELECT entity_id,subject FROM `ccc_order_mail` WHERE mfr_id is null";
            $orderMails     = $readConnection->fetchAll($query);
            $count=0;
            foreach ($orderMails as $_ordermail) 
            {
                $entityId       = $_ordermail['entity_id'];
                $subject        = $_ordermail['subject'];

                //Fatch string between first []
                if(preg_match('#\[(.*?)\]#', $subject, $match))
                {
                    $mfrName    = $match[1];
                    $mfrQuery   = "SELECT entity_id FROM `ccc_manufacturer` WHERE mfg = '{$mfrName}'";
                    $mfrid = $readConnection->fetchOne($mfrQuery);

                    if(isset($mfrid) && $mfrid != "")
                    {
                        $updateQuery = "UPDATE `ccc_order_mail` 
                                SET `mfr_id` = $mfrid
                                WHERE `entity_id` = $entityId";
                        Mage::getSingleton("core/resource")->getConnection("core_write")->query($updateQuery);   
                        $count++;    
                    }
                }
                /*$recipients     = $_ordermail['recipients'];            
                $recipientsAry  = explode(",", $recipients);

                foreach ($recipientsAry as $_recipient) 
                {
                    $mfrQuery = "SELECT mfg_id  FROM `ccc_manufacturer_varchar` WHERE `option_value` = '{$_recipient}' AND option_key = '".self::ORDERKEY."'";

                    $mfrid = $readConnection->fetchOne($mfrQuery);
                    if(isset($mfrid) && $mfrid != "")
                    {
                        $updateQuery = "UPDATE `ccc_order_mail` 
                                SET `mfr_id` = $mfrid
                                WHERE `entity_id` = $entityId";
                        Mage::getSingleton("core/resource")->getConnection("core_write")->query($updateQuery);   
                        $count++;    
                    }
                }*/
            }
            return $count;
        }
        catch(Exception $e){
            return $e->getMessage();
        }        
    }

    public function getOrderStatus()
    {
        try
        {              
        }
        catch(Exception $e){
            return $e->getMessage();
        }               
    }
}
?>