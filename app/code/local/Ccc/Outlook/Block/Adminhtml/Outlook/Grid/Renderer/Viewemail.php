<?php
class Ccc_Outlook_Block_Adminhtml_Outlook_Grid_Renderer_Viewemail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
		$dataHtml = "<a onclick='openMailPreview(this)'>View Mail</a>";
		$dataHtml .= "<span class='cls-mailbody' style='display:none'>".base64_encode($row->getContent())."</span>";
		$dataHtml .= "<input type='hidden' class='cls-ordermail-id' value='".$row->getId()."'>";
		return $dataHtml;
    }
}