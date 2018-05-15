<?php 
class Ccc_Import_Block_Adminhtml_Upload_Grid_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return '<a href="'.$this->getUrl('/adminhtml_upload/edit', array('type' => $row->getId())).'">Edit</a> || 
                <a href="'.$this->getUrl('/adminhtml_upload/save', array('type' => $row->getId())).'">Read Csv</a> || 
                <a href="'.$this->getUrl('*/adminhtml_process/index', array('type' => $row->getId())).'">Import Csv</a> || 
                <a href="'.$this->getUrl('/adminhtml_upload/remove', array('type' => $row->getId())).'">Empty Unused Data</a>';
     
    }
}
?>