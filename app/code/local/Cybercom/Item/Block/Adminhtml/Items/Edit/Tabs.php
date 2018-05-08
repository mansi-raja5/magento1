<?php
class Cybercom_Item_Block_Adminhtml_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {

        parent::__construct();
        $this->setId('cybercom_item_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('cybercom_item')->__('Item Information'));
    }

    protected function _beforeToHtml()
    {

        $this->addTab('general_information',array(
            'label' =>Mage::helper('cybercom_item')->__('General Information'),
            'title'=>Mage::helper('cybercom_item')->__('General Information'),
            'class' =>   'ajax',
            'url'   =>   $this->getUrl('*/*/general',array('_current'=>true)),
            'active'    => Mage::getSingleton('core/session')->getActiveTab()=='general_information'?true:false
        ));

        $this->addTab('images',array(
            'label' =>Mage::helper('cybercom_item')->__('Item Images'),
            'title'=>Mage::helper('cybercom_item')->__('Item Images'),
            'class' =>   'ajax',
            'url'   =>   $this->getUrl('*/*/images',array('_current'=>true)),
            'active'    => Mage::getSingleton('core/session')->getActiveTab()=='images'?true:false
        ));        
       
        //$this->_updateActiveTab();     
        return parent::_beforeToHtml();


    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}