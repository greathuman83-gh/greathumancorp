<?php
	if(!$sendEmail){
		exit;
	}
	//Import PHPMailer classes into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;
	use PHPMailer\PHPMailer\OAuth;
	//Alias the League Google OAuth2 provider class
	use League\OAuth2\Client\Provider\Google;

	date_default_timezone_set('Etc/UTC');

	require_once(dirname(__FILE__).'/../autoload.php');
	$mail = new PHPMailer();
	$mail->isSMTP();
	//Enable SMTP debugging
	//SMTP::DEBUG_OFF = off (for production use)
	//SMTP::DEBUG_CLIENT = client messages
	//SMTP::DEBUG_SERVER = client and server messages
	if($debug == 1){
		$mail->SMTPDebug = SMTP::DEBUG_CLIENT;
	}else if($debug == 2){
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
	}else{
		$mail->SMTPDebug = SMTP::DEBUG_OFF;
	}
	//Set the hostname of the mail server
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth = true;
	$mail->AuthType = 'XOAUTH2';
	
	$email = $sendEmail; // the email used to register google app
	$clientId = $googleClientId;
	$clientSecret = $googleClientSecret;
	$refreshToken = $googleRefreshToken;

	//Create a new OAuth2 provider instance
	$provider = new Google(
		[
			'clientId' => $clientId,
			'clientSecret' => $clientSecret,
		]
	);

	//Pass the OAuth provider instance to PHPMailer
	$mail->setOAuth(
		new OAuth(
			[
				'provider' => $provider,
				'clientId' => $clientId,
				'clientSecret' => $clientSecret,
				'refreshToken' => $refreshToken,
				'userName' => $email,
			]
		)
	);

	$receiveEmailArray = explode('|',$receiveEmail);
	$receiveNameArray = explode('|',$receiveName);
	if($fileName){//첨부파일
		$fileNameArray = explode('|',$fileName);//첨부파일 경로
		$fileRealNameArray = explode('|',$fileRealName);//첨부파일명
		for($i=0;$i<count($fileNameArray);$i++){
			$mail->AddAttachment($gh_path."data/$directoryName/".$fileNameArray[$i],$fileRealNameArray[$i]);// attachment
			//$mail->AddAttachment($filePathArray[$i],$fileNameArray[$i]);// attachment
		}
	}

	for($i=0;$i<count($receiveEmailArray);$i++){
		//$mail->ClearAttachments(); // 첨부파일 초기화
		$mail->ClearAddresses(); //주소초기화
		$mail->setFrom($sendEmail, $sendName);
		$mail->addAddress($receiveEmailArray[$i],$receiveNameArray[$i]);
		$mail->Subject = $subject;
		$mail->CharSet = PHPMailer::CHARSET_UTF8;
		$mail->msgHTML($body);

		//send the message, check for errors
		if (!$mail->send()) {
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			exit;
		} else {
			//echo 'Message sent!';
		}
	}
?>