<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {  

    	Mage::getSingleton('core/session')->setActiveTab('general_information');

		if(Mage::registry('cybercom_banner'))
        	$model = Mage::registry('cybercom_banner');
		else if (Mage::getSingleton('core/session')->getBannerdata())
			$model = Mage::getSingleton('core/session')->getBannerdata();


        $form = new Varien_Data_Form();
     	$this->setForm($form);
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Banner Group Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('Cybercom_Banner_Block_Adminhtml_Bannergroups_Edit_Tab_Formid', 'hidden', array(
                'name' => 'group_id',
            ));
        }     
     
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Group Name'),
            'title'     => Mage::helper('checkout')->__('Group Name'),
            'required'  => true,
            'tabindex'  => 1
        ));         

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('checkout')->__('Group Code'),
            'title'     => Mage::helper('checkout')->__('Group Code'),
            'required'  => true,
            'tabindex'  => 2
        ));

        $fieldset->addField('height', 'text', array(
            'name'      => 'height',
            'label'     => Mage::helper('checkout')->__('Height'),
            'title'     => Mage::helper('checkout')->__('Height'),
            'required'  => true,
            'tabindex'  => 3
        )); 

        $fieldset->addField('width', 'text', array(
            'name'      => 'width',
            'label'     => Mage::helper('checkout')->__('Width'),
            'title'     => Mage::helper('checkout')->__('Width'),
            'required'  => true,
            'tabindex'  => 4
        ));    

        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('checkout')->__('Description'),
            'title'     => Mage::helper('checkout')->__('Description'),
            'required'  => true,
            'tabindex'  => 5
        ));    
   


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);   
	     
        return parent::_prepareForm();
    }  	    
}
