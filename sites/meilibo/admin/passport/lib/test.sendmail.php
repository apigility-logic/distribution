<?php

/*
	[UCenter] (C)2001-2099 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: sendmail.inc.php 1059 2011-03-01 07:25:09Z monkey $
*/

require __DIR__ . '/phpmailer/PHPMailerAutoload.php';

$maild = new PHPMailer;


$mail_setting = array (
  'accessemail' => '',
  'censoremail' => '',
  'censorusername' => '',
  'dateformat' => 'y-n-j',
  'doublee' => '0',
  'nextnotetime' => '0',
  'timeoffset' => '28800',
  'privatepmthreadlimit' => '25',
  'chatpmthreadlimit' => '30',
  'chatpmmemberlimit' => '35',
  'pmfloodctrl' => '15',
  'pmcenter' => '1',
  'sendpmseccode' => '1',
  'pmsendregdays' => '0',
  'maildefault' => 'help@meilibo.net',
  'mailsend' => '1',
  'mailserver' => 'smtp.qq.com',
  'mailport' => '465',
  'mailauth' => '1',
  'mailfrom' => 'help@meilibo.net',
  'mailauth_username' => '577423463',
  'mailauth_password' => 'uhalavnzpyqzbdaj',
  'maildelimiter' => '0',
  'mailusername' => '1',
  'mailsilent' => '1',
  'version' => '1.6.0',
  'credits' => 'N;',
);

$mail['email_to'] = '2202055656@qq.com';
$mail['subject'] =  'something wrong?';
$mail['message'] = 'Oh my god';

$maild->SMTPDebug = 3;                                 // Enable verbose debug output

$maild->isSMTP();                                      // Set mailer to use SMTP
$maild->Host = $mail_setting['mailserver'];            // Specify main and backup SMTP servers
$maild->SMTPAuth = true;                               // Enable SMTP authentication
$maild->Username = $mail_setting['mailauth_username']; // SMTP username
$maild->Password = $mail_setting['mailauth_password']; // SMTP password
$maild->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$maild->Port = $mail_setting['mailport'];              // TCP port to connect to
$maild->Hostname = 'meilibo.net';
$maild->addAddress($mail['email_to']);
$maild->setFrom($mail_setting['maildefault']);
$maild->addCustomHeader('Content-Type','text/html; charset=UTF-8;');
$maild->isHTML(true);                                  // Set email format to HTML
$maild->Subject = "=?UTF-8?B?".base64_encode($mail['subject'])."?=";;
$maild->Body    = $mail['message'];

return $maild->send();
