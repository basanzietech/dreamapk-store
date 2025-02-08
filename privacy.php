<?php
// privacy.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Privacy Policy - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (located in assets/css/style.css) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Optional inline CSS if needed -->
  <style>
    .card-content {
      padding: 20px;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="toolbar bg-dark text-white p-3">
    <div class="container">
      <h1 class="h4 m-0 text-center">dream-apkstore</h1>
    </div>
  </header>

  <!-- Main Content -->
  <div class="container container-custom my-5">
    <div class="card">
      <div class="card-content">
        <h2 class="text-center mb-4">Privacy Policy</h2>
        <p>
          At dream-apkstore, we value your privacy. Our privacy policy outlines how we collect, store, and use your personal information.
        </p>
        <p>
          Your data will not be shared with any third party without your consent unless stated in our policy. We ensure that your information is securely protected.
        </p>
        <p>
          For any questions regarding your privacy, please visit our <a href="contact.php">Contact Us</a> page.
        </p>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer bg-dark text-white p-3 mt-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div class="footer-col">
        <a href="privacy.php" class="me-3 text-white text-decoration-none">Privacy Policy</a>
        <a href="contact.php" class="text-white text-decoration-none">Contact Us</a>
      </div>
      <div class="footer-col">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> dreamapkstore. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
