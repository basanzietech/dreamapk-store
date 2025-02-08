<?php
// process_contact.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kusafisha data zinazopokelewa kutoka kwa fomu
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Angalia kama sehemu zote zimejazwa
    if (empty($name) || empty($email) || empty($message)) {
        die("Please fill in all required parts.");
    }
    
    // Tayarisha maelezo ya email
    $to = 'basanzietech@gmail.com';  // Badilisha na anwani yako ya email
    $subject = "Ujumbe kutoka fomu ya Contact Us ya $name";
    $body = "Jina: $name\nEmail: $email\n\nUjumbe:\n$message";
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    // Tuma email
    if (mail($to, $subject, $body, $headers)) {
        echo "Your message has been successfully sent. Thank you for contacting us.";
    } else {
        echo "Failed to send messages. Please try again later or contact Us via this email basanzietech@gmail.com .";
    }
} else {
    // Kama si POST, rudisha kwa fomu
    header("Location: contact.php");
    exit;
}
?>
