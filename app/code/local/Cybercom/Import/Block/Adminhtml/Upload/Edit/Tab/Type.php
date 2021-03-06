<?php 
class Cybercom_Import_Block_Adminhtml_Upload_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form
{
    
  protected function _prepareForm()
  {   
      $type = Mage::registry('current_type'); 
      $form = new Varien_Data_Form();    
      //$form->setUseContainer(true);
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('import')->__($type->getName())));
       
      $fieldset->addField('import_csv', 'file', array(
          'label'     => Mage::helper('import')->__('Choose File'),
          'name'      => 'import_csv',
      ));

      if(Mage::getSingleton('adminhtml/session')->getTypeData()){
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTypeData());
          Mage::getSingleton('adminhtml/session')->setTypeData(null);
      } elseif ( Mage::registry('current_type') ) {
          $form->setValues(Mage::registry('current_type')->getData());
      }
      
      return parent::_prepareForm();
  }
}
