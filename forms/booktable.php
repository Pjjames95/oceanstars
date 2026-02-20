<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Composer autoload for PHPMailer

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get and sanitize inputs
    $name    = isset($_POST["name"]) ? trim($_POST["name"]) : '';
    $email   = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $phone   = isset($_POST["phone"]) ? trim($_POST["phone"]) : '';
    $date    = isset($_POST["date"]) ? trim($_POST["date"]) : '';
    $time    = isset($_POST["time"]) ? trim($_POST["time"]) : '';
    $people  = isset($_POST["people"]) ? trim($_POST["people"]) : '';
    $message = isset($_POST["message"]) ? trim($_POST["message"]) : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || empty($people)) {
        http_response_code(400);
        echo "Please fill in all required fields.";
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Invalid email address.";
        exit;
    }

    // Sanitize for email content
    $name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $phone   = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $date    = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
    $time    = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');
    $people  = htmlspecialchars($people, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Recipient email
    $recipient = "gachombajames7@gmail.com";

    // Email subject
    $subject = "New Table Booking Request - $name";

    // Email body
    $email_content  = "You have received a new table booking request:\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone: $phone\n";
    $email_content .= "Date: $date\n";
    $email_content .= "Time: $time\n";
    $email_content .= "Number of People: $people\n\n";
    $email_content .= "Message:\n$message\n";

    // PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gachombajames7@gmail.com';
        $mail->Password   = 'bgya rpjx nzod khej'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom('gachombajames7@gmail.com', 'Spanish Hotel Booking');
        $mail->addReplyTo($email, $name);
        $mail->addAddress($recipient);

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $email_content;

        $mail->send();
        
        // Return success
        echo "OK";

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        http_response_code(500);
        echo "Message could not be sent. Please try again later.";
    }

} else {
    http_response_code(403);
    echo "Invalid request method.";
}
?>


