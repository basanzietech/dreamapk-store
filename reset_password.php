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
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $token = $_GET['token'] ?? '';
        if (empty($password) || empty($confirm_password)) {
            $errors[] = "Please fill in both password fields.";
        } elseif ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            if ($user) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                try {
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
                    $stmt->execute([$hashed_password, $token]);
                    $success = "Your password has been successfully reset. You can now <a href='login.php'>log in</a>.";
                } catch (PDOException $e) {
                    $errors[] = "Failed to reset password. Please try again later.";
                    error_log('DATABASE ERROR (reset_password): ' . $e->getMessage());
                }
            } else {
                $errors[] = "Invalid or expired token.";
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
  <title>Reset Password - dreamapkstore</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container my-5">
    <div class="card p-4">
      <h2 class="text-center mb-4">Reset Your Password</h2>
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
          <label for="password" class="form-label">New Password:</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password:</label>
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Reset Password</button>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
