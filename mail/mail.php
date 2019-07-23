<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($name, $email, $subject, $message) {

  require 'vendor/autoload.php';

  $mail = new PHPMailer(TRUE);

  try {
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = $_ENV['mail-host'];
    $mail->SMTPAuth = TRUE;
    $mail->Username = $_ENV['mail-user'];
    $mail->Password = $_ENV['mail-password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($_ENV['mail-address'], $_ENV['mail-name']);
    $mail->addAddress($_ENV['mail-to']);
    $mail->addReplyTo($email);

    $mail->isHTML(FALSE);
    $mail->Subject = "New message from {$name} - {$subject}";
    $mail->Body = $message;

    $mail->send();
    return TRUE;
  } catch (Exception $e) {
    error_log($mail->ErrorInfo);
    return FALSE;
  }
}

function getInput($variable) {
  $input = htmlspecialchars_decode($_GET[$variable]);
  $output = filter_var($input);
  if ($output != NULL)
    return TRUE;
}

function verifyToken($token, $secret_key) {
  $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$token}"));
  if (is_object($response)) {
    if ($response->success)
      return TRUE;
    return FALSE;
  } else {
    error_log("response isn't an object.");
  }
}

/* ReCaptcha keys */
$site_key = $_ENV['recaptcha-site-key'];
$secret_key = $_ENV['recaptcha-secret-key'];

/* User inputs */
$name = getInput('name');
$email = getInput('email');
$subject = getInput('subject');
$message = getInput('message');
$recaptcha_token = getInput('g-recaptcha-response');


// Verify Captcha
if (verifyToken($recaptcha_token, $secret_key)) {
  // Send mail
  $output = sendMail($name, $email, $subject, $message);
  http_response_code(201);
  error_log("mail sent: {$output}");
  print(json_encode($output, JSON_PRETTY_PRINT));
} else {
  http_response_code(400);
  error_log("Invalid recaptcha");
  print(json_encode(FALSE, JSON_PRETTY_PRINT));
}
