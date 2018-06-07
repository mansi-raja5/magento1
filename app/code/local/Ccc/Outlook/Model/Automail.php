<?php
class Ccc_Outlook_Model_Automail
{
	public function sendMail()
	{
		$query = 'SELECT entity_id,subject,com.order_id,
					com.from_email,com.recipients,
					com.received_date,com.content,
					reply_order_id,
					reply_date,
					reply_subject
					FROM `ccc_order_mail` com
					LEFT JOIN (SELECT received_date as reply_date,order_id as reply_order_id,subject as reply_subject FROM `ccc_order_mail` where subject like "Re:%") reply
					ON reply.reply_order_id = com.order_id
					WHERE subject like "New order PO#%" 
					AND reply_date is null AND reply.reply_order_id is null
					AND DATE_ADD(received_date, INTERVAL 3 DAY) < NOW()
					AND automail is null
					GROUP BY `com`.`entity_id`
					ORDER BY `com`.`entity_id` ASC';

		$orderMails = Mage::getSingleton("core/resource")->getConnection("read")->fetchAll($query);
		// echo "<pre>";
		// print_r($orderMails);
		// exit;
		foreach ($orderMails as $_orderMail)
		{

			$message = "Hi,<br><br>We sent the PO request, and still have not received acknowledgement.";
			$message .= "<br>Can you please give us an update on the same.";
			$message .= "<br><br>Please Find the PO request initially sent for reference";
			$message .= "<br><br>";

			$content 	= $_orderMail['content'];
			// $removeThis = "<p>Hi, </p>";
			// $strpos 	= strpos($content, $removeThis);
    		// $content	= substr($content, 0, $strpos) . substr($content, $strpos + strlen($removeThis));

    		$message .= $content;
			
			// $data["email_from"] = $_orderMail['from_email'];
			$data["email_from"] = "cccvishwanathan@gmail.com";
			// $data["name_to"] 	= "mansi";
			//$data["email"] 		= $_orderMail['recipients'];
			$data["email"] 		= "vishwanathan@cybercomcreation.com";
			$data["ccemail"] 	= "";
			$data["bccemail"] 	= "cccvishwanathan@gmail.com";
			$data["subject"] 	= "2nd Request PO# {$_orderMail['order_id']}";
			$data["message"] 	= $message;

			$email 				= explode(',', $data['email']);
			$add_cc 			= $data['ccemail'] ? explode(';', $data['ccemail']) : array();
					
					
			$mail = Mage::getModel('outlook/mail');
			$mail->setMessage($data['message']);
			$mail->setEmailFrom($data['email_from']);
			$mail->setEmailTo(array_values($email));
			$mail->setEmailCc($add_cc);
			$mail->setEmailBcc($data["bccemail"]);
			$mail->setSubject($data['subject']);
			$mail->send();

            $query = "UPDATE `ccc_order_mail` 
                        SET `automail` = NOW()
                        WHERE `entity_id` = {$_orderMail['entity_id']}";
            //$orderMail = Mage::getSingleton("core/resource")->getConnection("core_write")->query($query);			
		}
		return $orderMails;
	}
}
?>