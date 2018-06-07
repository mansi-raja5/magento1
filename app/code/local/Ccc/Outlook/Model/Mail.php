<?php
class Ccc_Outlook_Model_Mail extends Mage_Core_Model_Abstract
{

    protected $_config = array(
                'ssl' => 'tls',
                'auth' => 'login',
                'username' => 'zivafrancis@gmail.com',
                'password' => 'zivafrancis789');

    protected $_message = null;
    protected $_emailFrom = null;
    protected $_emailTo = array();
    protected $_emailCc = null;
    protected $_emailBcc = null;
    protected $_subject = null;
    protected $_brandId = null;
    protected $_contactId = null;
    protected $_templateId = null;
    //protected $_mailType = Ccc_Manufacturer_Model_Brand_Contact_Mail_History::MAIL_TYPE_MANUAL;

    public function getConfig()
    {
        return $this->_config;
    }

    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function setEmailFrom($_emailFrom)
    {
        $this->_emailFrom = $_emailFrom;
        return $this;
    }

    public function getEmailFrom()
    {
        return $this->_emailFrom;
    }

    public function setEmailTo($_emailTo)
    {
        $this->_emailTo = $_emailTo;
        return $this;
    }

    public function getEmailTo()
    {
        return $this->_emailTo;
    }

    public function setEmailCc($_emailCc)
    {
        $this->_emailCc = $_emailCc;
        return $this;
    }

    public function getEmailCc()
    {
        return $this->_emailCc;
    }

    public function setEmailBcc($_emailBcc)
    {
        $this->_emailBcc = $_emailBcc;
        return $this;
    }

    public function getEmailBcc()
    {
        return $this->_emailBcc;
    }    

    public function setSubject($_subject)
    {
        $this->_subject = $_subject;
        return $this;
    }

    public function getSubject()
    {
        return $this->_subject;
    }

    public function setBrandId($_brandId)
    {
        $this->_brandId = $_brandId;
        return $this;
    }

    public function getBrandId()
    {
        return $this->_brandId;
    }

    public function setContactId($_contactId)
    {
        $this->_contactId = $_contactId;
        return $this;
    }

    public function getContactId()
    {
        return $this->_contactId;
    }

    public function setTemplateId($_templateId)
    {
        $this->_templateId = $_templateId;
        return $this;
    }

    public function getTemplateId()
    {
        return $this->_templateId;
    }

     public function setMailType($_mailType)
    {
        $this->_mailType = $_mailType;
        return $this;
    }

    public function getMailType()
    {
        return $this->_mailType;
    }

    public function send()
    {
        $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $this->getConfig());
         
        $mail = new Zend_Mail();
        $mail->setBodyHtml($this->getMessage());
        $mail->setFrom($this->getEmailFrom(),'1Stopbedrooms');
        $mail->addTo($this->getEmailTo());
        $mail->addCc($this->getEmailCc());
        $mail->addBcc($this->getEmailBcc());
        $mail->setSubject($this->getSubject());
        $mail->send($transport); 

        return $this;
    }
}