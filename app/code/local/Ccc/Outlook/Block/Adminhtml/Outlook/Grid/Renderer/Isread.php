<?php
class Ccc_Outlook_Block_Adminhtml_Outlook_Grid_Renderer_Isread extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
		$dataHtml = "<span id='isread-".$row->getId()."'>".($row->getRead()==1 ? "Yes": "No")."</span>";
		return $dataHtml;
    }
}