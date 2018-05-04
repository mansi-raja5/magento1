<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {  
        if(Mage::registry('bannerGroupData'))
            $bannerGroupModel = Mage::registry('bannerGroupData');


        $form = new Varien_Data_Form(array(
                  'id' => 'edit_form',
                  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                  'method' => 'post',
                  'enctype' => 'multipart/form-data'
                ));

        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('cybercom_banner')->__('Banner Group Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($bannerGroupModel && $bannerGroupModel->getId()) {
            $fieldset->addField('group_id', 'hidden', array(
                'name' => 'group_id',
            ));
        }     
     
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('cybercom_banner')->__('Group Name'),
            'title'     => Mage::helper('cybercom_banner')->__('Group Name'),
            'required'  => true,
            'tabindex'  => 1
        ));         

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('cybercom_banner')->__('Group Code'),
            'title'     => Mage::helper('cybercom_banner')->__('Group Code'),
            'required'  => true,
            'tabindex'  => 2
        ));

        $fieldset->addField('height', 'text', array(
            'name'      => 'height',
            'label'     => Mage::helper('cybercom_banner')->__('Height'),
            'title'     => Mage::helper('cybercom_banner')->__('Height'),
            'required'  => true,
            'tabindex'  => 3
        )); 

        $fieldset->addField('width', 'text', array(
            'name'      => 'width',
            'label'     => Mage::helper('cybercom_banner')->__('Width'),
            'title'     => Mage::helper('cybercom_banner')->__('Width'),
            'required'  => true,
            'tabindex'  => 4
        ));    

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('cybercom_banner')->__('Description'),
            'title'     => Mage::helper('cybercom_banner')->__('Description'),
            'required'  => true,
            'tabindex'  => 5
        ));    
   
        $fieldset->addField('parent_id','select',array(
            'name'  => "parent_id",
            'label'     => Mage::helper('cybercom_banner')->__('Parent Group'),
            'title'     => Mage::helper('cybercom_banner')->__('Parent Group'),
            'required'  => true,
            'tabindex'  => 6,          
            'values'    => Mage::getModel('cybercom_banner/bannergroup')->fetchAllGroups(),
        ));

        if($bannerGroupModel)
            $form->setValues($bannerGroupModel->getData());
        $form->setUseContainer(true);
        $this->setForm($form);   
         
        return parent::_prepareForm();
    } 
}