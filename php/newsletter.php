<?php 

include_once('inc/class.simple_mail.php');
include_once('inc/gump.class.php');
require_once('inc/MCAPI.class.php');

include_once('mail-config.php');


// Check Data
$isValid = GUMP::is_valid($_POST, array(
	'newsletter-email' => 'required|valid_email'
	));

if($mailchimpSupport === true)
{
	$mailchimpResult = sendMailchimp($_POST);
}
else
{
	$mailchimpResult = true;
}

if($isValid === true && $mailchimpResult === true) 
{

	// Submit Mail
	$mail = new SimpleMail();
	$mail->setTo(YOUR_EMAIL_ADDRESS, YOUR_COMPANY_NAME)
	->setSubject('New newsletter subscription')
	->setFrom(htmlspecialchars($_POST['newsletter-email']), htmlspecialchars($_POST['newsletter-email']))
	->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
	->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
	->setMessage(createMessage($_POST))
	->setWrap(100);

	$mail->send();

	$result = array(
		'result' => 'success', 
		'msg' => array('Success! Thank you for signing up to our newsletter.')
		);

	echo json_encode($result);

} 
else 
{
	if($isValid === true)
	{
		$error = array($mailchimpResult);
	}
	else
	{
		$error = $isValid;
	}

	$result = array(
		'result' => 'error', 
		'msg' => $error
		);

	echo json_encode($result);
}


function createMessage($formData)
{
	$body  = 	"You have got new subscribe request from your website : <br><br>";
	$body .=	"Email-Address:  ".htmlspecialchars($formData['newsletter-email'])." <br><br>";

	return $body;
}


function sendMailchimp($formData)
{
	$api = new MCAPI(MAILCHIMP_API_KEY);

	if($api->listSubscribe(MAILCHIMP_LIST_ID, $formData['newsletter-email'], '') === true) {
		return true;

	}else{	
		return $api->errorMessage;
	}
}

