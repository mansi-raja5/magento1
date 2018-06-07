<?php
class Ccc_Outlook_Model_Outlook 
{
	protected $_mailCount = 0;

    const TOKEN_FILE_PATH      = "/outlook";

    public function init()
    {
        //Intialize Session for outlook token
		$_SESSION["client_id"] 		= "5b72dfb8-506c-4105-8268-a0c60bf3ddf8";
		$_SESSION["client_secret"] 	= "opI77#(nqmdxPALBRJ177~#";
		$_SESSION["redirect_uri"] 	= "https://www.1stopbedrooms.com/ipn/outlook/index";
		$_SESSION["authority"] 		= "https://login.microsoftonline.com";
		$_SESSION["scopes"] 		= array("offline_access", "openid");

		if(true) {
		array_push($_SESSION["scopes"], "https://outlook.office.com/mail.read");
		}

		if(true) {
		array_push($_SESSION["scopes"], "https://outlook.office.com/mail.send");
		}

		$_SESSION["auth_url"] = "/common/oauth2/v2.0/authorize";
		$_SESSION["auth_url"] .= "?client_id=".$_SESSION["client_id"];
		$_SESSION["auth_url"] .= "&redirect_uri=".$_SESSION["redirect_uri"];
		$_SESSION["auth_url"] .= "&response_type=code&scope=".implode(" ", $_SESSION["scopes"]);

		$_SESSION["token_url"] 	= "/common/oauth2/v2.0/token";
		$_SESSION["api_url"] 	= "https://outlook.office.com/api/v2.0";
    }

    public function getTokenFilepath()
    {
    	return Mage::getBaseDir('var') . self::TOKEN_FILE_PATH;
    }

    public function getMailCount()
    {
        return $this->_mailCount;
    }

    //Setter - Set Mail details which are having an attachment
    public function setAttachmentData($attchmentMailids)
    {
    	$this->_attachmentMailids = $attchmentMailids;
        return $this->_attachmentMailids;
    }    

    //Getter - Get Mail details which are having an attachment
    public function getAttachmentData()
    {
        return $this->_attachmentMailids;
    }        

    public function token()
    {    	
	    $text = file_exists($this->getTokenFilepath()."/office_auth_config.txt") ? file_get_contents($this->getTokenFilepath()."/office_auth_config.txt") : null;
	    if($text != null && strlen($text) > 0) {
	        return json_decode($text);
	    }
	    return null;
    }

	public function flush_token() {
	    file_put_contents($this->getTokenFilepath()."/office_auth_config.txt", "");
	    $_SESSION["user_id"] = "";
	    $_SESSION["mail_id"] = "";
	}

	public function store_token($o) {
	    $fileCharCount = file_put_contents($this->getTokenFilepath()."/office_auth_config.txt", json_encode($o));
	    return $fileCharCount;
	}    

    public function makeGuid()
    {
	    if (function_exists('com_create_guid')) {
	        error_log("Using 'com_create_guid'.");
	        return strtolower(trim(com_create_guid(), '{}'));
	    }
	    else {
	        $charid = strtolower(md5(uniqid(rand(), true)));
	        $hyphen = chr(45);
	        $uuid = substr($charid, 0, 8).$hyphen
	            .substr($charid, 8, 4).$hyphen
	            .substr($charid, 12, 4).$hyphen
	            .substr($charid, 16, 4).$hyphen
	            .substr($charid, 20, 12);
	        return $uuid;
	    }
    }

	public function get_user_id() {
	    if(isset($_SESSION["user_id"]) && strlen($_SESSION["user_id"]) > 0) {
	        return $_SESSION["user_id"];
	    }
	    $this->view_profile(true);
	    $response = json_decode(file_get_contents($this->getTokenFilepath()."/office_user_data.txt"));
	    $_SESSION["user_id"] = $response->Id;
	    return $response->Id;
	}

    public function get_user_email()
    {
	    if(isset($_SESSION["user_email"]) && strlen($_SESSION["user_email"]) > 0) {
	        return $_SESSION["user_email"];
	    }
	    $this->view_profile(true);
	    $response = json_decode(file_get_contents($this->getTokenFilepath()."/office_user_data.txt"));
	    $_SESSION["user_email"] = $response->EmailAddress;
	    return $response->EmailAddress;
    }

    public function view_profile($skipPrint = false) 
    {
	    $headers = array(
	        "User-Agent: php-tutorial/1.0",
	        "Authorization: Bearer ".$this->token()->access_token,
	        "Accept: application/json",
	        "client-request-id: ".$this->makeGuid(),
	        "return-client-request-id: true"
	    );
	    $outlookApiUrl = $_SESSION["api_url"] . "/Me";
	    $response = $this->runCurl($outlookApiUrl, null, $headers);
	    $response = explode("\n", trim($response));
	    $response = $response[count($response) - 1];
	    file_put_contents($this->getTokenFilepath()."/office_user_data.txt", $response);
	    $response = json_decode($response);

	    $_SESSION["user_id"] = $response->Id;
	    $_SESSION["mail_id"] = $response->MailboxGuid;
	    $_SESSION["user_email"] = $response->EmailAddress;
	    if(!$skipPrint) {
	        echo "<pre>"; print_r($response); echo "</pre>";
	    }
	}

	public function runCurl($url, $post = null, $headers = null) 
	{
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, $post == null ? 0 : 1);
	    if($post != null) {
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	    }
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    if($headers != null) {
	        curl_setopt($ch, CURLOPT_HEADER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    }
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    if($http_code >= 400) {
	        echo "Error executing request to Office365 api with error code=$http_code<br/><br/>\n\n";
	        echo "<pre>"; print_r($response); echo "</pre>";
	        die();
	    }
	    return $response;
	}

	public function readmail()
	{
        $query = "SELECT * FROM `ccc_order_mail` ORDER BY received_date DESC LIMIT 1";
        $orderMail = Mage::getSingleton("core/resource")->getConnection("read")->fetchRow($query);	

        $headers = array(
            "User-Agent: php-tutorial/1.0",
            "Authorization: Bearer ".$this->token()->access_token,
            "Accept: application/json",
            "client-request-id: ".$this->makeGuid(),
            "return-client-request-id: true",
            "X-AnchorMailbox: ". $this->get_user_email()
        );

        $top  = 10;
        $skip = 0;
        //SELECT message_id, GROUP_CONCAT(`entity_id`) FROM `ccc_order_mail` GROUP BY `message_id` ORDER BY entity_id ASC LIMIT 100

        $search = array (
            // Only return selected fields
            "\$select" => "Subject,ReceivedDateTime,Sender,From,ToRecipients,HasAttachments,BodyPreview,Body,IsRead",
            // Sort by ReceivedDateTime, newest first
            "\$orderby" => "ReceivedDateTime ASC",
            // Return at most n results
            "\$top" => $top, "\$skip" => $skip

        );

		if($orderMail)
		{
			$date = date("Y-m-d", strtotime($orderMail['received_date']));
			$time = date("H:i:m", strtotime($orderMail['received_date']));
			$receivedDate = $date.'T'.$time.'Z';

			$search["\$filter"] = "ReceivedDateTime ge {$receivedDate}";
		}        

        $outlookApiUrl = $_SESSION["api_url"] . "/Me/MailFolders/inbox/Messages?" . http_build_query($search);
        $response = $this->runCurl($outlookApiUrl, null, $headers);

        $response = explode("\n", trim($response));
        $response = $response[count($response) - 1];
        $response = json_decode($response, true);



        if(isset($response["value"]) && count($response["value"]) > 0) 
        {
        	$cnt = 0;
            foreach ($response["value"] as $mail) 
            {
            	if($orderMail && $cnt == 0)
            	{
            		$cnt++;
            		continue;
            	}

            	$orderMail = Mage::getModel('outlook/ordermail');

            	$recipients = Array();
            	foreach ($mail["ToRecipients"] as $_Recipients) {
            		$recipients[] = $_Recipients['EmailAddress']['Address'];	
            	}
            	$allRecipients = implode(",", $recipients);

            	$data = array();
            	
                $data['message_id']         = $mail["Id"];
                $data['received_date']      = date('Y-m-d H:i:s',strtotime($mail["ReceivedDateTime"]));
                $data['from_email']         = $mail["From"]["EmailAddress"]["Address"];
                $data['recipients']         = $allRecipients;
                $data['subject']            = $mail["Subject"];
                $data['content']            = $mail["Body"]["Content"];
                $data['has_attachment']     = $mail["HasAttachments"]?1:2;
                // $data['read']            	= $mail["IsRead"]?1:2;  //1 = true and 2 = false
                $data['read']            	= 2;  //1 = true and 2 = false


                if(strpos($mail['Subject'], "PO#") != false)
                {
	                $subjectAry	 = explode("PO#", $mail['Subject']);
	                if(isset($subjectAry[1]))
	                {
						$data['order_id']		= (int)$subjectAry[1];
	                }
	            }

                $data['create_date']        = date("Y-m-d H:i:s");

				//Fetch Order status from subject from magento system/configuration/outlook
				$order_status = "";
				$orderStatus = Mage::getStoreConfig('outlook/outlook_group/outlook_order_status');
				$orderStatus = unserialize($orderStatus);
				if (is_array($orderStatus) || sizeof($orderStatus)) 
				{
					foreach ($orderStatus as $_orderStatus) 
					{
						$isFound = strpos(strtolower($mail["Subject"]),strtolower($_orderStatus['subject']));
						if($isFound !== false && $isFound == 0)  //status should be found and It should be found from the first character of mail subject
						{
							$order_status =  $_orderStatus['status'];
							break;
						}
					}
				}
                $data['order_status']       = $order_status;

                $orderMail->setData($data);
                $orderMail->save();
                $this->_mailCount++;	
                     
            }

            if(count($response["value"]) == $top)
            {
            	return true;
            }
        }

        return false;
	}

    public function refreshToken()
    {
    	$this->init();
        $token_request_data = array (
            "grant_type"    => "refresh_token",
            "refresh_token" => $this->token()->refresh_token,
            "redirect_uri"  => $_SESSION["redirect_uri"],
            "scope"         => implode(" ", $_SESSION["scopes"]),
            "client_id"     => $_SESSION["client_id"],
            "client_secret" => $_SESSION["client_secret"]
        );
        $body = http_build_query($token_request_data);

        $response = $this->runCurl($_SESSION["authority"].$_SESSION["token_url"], $body);
        $response = json_decode($response);
        $fileCharCount = $this->store_token($response);

        file_put_contents($this->getTokenFilepath()."/office_access_token.txt", $response->access_token);
        return $fileCharCount;
        //header("Location: " . $_SESSION["redirect_uri"]);
    }	

    public function downloadAttchment()
    {    	
    	if(count($this->_attachmentMailids))
    	{
    		$attachmentModel = Mage::getModel('outlook/mailattachment');
		    //Create Outlook Folder If Not exist
		    if(!file_exists($attachmentModel->getAttachmentpath())) 
		    {
				$ioFile = new Varien_Io_File();
				$ioFile->checkAndCreateFolder($attachmentModel->getAttachmentpath());
		    }


		    //Mail Was Loop Having An Attchment
    		foreach ($this->_attachmentMailids as $_mail) 
    		{
			    $messageId 	= $_mail["message_id"]; 			   

			    //Create Message Id Wise Folders inside media/outlook
			    $folder = $attachmentModel->getAttachmentpath(). DS .md5($messageId);
			    if(!file_exists($folder)) {
					$ioFile = new Varien_Io_File();
					$ioFile->checkAndCreateFolder($folder);			        
			    } 

			    $userID = $this->get_user_id();
			    $headers = array(
			        "User-Agent: php-tutorial/1.0",
			        "Authorization: Bearer ".$this->token()->access_token,
			        "Accept: application/json",
			        "client-request-id: ".$this->makeGuid(),
			        "return-client-request-id: true",
			        "X-AnchorMailbox: ". $this->get_user_email()
			    ); 

			    $outlookApiUrl = $_SESSION["api_url"] . "/Users('$userID')/Messages('$messageId')/Attachments";
			    $response = $this->runCurl($outlookApiUrl, null, $headers);
			    $response = explode("\n", trim($response));
			    $response = $response[count($response) - 1];
			    $response = json_decode($response, true);
			    
			    //Attachment Wise Loop
			    foreach ($response["value"] as $attachment) 
			    {
			        $attachmentName = "/".md5($attachment["ContentId"])."-".$attachment["Name"];
			        $to_file = $folder.$attachmentName;
			        file_put_contents($to_file, base64_decode($attachment["ContentBytes"]));

			        $attachmentName = "/".md5($messageId)."/".md5($attachment["ContentId"])."-".$attachment["Name"];
            	
	                $data['entity_id']         = $_mail["entity_id"];
	                $data['attachment']        = $attachmentName;
	                $data['content_id']        = $attachment["ContentId"];
	                $data['content_type']      = $attachment["ContentType"];
	                $data['download_date']     = date("Y-m-d H:i:s");

		        	$this->saveAttchment($data);
			    }
			    $orderMail = Mage::getModel('outlook/ordermail');
                $orderMailData['is_downloaded'] = 1;
                $orderMailData['entity_id'] 	= $_mail["entity_id"];
                $orderMail->setData($orderMailData);
                $orderMail->save();			    
    		}
    		return true;		   
    	}
    	return false;
    }

    public function saveAttchment($data)
    {
    	$attachmentModel = Mage::getModel('outlook/mailattachment');
    	$attachmentModel->setData($data);
    	$attachmentModel->save();
    	return true;
    }
}
?>