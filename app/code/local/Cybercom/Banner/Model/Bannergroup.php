<?php
class Cybercom_Banner_Model_Bannergroup extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {  
        $this->_init('cybercom_banner/bannergroup');
    }  
	public function fetchAllGroups()
	{

	  $groupCollection = Mage::getModel('cybercom_banner/bannergroup')->getCollection();

	  $groupAry = array(array('value'=>-1, 'label'=>'Please Select'));

	  foreach ($groupCollection as $group){

	        $groupAry[] = array('value'=>$group['group_id'],'label'=>$group['name']);
	  }

	  return $groupAry;

	}      
}
?>