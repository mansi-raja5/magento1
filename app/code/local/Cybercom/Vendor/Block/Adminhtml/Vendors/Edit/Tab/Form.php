<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {  

    	Mage::getSingleton('core/session')->setActiveTab('general_information');
    	// echo Mage::getSingleton('core/session')->getActiveTab();
		if(Mage::registry('cybercom_vendor'))
        	$model = Mage::registry('cybercom_vendor');
		else if (Mage::getSingleton('core/session')->getVendordata())
			$model = Mage::getSingleton('core/session')->getVendordata();


        $form = new Varien_Data_Form();
     	$this->setForm($form);
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Vendor Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name' => 'id',
            ));
        } 
     
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('checkout')->__('Name'),
            'title'     => Mage::helper('checkout')->__('Name'),
            'required'  => true,
            'tabindex'  => 1
        ));
   
        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => Mage::helper('checkout')->__('Status'),
            'title'     => Mage::helper('checkout')->__('Status'),
            'values'    => array(0=>'Disabled',1 => 'Enabled'),
            'required'  => true,
            'after_element_html' => '<small>Enabled / Disabled </small>',       
            'tabindex' => 1   
        )); 

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);   
	     
        return parent::_prepareForm();
    }  	    
}
