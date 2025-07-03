<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Please fill in all required parts.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords are not the same.";
    }

    // Angalia kama kuna user yeyote, kama hapo kwanza awe admin
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    $role = ($userCount == 0) ? 'admin' : 'user';

    // Angalia kama email tayari imesajiliwa
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Email is already registered.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
    $success = 'Registration successful! Please login to continue.';
       header("Location: login.php");
        exit;
        } else {
            $errors[] = "A problem occurred while inserting data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Register - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .register-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      padding: 2rem 2.5rem;
      max-width: 400px;
      margin: 2rem auto;
      transition: box-shadow 0.3s, transform 0.2s;
    }
    .register-card:hover {
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
    <div class="register-card card shadow-lg p-4 animate__animated animate__fadeInUp" style="max-width:400px;margin:auto;border-radius:20px;">
      <h2 class="mb-4 text-center">Create Account</h2>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-user me-1"></i> Username</label>
          <input type="text" name="username" class="form-control" required maxlength="50">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-envelope me-1"></i> Email</label>
          <input type="email" name="email" class="form-control" required maxlength="100">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-lock me-1"></i> Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-lock me-1"></i> Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        
        <?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate__animated animate__fadeInUp">
        <?php foreach ($errors as $error): ?>
            <?php echo $error . '<br>'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if (isset($success)): ?>
    <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
<?php endif; ?>   
        
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2"><i class="fa fa-user-plus me-2"></i>Register</button>
      </form>
      <div class="mt-3 text-center">
        <a href="login.php">Already have an account? Login</a>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
