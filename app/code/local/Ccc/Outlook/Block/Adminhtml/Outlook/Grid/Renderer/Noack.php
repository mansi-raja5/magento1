<?php
class Ccc_Outlook_Block_Adminhtml_Outlook_Grid_Renderer_Noack extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime
{
    public function render(Varien_Object $row)
    {
    	$recipients = $row->getRecipients();
    	$recipientsAry = explode(",", $recipients);

    	$ischecked = "";
    	if(in_array("Orders@paylessfurnitureny.com", $recipientsAry))
    	{
    			$ischecked = "checked";
    	}

		$dataHtml = "<input type='checkbox' onclick='ackThis(this)' data-id=".$row->getId()." name='noack[]' ".$ischecked." />";
		return $dataHtml;
    }
}