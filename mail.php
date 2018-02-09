<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.02.2018
 * Time: 17:27
 */

$message_body = $_GET['message'];;
$theme = $_GET['theme'];
$addressee = $_GET['email'];
$filename = str_replace(' ','_',$_FILES['userfile']['name']);
$uploadfile = str_replace(' ','_',$_FILES['userfile']['name']);

send_mail_with_attachment(
    'site@email.com',
    $addressee,
    'MySite :: '.$theme,
    'utf-8',
    'utf-8',
    $message_body,
    $filename,
    $uploadfile);

/*static*/ function send_mail_with_attachment($from, $to, $subject, $datacharset, $sendcharset, $message, $filename, $filepath ){
    $subject = mime_header_encode($subject, $datacharset, $sendcharset);
    $boundary = "--".md5(uniqid(time()));// генерируем разделитель
    $mailheaders = "MIME-Version: 1.0;\r\n";
    $mailheaders .="Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";// разделитель указывается в заголовке в параметре boundary
    $mailheaders .= "From: $from <$from>\r\n";
    $mailheaders .= "Reply-To: $from\r\n";

    $multipart = "--$boundary\r\n";
    $multipart .= "Content-Type: text/html; charset=$sendcharset\r\n";
    $multipart .= "Content-Transfer-Encoding: base64\r\n";
    $multipart .= "\r\n";
    $multipart .= chunk_split(base64_encode($message));// первая часть: само сообщение

    // Закачиваем файл
    $fp = fopen($filepath,"r");
    if (!$fp)
    {
        print "Не удается открыть файл22";
        exit();
    }
    $file = fread($fp, filesize($filepath));
    fclose($fp);

    $message_part = "\r\n--$boundary\r\n";
    $message_part .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
    $message_part .= "Content-Transfer-Encoding: base64\r\n";
    $message_part .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
    $message_part .= "\r\n";
    $message_part .= chunk_split(base64_encode($file));
    $message_part .= "\r\n--$boundary--\r\n";// второй частью прикрепляем файл, можно прикрепить два и более файла

    $multipart .= $message_part;

    return mail($to,$subject,$multipart,$mailheaders);// отправляем письмо
}

function mime_header_encode($str, $data_charset, $send_charset) {
    if($data_charset != $send_charset) {
        $str = iconv($data_charset, $send_charset, $str);
    }
    return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}