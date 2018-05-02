<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cybercom_bannergroups_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('cybercom_banner')->__('Banner Group Information'));
    }

    protected function _beforeToHtml()
    {

        $this->addTab('general_information',array(
            'label' =>Mage::helper('cybercom_banner')->__('General Information'),
            'title'=>Mage::helper('cybercom_banner')->__('General Information'),
            'class' =>   'ajax',
            'url'   =>   $this->getUrl('*/*/custom',array('_current'=>true)),
            'active'    => Mage::getSingleton('core/session')->getActiveTab()=='general_information'?true:false
        ));

       $this->addTab('categories', array(
            'label' => Mage::helper('cybercom_banner')->__('Associated categories'),
            'url'   => $this->getUrl('*/*/categories', array('_current' => true)),
            'class'    => 'ajax'
        ));
       
        $this->_updateActiveTab();     
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