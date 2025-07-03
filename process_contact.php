<?php
// process_contact.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Invalid CSRF token. Please refresh the page and try again.";
        exit;
    }
    // Kusafisha data zinazopokelewa kutoka kwa fomu
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Angalia kama sehemu zote zimejazwa
    if (empty($name) || empty($email) || empty($message)) {
        echo "Please fill in all required parts.";
        exit;
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit;
    }
    
    // Safisha message (ondoa newlines kwenye headers)
    $message = str_replace(["\r", "\n", "%0a", "%0d"], '', $message);
    
    // Tayarisha maelezo ya email
    $to = 'basanzietech@gmail.com';  // Badilisha na anwani yako ya email
    $subject = "Ujumbe kutoka fomu ya Contact Us ya $name";
    $body = "Jina: $name\nEmail: $email\n\nUjumbe:\n" . htmlspecialchars($message);
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    // Tuma email
    if (mail($to, $subject, $body, $headers)) {
        echo "Your message has been successfully sent. Thank you for contacting us.";
    } else {
        echo "Failed to send messages. Please try again later or contact Us via this email basanzietech@gmail.com .";
        error_log('MAIL ERROR (contact): Failed to send message from ' . $email);
    }
} else {
    // Kama si POST, rudisha kwa fomu
    header("Location: contact.php");
    exit;
}
?>
