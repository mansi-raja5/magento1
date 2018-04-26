<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

      $form = new Varien_Data_Form(array(
                  'id' => 'edit_form',
                  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                  'method' => 'post',
                  'enctype' => 'multipart/form-data'
                )
                );

      $form->setUseContainer(true);
      $this->setForm($form);

      // $mansi = Mage::registry('cybercom_vendor');
      // Mage::register('cybercom_vendor_again', "222");
      // $mansi = Mage::registry('cybercom_vendor_again');
      // echo "outer form";
      //   echo "<pre>";
      //   print_r($mansi);      
      return parent::_prepareForm();
  } 
}