<?php 

/* Configure basic variables */
$from = 'Contact Form <wong.taylor97@gmail.com>';
$sendTo = 'Contact Form <taylorwong@sandiego.edu>';
$subject = 'New message from Portfolio Contact Form';

$fields = array('name' => 'Name', 'email' => 'Email', 'message' => 'Message');

// message that is displayed whether the submission was a success or failure 
$okMessage = 'Message was sent successfully. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';



/* Send message */
error_reporting(E_ALL & ~E_NOTICE);

try {
	if(count($_POST) == 0) throw new \Exception('Form is empty');
	$emailText = "You have a new message from your contact form\n=================================\n";

	foreach ($_POST as $key => $value) {
		// if item in $_POST array already exists in $fields array, include in message 
		if(isset($fields[$key])){
			$emailText .= "$fields[$key]: $value\n";
		}
	}

	// add headers to email
	$headers = array('Content-Type: text/plain; charset="UTF-8;"',
		'From: ' . $from,
		'Reply-To: ' . $from,
		'Return-Path: ' . $from,
	);

	// send email via php internal mail() function 
	mail($sendTo, $subject, implode("\n", $headers));

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
