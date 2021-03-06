<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendMail($name, $email, $subject, $message) {
  $mail = new PHPMailer(TRUE);
  $mail->SMTPDebug = 0;
  $mail->CharSet = 'UTF-8';
  $mail->Encoding = 'base64';
  $mail->isSMTP();
  $mail->Host = $_ENV['mail-host'];
  $mail->SMTPAuth = TRUE;
  $mail->Username = $_ENV['mail-user'];
  $mail->Password = $_ENV['mail-password'];
  $mail->SMTPSecure = 'tls';
  $mail->Port = (int)$_ENV['mail-port'];
  try {
    $mail->setFrom($_ENV['mail-address'], $_ENV['mail-name']);
    $mail->addAddress($_ENV['mail-to']);
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

function verifyToken($token, $secret_key) {
  $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$token}"));
  if (is_object($response)) {
    if ($response->success === TRUE && $response->hostname === 'carlgo11.com')
      return TRUE;
  } else error_log("response isn't an object.");
  return FALSE;
}

/* ReCaptcha secret key */
$secret_key = $_ENV['recaptcha-secret-key'];

/* User inputs */
$name = filter_input(INPUT_POST, 'email_name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
$subject = filter_input(INPUT_POST, 'email_subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'email_body', FILTER_SANITIZE_STRING);
$recaptcha_token = filter_input(INPUT_POST, 'g-recaptcha-response', FILTER_SANITIZE_STRING);

// Verify Captcha
if (!verifyToken($recaptcha_token, $secret_key)) {
  http_response_code(400);
  return FALSE;
}

// Send mail
$output = sendMail($name, $email, $subject, $message);
if ($output) http_response_code(201);
else http_response_code(500);
