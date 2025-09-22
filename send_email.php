<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/PHPMailer/src/Exception.php';
require __DIR__.'/PHPMailer/src/PHPMailer.php';
require __DIR__.'/PHPMailer/src/SMTP.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['success'=>false,'message'=>'Invalid request method']);
    exit;
}

function post($key){
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$name = substr(strip_tags(post('name')),0,200);
$email = substr(strip_tags(post('email')),0,200);
$phone = substr(strip_tags(post('phone')),0,50);
$address = substr(strip_tags(post('address')),0,250);
$destination = substr(strip_tags(post('destination')),0,100);
$messageUser = substr(strip_tags(post('message')),0,2000);

if(empty($name) || empty($email)){
    echo json_encode(['success'=>false,'message'=>'Name and Email are required']);
    exit;
}

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    echo json_encode(['success'=>false,'message'=>'Invalid email']);
    exit;
}

try {
    $mail = new PHPMailer(true);

    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ks5065386@gmail.com'; // Gmail
    $mail->Password = 'cusmsvqxiqzzywuw';    // Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('ks5065386@gmail.com','Rajasthan Tour Cabs');
    $mail->addAddress($email,$name);
    $mail->addReplyTo('ks5065386@gmail.com','Support');

    $mail->isHTML(true);
    $mail->Subject = 'Thank you for contacting us!';

    // Attractive HTML email body
    $mail->Body = '
    <div style="font-family:Arial,sans-serif; color:#333; line-height:1.6; max-width:600px; margin:auto; border:1px solid #e2e2e2; padding:20px; border-radius:10px; background:#f9f9f9;">
        <div style="text-align:center; margin-bottom:20px;">
            <img src="https://yourdomain.com/logo.png" alt="Rajasthan Tour Cabs" width="120" style="margin-bottom:10px;">
            <h2 style="color:#ff6600; margin:0;">Welcome to Rajasthan Tour Cabs</h2>
        </div>
        <p>Hi <strong>'.htmlspecialchars($name).'</strong>,</p>
        <p>Thank you for reaching out! We have received your request with the following details:</p>
        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><strong>Name:</strong></td>
                <td style="padding:8px; border:1px solid #ddd;">'.htmlspecialchars($name).'</td>
            </tr>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><strong>Phone:</strong></td>
                <td style="padding:8px; border:1px solid #ddd;">'.htmlspecialchars($phone).'</td>
            </tr>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><strong>Address:</strong></td>
                <td style="padding:8px; border:1px solid #ddd;">'.nl2br(htmlspecialchars($address)).'</td>
            </tr>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><strong>Destination:</strong></td>
                <td style="padding:8px; border:1px solid #ddd;">'.htmlspecialchars($destination).'</td>
            </tr>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><strong>Message:</strong></td>
                <td style="padding:8px; border:1px solid #ddd;">'.nl2br(htmlspecialchars($messageUser)).'</td>
            </tr>
        </table>
        <p style="margin-top:20px;">Our team will contact you soon. We look forward to providing you an amazing tour experience!</p>
        <hr style="margin:20px 0; border:none; border-top:1px solid #ddd;">
        <p style="text-align:center; color:#777; font-size:12px;">Rajasthan Tour Cabs | Your trusted travel partner</p>
    </div>
    ';

    // Plain text alternative
    $mail->AltBody = "Hi $name,\n\nThank you for reaching out! We have received your request. Your details:\n\nName: $name\nPhone: $phone\nAddress: $address\nDestination: $destination\nMessage: $messageUser\n\nOur team will contact you soon.\n\nRegards,\nRajasthan Tour Cabs";

    $mail->send();

    echo json_encode(['success'=>true,'message'=>'Email sent successfully!']);
    exit;

} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>'Mailer Error: '.$e->getMessage()]);
    exit;
}
