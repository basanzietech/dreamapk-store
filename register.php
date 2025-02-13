<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

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
  
</head>
<body>
  <div class="container my-5">
    <div class="card p-4">
      <h2 class="text-center mb-4">Create New Account</h2>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form method="post" action="">
        <div class="mb-3">
          <label for="username" class="form-label">Developer Username:</label>
          <input type="text" class="form-control" name="username" id="username" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <div class="mb-3">
          <label for="confirm" class="form-label">Confirm Password:</label>
          <input type="password" class="form-control" name="confirm" id="confirm" required>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Register Now</button>
        </div>
      </form>
      <p class="text-center mt-3">Have you already registered? <a href="login.php" class="text-primary">Login</a></p>
    </div>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
