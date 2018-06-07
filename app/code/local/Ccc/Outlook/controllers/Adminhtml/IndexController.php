<?php
class Ccc_Outlook_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('outlook/ordermail');        
        return $this;
    }
     
    protected function _isAllowed()
    {
        return true;
    }
    
    public function viewAction()
    {    
        $this->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('outlook/adminhtml_view'))
                ->renderLayout();
    }

    public function outlookloginAction()
    {
        $outlookModel = Mage::getModel("outlook/outlook");
        $outlookModel->init(); 
        if(isset($_SESSION["authority"]) && isset($_SESSION["auth_url"]))
        {
            $accessUrl = $_SESSION["authority"].$_SESSION["auth_url"];
            header("Location:".$accessUrl);
            exit;
        }
    }

    public function refreshtokenAction()
    {
    	try
    	{
            $orderModel = Mage::getModel("outlook/ordermail");
            $hasToken = $orderModel->refreshToken(); 

            $reload = $this->getRequest()->getParam('reload');
            if($reload)
            {
                Mage::getSingleton('adminhtml/session')->addSuccess("Token is refreshed!");
                $this->_redirect('*/index/outlook');   
            }

    		if($hasToken)
    		{
            	$response = array(
                        "responseType" 	=> "success",
                        "message"		=> "Token is refreshed!",  
                    );    		
        	}
        	else
        	{
            	$response = array(
                        "responseType" 	=> "failure",
                        "message"		=> "Token is not refreshed!",  
                    );    		
        	}
    	}
    	catch(Exception $e){
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
    	}   
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));      	
    }

    public function readmailAction()
    {
    	try
    	{
    		$orderModel        = Mage::getModel("outlook/ordermail");
        	$orderMailCount    = $orderModel->readMail(); 
            $result            = $orderModel->getMfr();
        	if($orderMailCount)
        	{
        		$dataString   = ($orderMailCount==1)?" mail is":" mails are";
        		$responseHtml =  $orderMailCount." ".$dataString." imported!";
        	}
        	else
        	{
        		$responseHtml = "No mails to read";
        	}

            $reload = $this->getRequest()->getParam('reload');
            if($reload)
            {
                Mage::getSingleton('adminhtml/session')->addSuccess($responseHtml);
                $this->_redirect('*/index/outlook');   
            }

            $response = array(
                        "responseType" 	=> "success",
                        "message"		=> "Mail Import Successfully Completed.",  
                        "content" 		=> array(
                            array(
                                "elementId" => "response-area-div",
                                "html"      => $responseHtml
                            ),
                            array(
                                "elementId" => "mail-list",
                                "html"      => ""
                            )                            

                        )
                    );
    	}
    	catch(Exception $e){
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
    	}   
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));    	
    }    

    public function listmailAction()
    {
        try
        {        
        	$orderId = $this->getRequest()->getParam('orderid');
        	$condition = " 1=1";      	
        	if($orderId)
        	{
        		$condition = "com.order_id = {$orderId}";
        	}
            
            $query = "SELECT 
                        com.entity_id as order_mail_id,
                        com.received_date,
                        com.from_email,
                        com.recipients,
                        com.subject,
                        com.read,
                        com.no_ack,
                        com.content,
                        coma.attachment,
                        coma.content_type,
                        cm.mfg as mfr_name
                        FROM `ccc_order_mail` com
                        LEFT JOIN  `ccc_order_mail_attachment` coma ON coma.entity_id = com.entity_id
                        LEFT JOIN `ccc_manufacturer` cm ON cm.entity_id = com.mfr_id
                        WHERE 1=1
                        ORDER BY received_date DESC";
            $orderMails = Mage::getSingleton("core/resource")->getConnection("read")->fetchAll($query);
             

            $dataHtml = "<style>#outlook_custom_block td,#outlook_custom_block th { padding: 5px; border: 1px solid #d6d6d6;word-break: break-all; }</style>"
                        ."<table><tr>"
                        ."<th>Date</th>"    
                        ."<th>From Email</th>"
                        ."<th>Recipients</th>"
                        ."<th>Subject</th>"
                        ."<th>Read</th>"
                        ."<th>Action</th>"
                        ."<th>Attachment</th>"
                        ."<th>No Ack</th>"
                    ."</tr>";

            if(count($orderMails))
            {
                $mailAttachmentModel = Mage::getModel("outlook/mailattachment");
                foreach ($orderMails as $_orderMail) 
                {
                    if($_orderMail['content_type'] == "application/pdf" || $_orderMail['content_type'] == "")
                    {
                        $dataHtml .= "<tr>";
                        $dataHtml .= "<td>".$_orderMail['received_date']."</td>";
                        $dataHtml .= "<td>".$_orderMail['from_email']."</td>";
                        $dataHtml .= "<td>".$_orderMail['recipients']."</td>";
                        $dataHtml .= "<td>".$_orderMail['subject']."</td>";
                        // $dataHtml .= "<td>".$_orderMail['mfr_name']."</td>";
                        $dataHtml .= "<td id='isread-".$_orderMail['order_mail_id']."'>".($_orderMail['read']==1 ? "Yes": "No")."</td>";

                        $dataHtml .= "<td>";
                        $dataHtml .= "<a onclick='openMailPreview(this)'>View Mail</a>";
                        $dataHtml .= "<span class='cls-mailbody' style='display:none'>".base64_encode($_orderMail['content'])."</span>";
                        $dataHtml .= "<input type='hidden' class='cls-ordermail-id' value='".$_orderMail['order_mail_id']."'>";
                        $dataHtml .= "</td>";

                        if($_orderMail['attachment'] != "")
                        {
                            $dataHtml .= "<td><a href='".$mailAttachmentModel->getAttachmentUrl().$_orderMail['attachment']."' target='_blank'>View Attachment</a></td>";
                            //$attachmentUrl = $_orderMail['attachment'];

                            //$dataHtml .= "<td><a onclick='getAttachmentUrl(this)' data-url='".$attachmentUrl."'' target='_blank'>View Attachment</a></td>";
                        }
                        else
                        {
                            $dataHtml .= "<td></td>";
                        }


                        $recipients = $_orderMail['recipients'];
                        $recipientsAry = explode(",", $recipients);

                        $ischecked = "";
                        if(in_array("Orders@paylessfurnitureny.com", $recipientsAry))
                        {            
                            if($_orderMail['no_ack'] == 1)
                                $ischecked = "checked";

                            $dataHtml .= "<td><input type='checkbox' onclick='setNoack(this)' data-id=".$_orderMail['order_mail_id']." name='noack[]' ".$ischecked." /></td>";
                        }
                        else
                        {
                            $dataHtml .= "<td></td>";
                        }
                        $dataHtml .= "</tr>";
                    }
                }
            }
            else
            {
                $dataHtml .= "<tr>";
                $dataHtml .= "<td colspan=7>No Records Found</td>";
                $dataHtml .= "</tr>";
            }
            
            $dataHtml .= "</table>";
            $dataHtml .= '<script type="text/javascript">'
            .'var ajaxObj  = new  Furnique.Method();'            
            .'ajaxObj.loaderElementId  = "loading-mask-core";'            
            .'function openMailPreview(r)'
            .'{'
            .'    var url = "";'
            .'    var dialogWindow = Dialog.info(null, {'
            .'        closable:true,'
            .'        resizable:false,'
            .'        draggable:true,'
            .'        className:"magento",'
            .'        windowClassName:"popup-window",'
            .'        title:"Mail Preview",'
            .'        top:50,'
            .'        width:600,'
            .'        height:500,'
            .'        zIndex:1000,'
            .'        recenterAuto:false,'
            .'        hideEffect:Element.hide,'
            .'        showEffect:Element.show,'
            .'        id:"browser_window",'
            .'        url:url,'
            .'        onClose:function (param, el) {'
            .'              orderMailId = jQuery(r).siblings(".cls-ordermail-id").val();'
            .'              var postData  = {"form_key" : "'.Mage::getSingleton('core/session')->getFormKey().'","orderMailId":orderMailId};'
            .'              var isReadUrl = "'.Mage::helper('adminhtml')->getUrl('adminhtml/index/isread/').'?ajax=true";'
            .'              ajaxObj.setUseType("url").setRequestType("post").setData(postData).setURL(isReadUrl).loadPage();'
            .'        }'
		    .'    });'
		    .'    content = jQuery(r).siblings(".cls-mailbody").html();'
		    .'    dialogWindow.getContent().update(atob(content));'
            .'}'
            .'function getAttachmentUrl(r)'
            .'{'
            .'  var attachmentUrl = jQuery(r).attr("data-url");'
            .'  window.location = "'.Mage::helper('adminhtml')->getUrl('adminhtml/index/download/').'?filename="+attachmentUrl;'
            .'}'
            .'function setNoack(r)'
            .'{'
            .'  var orderMailId = jQuery(r).attr("data-id");'
            .'  var noAck = 2;'
            .'  if(r.checked)'
            .'  {'
            .'      noAck = 1;'
            .'  }'
            .'  debugger;'
            .'  ajaxObj.showLoader();'
            .'  var postData  = {"form_key" : "'.Mage::getSingleton('core/session')->getFormKey().'","orderMailId":orderMailId, "noAck":noAck};'
            .'  var noAckUrl = "'.Mage::helper('adminhtml')->getUrl('adminhtml/index/setNoack/').'?ajax=true";'            
            .'  ajaxObj.setUseType("url").setRequestType("post").setData(postData).setURL(noAckUrl).loadPage();'             
            .'}'
            .'</script>';

            
            $response = array(
                        "responseType" => "success",
                        //"message" => "Mail List is displayed.",  
                        "content" => array(
                            array(
                                "elementId" => "mail-list",
                                //"position" => "append",
                                "html"      => $dataHtml
                            )
                        )
                    );
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));
    }

    public function isreadAction()
    {
        try
        {
            $postData = $this->getRequest()->getPost();
            $orderMailId = $postData['orderMailId'];
            $query = "UPDATE `ccc_order_mail` 
                        SET `read` = 1 
                        WHERE `entity_id` = $orderMailId";
            $orderMail = Mage::getSingleton("core/resource")->getConnection("core_write")->query($query);
            
            $yesHtml = "Yes";
            $response = array(
                        "responseType"  => "success",
                        "content"       => array(
                            array(
                                "elementId" => "isread-".$orderMailId,
                                "html"      => $yesHtml
                            ),                          
                        )
                    );
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));        
    }

    public function downloadattchmentAction()
    {
        try
        {
            $orderModel = Mage::getModel("outlook/ordermail");
            $isDownloaded = $orderModel->downloadAttchment(); 
            if($isDownloaded)
            {
                $responseHtml   =  "Mail Attchments are Downloaded!";
            }
            else
            {
                $responseHtml = "No attchment to Download";
            }

            $reload = $this->getRequest()->getParam('reload');
            if($reload)
            {
                Mage::getSingleton('adminhtml/session')->addSuccess($responseHtml);
                $this->_redirect('*/index/outlook');   
            }

            $response = array(
                        "responseType"  => "success",
                        "message"       => "Mail Import Successfully Completed.",  
                        "content"       => array(
                            array(
                                "elementId" => "response-area-div",
                                "html"      => $responseHtml
                            ),
                            array(
                                "elementId" => "mail-list",
                                "html"      => ""
                            )                            

                        )
                    );
        }
        catch(Exception $e){
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }   
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));  
    }

    public function truncateAction()
    {
        try{
            $query = "TRUNCATE ccc_order_mail;TRUNCATE ccc_order_mail_attachment;";
            $orderMail = Mage::getSingleton("core/resource")->getConnection("core_write")->query($query);
            $response = array(
                            "responseType"  => "success",
                            "message"       => "Tables are Truncated Successfully",  
                        );
        }
        catch(Exception $e){
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }      
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));  
    }

    public function downloadAction() 
    {
        $attachmentModel = Mage::getModel("outlook/mailattachment");
        $filepath = $attachmentModel->getAttachmentUrl();
        $fileName = $this->getRequest()->getParam('filename');
        $this->_prepareDownloadResponse($fileName, array('type' => 'filename', 'value' => $filepath));
    } 

    public function outlookAction()
    {
        $this->loadLayout()
                ->_initAction()
                ->_addContent($this->getLayout()->createBlock('outlook/adminhtml_outlook'))
                ->renderLayout();      
    }   

    public function setNoackAction()
    {
        try
        {
            $postData   = $this->getRequest()->getPost();
            $orderMailId = $postData['orderMailId'];
            $noAck       = $postData['noAck'];
            $query = "UPDATE `ccc_order_mail` 
                        SET `no_ack` = $noAck
                        WHERE `entity_id` = $orderMailId";
            $orderMail = Mage::getSingleton("core/resource")->getConnection("core_write")->query($query);
            
            if($noAck == 1)
                $message = "Order Mail is Acknowledged!";
            else
                $message = "Order Mail Acknowledgement is removed!";

            $response = array(
                        "responseType"  => "success",
                        "message"  => $message
                    );
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));           
    }

    public function automailAction()
    {
        try
        {            
            $automailModel = Mage::getModel("outlook/automail");
            $result = $automailModel->sendMail();
            $response = array(
                        "responseType"  => "success",
                        "message"  => "success"
                    );
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));         
    }

    public function getmfrAction()
    {
        try
        {            
            $ordermailModel = Mage::getModel("outlook/ordermail");
            $result = $ordermailModel->getMfr();
            $response = array(
                        "responseType"  => "success",
                        "message"  => $result." records updated!"
                    );
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));         
    }   

    public function getordertatusAction()
    {
        try
        {            
            $orderId = $this->getRequest()->getParam('orderid');
            $condition = "";
            if(isset($orderId) && $orderId != "")
            {
                $condition      = " AND order_id = $orderId";        
                $readConnection = Mage::getSingleton("core/resource")->getConnection("read");
                $query          = "SELECT order_status
                                    FROM `ccc_order_mail` 
                                    WHERE entity_id IN (SELECT max(entity_id) FROM `ccc_order_mail` where order_id != 0 group by order_id ORDER BY order_id,received_date DESC) 
                                        {$condition}";                                
                $orderMailstatus     = $readConnection->fetchOne($query);        


                $response = array(
                            "responseType"  => "success",
                            "message"  => $orderMailstatus
                        );
            }
            else{
                $response = array(
                    "responseType" => "failure",
                    "message" => "Please send Order Id!",
                );
            }            
        }
        catch(Exception $e)
        {
            $response = array(
                    "responseType" => "failure",
                    "message" => $e->getMessage(),
                );
        }        
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode($response));  
    }
}