<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags(trim($_POST["subject"])));
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo "Please fill in all required fields.";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Invalid email address. Please enter a correct email.";
        exit;
    }

    $recipient = "gachombajames7@gmail.com";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gachombajames7@gmail.com';
        $mail->Password   = 'bgya rpjx nzod khej'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // SMTP requires From to be your verified email
        $mail->setFrom('gachombajames7@gmail.com', 'Spanish Hotel');

        // Only add valid Reply-To
        $mail->addReplyTo($email, $name);

        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        echo "OK";

    } catch (Exception $e) {
        echo "Message could not be sent: {$mail->ErrorInfo}";
    }

} else {
    http_response_code(403);
    echo "There was a problem with your submission.";
}


