<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in the email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Email or password is incorrect.";
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
</head>
<body>
  <div class="container my-5">
    <div class="card p-4">
      <h2 class="text-center mb-4">Login to Your Account</h2>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form method="post" action="">
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-4">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Login Now</button>
        </div>
      </form>
      <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-primary">Register</a></p>
      <p class="text-center mt-0">Forgot password? <a href="forgot_password.php" class="text-primary">Forgot Password</a>
</p>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
