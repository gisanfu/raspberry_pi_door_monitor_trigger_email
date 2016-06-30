#!/usr/bin/env php
<?php

$setmode27 = shell_exec("gpio -g mode 27 in");
//$setmode17 = shell_exec("gpio -g mode 17 out");

define('aaa_smtp_from', 'your_smtp_account@example.com');
//define('aaa_smtp_from_name', '測試');
define('aaa_smtp_to', 'your_email@example.com');
define('aaa_smtp_port', '465');
define('aaa_smtp_ssl', 'ssl');
define('aaa_smtp_account', 'blha');
define('aaa_smtp_password', 'blha');
define('aaa_smtp_server', 'example.com');

while(1){
$aaa = shell_exec('gpio -g read 27');
if($aaa == 1){
	//echo '123';
	$now = date('Y-m-d-H-i-s');
	$attach = '/home/pi/scripts/image_'.$now.'.png';
	// http://stackoverflow.com/questions/22675502/raspistill-quality-size-miss-match-file-too-big
	`sudo raspistill -w 640 -h 480 -n -t 100 -q 10 -rot 90 -th none -o $attach`;
	`sudo chmod 777 $attach`;
	//$zend_dir =  dirname(__FILE__).'/../../_butterfly/framework/vendors';
	//ini_set ('include_path',ini_get('include_path').PATH_SEPARATOR.$zend_dir);
	require_once('Zend/Loader/Autoloader.php');
	$autoloader = Zend_Loader_Autoloader::getInstance();

	$config = array(
		'auth' => 'login',
		'ssl' => 'ssl',
		'port' => aaa_smtp_port,
		'username' => aaa_smtp_account,
		'password' => aaa_smtp_password
	);

	$Transport = new Zend_Mail_Transport_Smtp(aaa_smtp_server, $config);
	Zend_Mail::setDefaultTransport($Transport);

	// 主旨
	$subject = '有人在你家門口 '.$now;

	$body = '請看附件圖片'."\n\n";

	$body_html = <<<XXX
	請看附件圖片<br /> 
XXX;

	$mail = new Zend_Mail('utf-8');
	$mail->setFrom(aaa_smtp_from, '');
	$mail->addTo(aaa_smtp_to);
	//$mail->addTo('gisanfu@yahoo.com.tw');
	$mail->setSubject($subject);
	$mail->setBodyText($body);
	$mail->setBodyHtml($body_html);

	// 正常來說，這個是附加PDF的檔案
	if($attach != ''){
		$at = new Zend_Mime_Part(file_get_contents($attach));
		//$at->type        = 'application/pdf';
		$at->type        = 'image/png';
		$at->disposition = Zend_Mime::DISPOSITION_INLINE;
		$at->encoding    = Zend_Mime::ENCODING_BASE64;
		$at->filename    = 'image_'.$now.'.png';
		//$mail->addAttachment(file_get_contents($attach), 'application/pdf', Zend_Mime::DISPOSITION_INLINE , Zend_Mime::ENCODING_BASE64);
		$mail->addAttachment($at);
	}

	$mail->send();
	unlink($attach);
} else {
   //echo 'none'."\n";
}
sleep(3);
}
