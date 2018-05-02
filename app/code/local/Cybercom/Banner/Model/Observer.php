<?php
class Cybercom_Vendor_Model_Observer
{
    public function addNewButton($observer)
    {   
        $container = $observer->getBlock();
        // print_r($container->getType());
        // exit;
        if(null !== $container && $container->getType() == 'cybercom_vendor/adminhtml_vendors') {
            $data = array(
                'label'     => 'Button added via Observer',
                'class'     => '',
                'onclick'   => 'setLocation(\''  . Mage::helper('adminhtml')->getUrl('companymodule/adminhtml_controller/action') . '\')',
            );
            $container->addButton('cybercom_vendor_mansi', $data);
        }

        return $this;
    }
}