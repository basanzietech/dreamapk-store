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
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://dreamapk.store/contact.php" />
  <title>Contact Us - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (optional external file) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4690089323418332" crossorigin="anonymous"></script>
</head>
<body>
  <!-- HEADER -->
  <header class="toolbar d-flex align-items-center justify-content-between px-2 py-1" style="min-height:60px;">
    <button class="btn btn-link d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainDrawer" aria-controls="mainDrawer">
      <i class="fa-solid fa-bars fa-lg"></i>
    </button>
    <div class="flex-grow-1 text-center">
      <h1 class="h5 m-0">dreamapk.store</h1>
    </div>
  </header>
  <!-- ADSENSE BANNER -->
  <div class="container my-2">
    <ins class="adsbygoogle"
         style="display:block; text-align:center; margin: 1rem auto;"
         data-ad-client="ca-pub-4690089323418332"
         data-ad-slot="1234567890"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
  </div>
  <!-- Drawer -->
  <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="mainDrawer" aria-labelledby="mainDrawerLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="mainDrawerLabel">Menu</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="nav flex-column">
        <li class="nav-item mb-2"><a class="nav-link" href="index.php"><i class="fa-solid fa-house"></i> Home</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="about.php"><i class="fa-solid fa-info-circle"></i> About Us</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="contact.php"><i class="fa-solid fa-headset"></i> Contact Us</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="privacy.php"><i class="fa-solid fa-user-secret"></i> Privacy</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="terms.php"><i class="fa-solid fa-gavel"></i> Terms</a></li>
      </ul>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="container my-5">
    <div class="card shadow-lg p-4 animate__animated animate__fadeInUp" style="max-width:500px;margin:auto;border-radius:20px;">
      <h2 class="mb-4 text-center"><i class="fa fa-envelope me-2"></i>Contact Us</h2>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-user me-1"></i> Name</label>
          <input type="text" name="name" class="form-control" required maxlength="50">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-envelope me-1"></i> Email</label>
          <input type="email" name="email" class="form-control" required maxlength="100">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-comment me-1"></i> Message</label>
          <textarea name="message" class="form-control" rows="4" required maxlength="1000"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2"><i class="fa fa-paper-plane me-2"></i>Send Message</button>
      </form>
    </div>
  </div>

  <!-- FOOTER -->
<?php include ('includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
