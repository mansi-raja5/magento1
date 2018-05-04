<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {  


        $this->_blockGroup = 'cybercom_banner';
        $this->_controller = 'adminhtml_bannergroups';
     
        parent::__construct();
        $this->setId('cybercom_banner_bannergroups_edit');
        $this->_updateButton('save', 'label', $this->__('Save Banner Group'));
        
  
        if($this->getRequest()->getParam('groupId')){
            //$this->_updateButton('delete', 'label', $this->__('Delete Banner'));
            $this->_addButton('delete', array(
                'label'     => Mage::helper('adminhtml')->__('Delete Banner Group'),
                'class'     => 'delete',
                'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to delete this bannergroup?')
                    .'\', \'' . $this->getDeleteUrl() . '\')',
            ));      
        }
    
    }  
     
    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {  
        if ($this->getId()) {
            return $this->__('Edit Banner Group (ID : '.$this->getid().')');
        }  
        else {

            return $this->__('New Banner Group');
        }  
    }  
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['groupId' => $this->getId()]);
    }    

    public function getId()
    {
        return $this->getRequest()->getParam('groupId');
    }      
}