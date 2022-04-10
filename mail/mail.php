<?php
require __DIR__ . '/vendor/autoload.php';

function sendMail($name, $email, $subject, $message) {
  $mail = new \PHPMailer\PHPMailer\PHPMailer(TRUE);
  $mail->SMTPDebug = 0;
  $mail->CharSet = 'UTF-8';
  $mail->Encoding = 'base64';
  $mail->isSMTP();
  $mail->Host = $_ENV['mail_host'];
  $mail->SMTPAuth = TRUE;
  $mail->Username = $_ENV['mail_user'];
  $mail->Password = $_ENV['mail_password'];
  $mail->SMTPSecure = $_ENV['SMTPS'];
  $mail->Port = (int)$_ENV['mail_port'];
  try {
    $mail->setFrom($_ENV['mail_user'], $_ENV['mail_name']);
    $mail->addAddress($_ENV['mail_to']);
    $mail->addReplyTo($email);
    $mail->isHTML(FALSE);
    $mail->Subject = "New message from {$name} - {$subject}";
    $mail->Body = $message;
    $mail->send();
    return TRUE;
  } catch (\PHPMailer\PHPMailer\Exception $ex) {
    error_log($ex->getMessage());
  }
  return FALSE;
}

function verifyToken($token, $secret_key, $email, $subject) {
  $response = json_decode(file_get_contents("https://hcaptcha.com/siteverify?secret={$secret_key}&response={$token}"));
  if (is_object($response)) {
    if ($response->success === TRUE && $response->hostname === $_ENV['domain']) return TRUE;
  } else error_log("response isn't an object.");
  error_log("Failed msg from: {$email} subject: {$subject}");
  return FALSE;
}

/* Captcha secret key */
$secret_key = $_ENV['captcha-secret-key'];

/* User inputs */
$name = filter_input(INPUT_POST, 'email_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
$subject = filter_input(INPUT_POST, 'email_subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'email_body', FILTER_SANITIZE_STRING);
$captcha_response = filter_input(INPUT_POST, 'h-captcha-response', FILTER_SANITIZE_STRING);

// Verify Captcha
if (!verifyToken($captcha_response, $secret_key, $email, $subject)) {
  http_response_code(400);
  return FALSE;
}

// Send mail
$output = sendMail($name, $email, $subject, $message);
if ($output) http_response_code(201);
else http_response_code(500);
