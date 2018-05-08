<?php
class Cybercom_Item_Block_Adminhtml_Items_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {  

    	Mage::getSingleton('core/session')->setActiveTab('general_information');

		if(Mage::registry('cybercom_item'))
        	$model = Mage::registry('cybercom_item');
		else if (Mage::getSingleton('core/session')->getItemdata())
			$model = Mage::getSingleton('core/session')->getItemdata();


        $form = new Varien_Data_Form();
     	$this->setForm($form);
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('checkout')->__('Item Information'),
            'class'     => 'fieldset-wide',
        ));
     
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        } 
     
        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => Mage::helper('cybercom_item')->__('Name'),
            'title'     => Mage::helper('cybercom_item')->__('Name'),
            'required'  => true,
            'tabindex'  => 1
        ));
   

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);   
	     
        return parent::_prepareForm();
    }  	    
}
