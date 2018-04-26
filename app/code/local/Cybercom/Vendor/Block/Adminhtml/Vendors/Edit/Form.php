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

      
      //Set vendor Id for all tabs
      if(Mage::registry('cybercom_vendor'))
            $model = Mage::registry('cybercom_vendor');
      else if (Mage::getSingleton('core/session')->getVendordata())
        $model = Mage::getSingleton('core/session')->getVendordata();      
      
      $this->setForm($form);
      if ($model->getId()) {        
          $form->addField('id', 'hidden', array(
              'name' => 'id',
          ));
      } 
      else
        Mage::getSingleton('core/session')->setActiveTab('general_information');      
      
      $form->setValues($model->getData());
      $form->setUseContainer(true);
      $this->setForm($form);      
      return parent::_prepareForm();
  } 
}