<?php
class Ccc_Outlook_OutlookController extends Mage_Core_Controller_Front_Action
{
   	public function indexAction()
   	{
	    echo "<pre>";print_r($this->getRequest()->getParam('code'));echo "</pre>";

	    if($this->getRequest()->getParam('code'))
	    {
		    $outlook   = Mage::getModel('outlook/outlook');
		    $outlook->init();
		    $token_request_data = array 
		    (
		        "grant_type" 	=> "authorization_code",
		        "code" 			=> $this->getRequest()->getParam('code'),
		        "redirect_uri" 	=> $_SESSION["redirect_uri"],
		        "scope" 		=> implode(" ", $_SESSION["scopes"]),
		        "client_id" 	=> $_SESSION["client_id"],
		        "client_secret" => $_SESSION["client_secret"]
		    );
		    $body = http_build_query($token_request_data);
		    $response = $outlook->runCurl($_SESSION["authority"].$_SESSION["token_url"], $body);
		    $response = json_decode($response);

		    $outlook->store_token($response);
		    file_put_contents($outlook->getTokenFilepath()."/office_active_user_id.txt", $outlook->get_user_id());
		    file_put_contents($outlook->getTokenFilepath()."/office_access_token.txt", $response->access_token);
		    echo "<pre>";
		    print_r($response);

        	echo $adminUrl = Mage::helper("adminhtml")->getUrl("bullhorn/dashboard");
        	header("Location:".$adminUrl);		    
		    

		}
		else
		{
			echo "Something went wrong";
		}
   	}
}