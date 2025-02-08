<?php
// contact.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Contact Us - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (located in assets/css/style.css) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Optional inline CSS if needed -->
  <style>
    /* Additional styling if needed */
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
        <h2 class="text-center mb-4">Contact Us</h2>
        <p class="text-center">Please use the form below for inquiries, questions, or any additional information.</p>
        <form action="process_contact.php" method="POST">
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

  <!-- Footer -->
  <footer class="footer bg-dark text-white p-3 mt-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div class="footer-col">
        <a href="privacy.php" class="me-3 text-white text-decoration-none">Privacy Policy</a>
        <a href="contact.php" class="text-white text-decoration-none">Contact Us</a>
      </div>
      <div class="footer-col">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> dream-apkstore. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>