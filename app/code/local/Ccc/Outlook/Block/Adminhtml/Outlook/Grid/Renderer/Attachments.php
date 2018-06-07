<?php
class Ccc_Outlook_Block_Adminhtml_Outlook_Grid_Renderer_Attachments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
    	
		$attachments = $row->getattachments();
		if(isset($attachments) && $attachments != "")
        {
			$dataAry = array();
        	$mailAttachmentModel = Mage::getModel("outlook/mailattachment");
	    	$attachments = explode(",", $attachments);
	    	$count = 1;
			foreach ($attachments as $attachment) 
			{
				$dataAry[] = "<a href='".$mailAttachmentModel->getAttachmentUrl().$attachment."' target='_blank'>$count</a>";
				$count++;
			}
			$dataHtml = implode(", ", $dataAry);
			return $dataHtml;
		}
		else
		{
			return false;
		}
    }
}