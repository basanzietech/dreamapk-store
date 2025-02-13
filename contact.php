<?php
// contact.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Jumuisha PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Safisha na pata data kutoka fomu
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $messageContent = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($messageContent)) {
        $error = "Please fill in all fields.";
    } else {
        // Andaa PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Setup ya SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';               // Badilisha kama unatumia server nyingine
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-email@gmail.com';           // Badilisha na email yako
            $mail->Password   = 'your-email-password';            // Badilisha na password yako (au App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Taarifa za barua pepe
            $mail->setFrom('your-email@gmail.com', 'dream-apkstore');
            $mail->addAddress('your-email@gmail.com');            // Ingiza barua pepe ya mpokeaji
            $mail->addReplyTo($email, $name);

            // Maudhui ya barua pepe
            $mail->isHTML(true);
            $mail->Subject = "New Contact Message from $name";
            $mail->Body    = "<strong>Name:</strong> $name<br>
                              <strong>Email:</strong> $email<br>
                              <strong>Message:</strong><br>$messageContent";

            $mail->send();
            $success = "Thank you for contacting us. Your message has been sent successfully.";
        } catch (Exception $e) {
            $error = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contact Us - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (optional external file) -->
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
  <!-- HEADER -->
  <header class="toolbar bg-dark text-white p-3">
    <div class="container">
      <h1 class="h4 m-0 text-center">dream-apkstore</h1>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="container my-5">
    <div class="card">
      <div class="card-content">
        <h2 class="text-center mb-4">Contact Us</h2>
        <p class="text-center">Please use the form below for inquiries, questions, or any additional information. If the form does not work  submit your information via this email <a href="mailto:basanzietech@gmail.com">basanzietech@gmail.com</a> . </p>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
          <div class="mb-3">
            <label for="contactName" class="form-label">Name:</label>
            <input type="text" class="form-control" id="contactName" name="name" required>
          </div>
          <div class="mb-3">
            <label for="contactEmail" class="form-label">Email:</label>
            <input type="email" class="form-control" id="contactEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="contactMessage" class="form-label">Message:</label>
            <textarea class="form-control" id="contactMessage" name="message" rows="5" required></textarea>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Send Message</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
<?php include ('includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
