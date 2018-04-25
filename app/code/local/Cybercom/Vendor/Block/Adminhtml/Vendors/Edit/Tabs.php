<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {

        parent::__construct();
        $this->setId('cybercom_vendor_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('cybercom_vendor')->__('Vendor Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('cybercom_vendor_vendordetail_edit', array(
            'label' => Mage::helper('cybercom_vendor')->__('General Information'),
            'title' => Mage::helper('cybercom_vendor')->__('General Information'),
            'class' =>   'ajax',
            'url'=>$this->getUrl('*/*/vendorDetails', array('_current'=>true)),
            'active'    => true
            //'content' => $this->getLayout()->createBlock('cybercom_vendor/adminhtml_vendors_edit')->setData('action', $this->getUrl('*/*/save')),
        ));



        $this->addTab('cybercom_vendor_price_grid',array(
            'label'=>Mage::helper('cybercom_vendor')->__('Vendor Price'),
            'title'=>Mage::helper('cybercom_vendor')->__('Vendor Price'),
            'class' =>   'ajax',
            'url'=>$this->getUrl('*/*/vendorPrice', array('_current'=>true)),

        ));
        return parent::_beforeToHtml();


    }

   /* protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }*/
}