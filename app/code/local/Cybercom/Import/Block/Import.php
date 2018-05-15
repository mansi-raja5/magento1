<?php
class Ccc_Import_Block_Import extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getImport()     
     { 
        if (!$this->hasData('import')) {
            $this->setData('import', Mage::registry('import'));
        }
        return $this->getData('import');
        
    }
}