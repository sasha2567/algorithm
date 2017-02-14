﻿<?php

function checkEmail ($email = '') {
	if (preg_match("/^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/", $email)) {
		return true;
	} else {
		return false;
	}
}

function checkPhone ($phone = '') {
	if (preg_match("/^\d[\d\(\)\ -]{4,14}\d$/", $phone)) {
		return true;
	} else {
		return false;
	}
}

function checkFields($massive = [])
{
	foreach ($massive as $value) {
		if(empty($value)){
			return false;
		}
	}
	return true;
}

$config['smtp_username'] = "lisyanscky.sasha2567@yandex.ru"; //Смените на имя своего почтового ящика.
$config['smtp_port'] = 465; // Порт работы. Не меняйте, если не уверены.
$config['smtp_host'] = 'ssl://smtp.yandex.ru'; //сервер для отправки почты
$config['smtp_password'] = 'ghjcnjnf545646'; //Измените пароль
$config['smtp_debug'] = true; //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
$config['smtp_charset'] = 'UTF-8'; //кодировка сообщений. (или UTF-8, итд)
$config['smtp_from'] = $fio; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"

function server_parse($socket, $response, $line = __LINE__) {
    global $config;
    while (@substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
             return false;
         }
    }
    if (!(substr($server_response, 0, 3) == $response)) {
        if ($config['smtp_debug']) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
        return false;
    }
    return true;
}

function smtpmail($mail_to, $subject, $message, $headers='', $fio) {
	global $config;
	$SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
	$SEND .= 'Subject: =?'.$config['smtp_charset'].'?B?'.base64_encode($subject)."=?=\r\n";
	if ($headers) $SEND .= $headers."\r\n\r\n";
	else
	{
			$config['smtp_from'] = iconv('UTF-8', 'windows-1251', $config['smtp_from']);
			$SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
			$SEND .= "MIME-Version: 1.0\r\n";
			$SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
			$SEND .= "Content-Transfer-Encoding: 8bit\r\n";
			$SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
			$SEND .= "To: $mail_to <$mail_to>\r\n";
			$SEND .= "X-Priority: 3\r\n\r\n";
	}
	$SEND .=  $message."\r\n";
	if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 30) ) {
        if ($config['smtp_debug']) echo $errno."<br>".$errstr;
        return false;
     }
 
    if (!server_parse($socket, "220", __LINE__)) return false;

        fwrite($socket, "HELO " . $config['smtp_host'] . "\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить HELO!</p>';
        fclose($socket);
        return false;
    }
 
    fwrite($socket, "AUTH LOGIN\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, base64_encode($config['smtp_username']) . "\r\n");
    if (!server_parse($socket, "334", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Логин авторизации не был принят сервером!</p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, base64_encode($config['smtp_password']) . "\r\n");
    if (!server_parse($socket, "235", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, "MAIL FROM: ".$config['smtp_username']."\r\n");
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, "RCPT TO: ".$config['smtp_username']."\r\n");
 
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить комманду RCPT TO: </p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, "DATA\r\n");
 
    if (!server_parse($socket, "354", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не могу отправить комманду DATA</p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, $SEND."\r\n.\r\n");
 
    if (!server_parse($socket, "250", __LINE__)) {
        if ($config['smtp_debug']) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
        fclose($socket);
        return false;
    }
    fwrite($socket, "QUIT\r\n");
    fclose($socket);
    return TRUE;
}

if (!empty($_POST['email'])) {
	$to = "sasha25678@mail.ru";
	$email = htmlspecialchars($_POST['email']);
	$phone = htmlspecialchars($_POST['phone']);
	$fio = htmlspecialchars($_POST['fio']);
	$comment = htmlspecialchars($_POST['comment']);
	$return = ['done' => false, 'message' => 'Сообщение не отправлено'];
	if (checkEmail($email) && checkPhone($phone) && checkFields([$email,$phone,$fio,$comment])) {
		$subject = 'Feedback';
		$headers = 'From: ' .$email. "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		$message = "Пользователь \n$fio\n(Контактный телефон - $phone)\nоставил свой коментарий на форме обратной связи:\n\"$comment\".\nВ ближайшее время необходимовыслать ответ.";
		if (smtpmail($to, $subject, $message, $headers, $fio)) {
			$return = ['done' => true, 'message' => 'Сообщение отправлено'];
		}
	}
	echo json_encode($return);
}
