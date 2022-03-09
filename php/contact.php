<?php 

include_once('inc/class.simple_mail.php');
include_once('inc/gump.class.php');

include_once('mail-config.php');




$isValid = GUMP::is_valid($_POST, array(
	'first-name' => 'required',
	'last-name' => 'required',
	'telephone' => 'required',
	'email' => 'required',
	'message' => 'required'
	));

if($isValid === true) {


	$mail = new SimpleMail();
	$mail->setTo(YOUR_EMAIL_ADDRESS, YOUR_COMPANY_NAME)
	->setSubject('New contact request')
	->setFrom(htmlspecialchars($_POST['email']), htmlspecialchars($_POST['first-name'].' '.$_POST['last-name']))
	->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
	->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
	->setMessage(createMessage($_POST))
	->setWrap(100);

	$mail->send();

	$result = array(
		'result' => 'success', 
		'msg' => array('Success! Your contact request has been send.')
		);

	echo json_encode($result);

} else {
	$result = array(
		'result' => 'error', 
		'msg' => $isValid
		);

	echo json_encode($result);
}


function createMessage($formData)
{
	$body  = 	"You have got a new contact request from your website : <br><br>";
	$body .=	"First Name:  ".htmlspecialchars($formData['first-name'])." <br><br>";
	$body .=	"Last Name:  ".htmlspecialchars($formData['last-name'])." <br><br>";
	$body .=	"Telephone:  ".htmlspecialchars($formData['telephone'])." <br><br>";
	$body .=	"Email:  ".htmlspecialchars($formData['email'])." <br><br>";
	$body .=	"Message: <br><br>";
	$body .=	htmlspecialchars($formData['message']);

	return $body;
}

