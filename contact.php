<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'path/to/PHPMailer/src/Exception.php';
require 'path/to/PHPMailer/src/PHPMailer.php';
require 'path/to/PHPMailer/src/SMTP.php';
require 'PHPMailer-master/PHPMailerAutoload.php';

/* Configure basic variables */
$fromEmail = 'JohnDoe@gmail.com';
$fromName = 'John Doe';

$sendToEmail = 'taylorwong@sandiego.edu';
$sendToName = 'Taylor Wong';

$subject = 'New message from Portfolio Contact Form';

$fields = array('name' => 'Name', 'email' => 'Email', 'message' => 'Message');

// message that is displayed whether the submission was a success or failure 
$okMessage = 'Message was sent successfully. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';


/* Send message */
error_reporting(E_ALL & ~E_NOTICE);

try {
	if(count($_POST) == 0) throw new \Exception('Form is empty');
	$emailTextHTML = "<h1>You have a new message from your contact form</h1><hr>";
	$emailTextHTML .= "<table>";

	foreach ($_POST as $key => $value) {
		// if item in $_POST array already exists in $fields array, include in message 
		if(isset($fields[$key])){
			$emailTextHTML .= "<tr><th>$fields[$key]</th><td>$value</td></tr>";
		}
	}

	$emailTextHtml .= "</table><hr>";
	$emailTextHtml .= "<p>Have a wonderful day,<br>Best,<br>Taylor</p>";

	// add headers to email
	$headers = array('Content-Type: text/plain; charset="UTF-8;"',
		'From: ' . $from,
		'Reply-To: ' . $from,
		'Return-Path: ' . $from,
	);


	// send email using PHPMailer
	$mail = new PHPMailer;

	$mail->setFrom($fromEmail, $fromName);
	$mail->addAddress($sendToEmail, $sendToName); 
	$mail->addReplyTo($from);

	$mail->isHTML(true);

	$mail->Subject = $subject;
	$mail->msgHTML($emailTextHtml); 

	if(!$mail->send()) {
    	throw new \Exception('I could not send the email.' . $mail->ErrorInfo);
	}

	// $responseArray will be sent as JSON response to contact.html
	$responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e) {
	$responseArray = array('type' => 'success', 'message' => $errorMessage);
}


// if requested by AJAX request return JSON response 
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	$encoded = json_encode($responseArray);

	header('Content-Type: application/json');

	echo $encoded;
}
else {
	echo $responseArray['message'];
}
