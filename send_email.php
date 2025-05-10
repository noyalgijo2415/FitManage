<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendBookingEmail($toEmail, $userName, $trainerName, $bookingDate, $timeSlot) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 2; // Debugging (Set to 0 when in production)
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noyal9389@gmail.com';
        $mail->Password = '241567234nJ'; // Replace with App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noyal9389@gmail.com', 'Smart-Fit Gym');
        $mail->addAddress($toEmail, $userName);

        $mail->isHTML(true);
        $mail->Subject = "Trainer Booking Confirmation";
        $mail->Body = "<h3>Hello $userName,</h3>
                       <p>Your trainer slot has been successfully booked.</p>
                       <p><strong>Trainer:</strong> $trainerName</p>
                       <p><strong>Date:</strong> $bookingDate</p>
                       <p><strong>Time Slot:</strong> $timeSlot</p>
                       <p>Thank you for using Smart-Fit Gym!</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo; // Show error message
        return false;
    }
}
?>

<?php


