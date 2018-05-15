<?php
class Cybercom_Skeleton_Block_Adminhtml_Myblocks extends Mage_Core_Block_Template
{
    public function __construct()
    {
        $this->_headerText = "Cybercom Skelton Details";
        parent::__construct();
    	echo "Block Child Constructor is getting executed!!<br>";
        $this->setTemplate('cybercom_skeleton/adminhtml/test.phtml');
        // exit;
        //$this->_removeButton('add');
    }      
    public function _prepareLayout(){

    	echo "preparing layout<br>";
    	echo "While this happens at a stage of the block’s lifecycle that’s very close to the _construct method, it only happens for block objects that have been added to the layout. ";
    	echo "If a block’s instantiated in a way that doesn’t involve createBlock or setLayout, this lifecycle callback will not be called, while the _construct method will always be called.";

    }
    public function _beforeToHtml(){
    	echo "before to html is calling!!<br>";    	
    }
    public function _afterToHtml($html){
    	Mage::Log("I just rendered " . $this->getName());	
    	return $html."after to html is calling!!<br>";    	    	
    }
    // public function _toHtml(){
    //Notice this method is defined with the final keyword. This means no child class is allowed to redefine its own toHtml method.
    // 	return "tohtml is calling <br>";
    // }

	//In Magento, a layout is a nested tree structure of blocks, with parent blocks rendering child blocks. When a parent renders one of its children (through a call to $this->getChildHtml('name')), this method is called immediately before rendering the child.

	//This method isn’t in use anywhere in core Magento code, so it’s likely one of those legacy methods they’re keeping around for backward compatibility. You’ll probably never encounter it, but it’s worth knowing about.    
    public function _beforeChildToHtml($name, $block){
    	return $name;
    }
    public function mansi(){
    	echo "mansi is called!!";
    }
    public function mansiRaja(){
    	echo "mansiRaja is called!!";
    }    
}