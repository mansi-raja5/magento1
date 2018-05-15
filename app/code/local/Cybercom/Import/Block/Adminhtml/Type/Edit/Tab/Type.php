<?php 
class Cybercom_Import_Block_Adminhtml_Type_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form
{
    
  protected function _prepareForm()
  {   
      $type = Mage::registry('current_type'); 
      $form = new Varien_Data_Form();    
      //$form->setUseContainer(true);
      $this->setForm($form);
      
      $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('import')->__('Import')));
       
       $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('import')->__('Name'),          
          'required'  => true,
          'name'      => 'import_type[name]',
      ));
      
      $fieldset->addField('record_load_interval', 'text', array(
          'label'       => Mage::helper('import')->__('Request Interval (In Sec. )'), 
          'name'        => 'import_type[record_load_interval]',
          'class'     =>  'validate-number',
      ));
      
      $fieldset->addField('per_load_item', 'text', array(
          'label'       => Mage::helper('import')->__('Records Pre Request'),          
          'name'        => 'import_type[per_load_item]',
          'class'     =>  'validate-number',
      ));
      
      $fieldset->addField('is_processing', 'select', array(
          'label'       => Mage::helper('import')->__('Is Processing'),          
          'name'        => 'import_type[is_processing]',
          'values'      => Mage::getModel('import/import_type')->getProcessingOption()      
      ));
      
      
      $fieldset->addField('class_name', 'text', array(
          'label'     => Mage::helper('import')->__('Class Name'),
          'name'      => 'import_type[class_name]',
      ));
      
      $fieldset->addField('import_file_name', 'text', array(
          'label'     => Mage::helper('import')->__('Import File Name'),
          'name'      => 'import_type[import_file_name]',
      ));
      
      $fieldset->addField('note', 'textarea', array(
          'label'     => Mage::helper('import')->__('Note'),
          'name'      => 'import_type[note]',
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
