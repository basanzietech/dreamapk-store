<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in the email/username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            $success = "Login successful! Redirecting to dashboard...";
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email/Username or password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Login - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      padding: 2rem 2.5rem;
      max-width: 400px;
      margin: 2rem auto;
      transition: box-shadow 0.3s, transform 0.2s;
    }
    .login-card:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      transform: translateY(-6px) scale(1.02);
      z-index: 2;
    }
    .btn-primary {
      transition: background 0.2s, transform 0.2s;
    }
    .btn-primary:hover {
      background: #0056b3;
      transform: scale(1.04);
    }
    .animate__animated {
      animation-duration: 0.7s;
      animation-fill-mode: both;
    }
    .animate__fadeInUp {
      animation-name: fadeInUp;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translate3d(0, 40px, 0);
      }
      to {
        opacity: 1;
        transform: none;
      }
    }
  </style>
</head>
<body>
  <div class="container my-5">
    <div class="login-card card shadow-lg p-4 animate__animated animate__fadeInUp" style="max-width:400px;margin:auto;border-radius:20px;">
      <h2 class="mb-4 text-center">Login</h2>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-user me-1"></i> Email or Username</label>
          <input type="text" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-lock me-1"></i> Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <?php if (!empty($success)): ?>
          <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger animate__animated animate__fadeInUp">
            <?php foreach($errors as $e) echo $e . '<br>'; ?>
          </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2"><i class="fa fa-sign-in-alt me-2"></i>Login</button>
      </form>
      <div class="mt-3 text-center">
        <a href="register.php">Create an account</a> &middot; <a href="forgot_password.php">Forgot password?</a>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
