<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
$success = '';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    } else {
        $email = clean($_POST['email'] ?? '');
        if (empty($email)) {
            $errors[] = "Please enter your email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                // Generate a unique token for password reset
                $token = bin2hex(random_bytes(50));
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
                $stmt->execute([$token, $email]);
                // Send email with password reset link
                $reset_link = DOMAIN . "/reset_password.php?token=" . $token;
                $subject = "Password Reset Request";
                $message = "Click the following link to reset your password: $reset_link";
                if (mail($email, $subject, $message)) {
                    $success = "A password reset link has been sent to your email.";
                } else {
                    $errors[] = "Failed to send reset email. Please try again later.";
                    error_log('MAIL ERROR (forgot_password): Failed to send reset email to ' . $email);
                }
            } else {
                $errors[] = "No account found with that email address.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password - dreamapkstore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container my-5">
    <div class="card p-4">
      <h2 class="text-center mb-4">Forgot Your Password?</h2>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success">
          <?php echo $success; ?>
        </div>
      <?php endif; ?>
      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-3">
          <label for="email" class="form-label">Enter your email:</label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
