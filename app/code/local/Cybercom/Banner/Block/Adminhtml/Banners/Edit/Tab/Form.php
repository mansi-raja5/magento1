<?php
class Cybercom_Banner_Block_Adminhtml_Banners_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
            'legend'    => Mage::helper('checkout')->__('Banner Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
        }     
     
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Name'),
            'title'     => Mage::helper('checkout')->__('Name'),
            'required'  => true,
            'tabindex'  => 1
        ));

        $fieldset->addField('image', 'image', array(
            'name'      => 'image',
            'label'     => Mage::helper('cybercom_banner')->__('Banner Image'),
            'title'     => Mage::helper('cybercom_banner')->__('Banner Image'),
            'required'  => true,
            'tabindex'  => 2
        ));   

        $fieldset->addField('url', 'text', array(
            'name'      => 'url',
            'label'     => Mage::helper('checkout')->__('Redirct URL'),
            'title'     => Mage::helper('checkout')->__('Redirct URL'),
            'required'  => true,
            'tabindex'  => 3
        ));

        $fieldset->addField('sort_order','select',array(
            'name'      => 'sort_order',
            'label'     => Mage::helper('checkout')->__('Sort Order'),
            'title'     => Mage::helper('checkout')->__('Sort Order'),
            'values'    => array(0=>'0',1 => '1',2=>'2',3 => '3'),
            'required'  => true,       
            'tabindex'  => 4         
        ));
   
        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('checkout')->__('Status'),
            'title'     => Mage::helper('checkout')->__('Status'),
            'values'    => array(0=>'Inactive',1 => 'Active'),
            'required'  => true,
            'after_element_html' => '<small>Active / Inactive </small>',       
            'tabindex'  => 5
        )); 

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);   
	     
        return parent::_prepareForm();
    }  	    
}
