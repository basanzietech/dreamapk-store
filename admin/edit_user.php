<?php
// edit_user.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$user = null;

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID is required.");
}

$user_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    } else {
        $username = clean($_POST['username'] ?? '');
        $email    = clean($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';
        if (empty($username) || empty($email)) {
            $errors[] = "Tafadhali jaza taarifa zote muhimu (username na email).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
        if (!empty($password)) {
            if ($password !== $confirm) {
                $errors[] = "Passwords hazilingani.";
            } elseif (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }
        }
        if (empty($errors)) {
            try {
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $hashedPassword, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $user_id]);
                }
                header("Location: manage_users.php?success=updated");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Failed to update user. Please try again later.";
                error_log('DATABASE ERROR (edit_user): ' . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Header -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>Edit User</h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Content (Card) -->
  <div class="container">
    <div class="edit-card">
      <h2 class="text-center mb-4">Badilisha Taarifa za User</h2>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password (Leave blank to keep unchanged):</label>
          <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="mb-4">
          <label for="confirm" class="form-label">Confirm Password:</label>
          <input type="password" name="confirm" id="confirm" class="form-control">
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
<?php include ('../includes/footer.php'); ?>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>