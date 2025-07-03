<?php
// edit_assistant.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$assistant = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    // die("Assistant ID is required.");
    $fatal_error = "Assistant ID is required.";
}

$assistant_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'assistant'");
$stmt->execute([$assistant_id]);
$assistant = $stmt->fetch();
if (!$assistant) {
    // die("Assistant not found.");
    $fatal_error = "Assistant not found.";
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    } else {
        $username = clean($_POST['username'] ?? '');
        $email = clean($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        $active = isset($_POST['active']) ? 1 : 0;
        if (empty($username) || empty($email)) {
            $errors[] = "Tafadhali jaza taarifa zote muhimu (Username na Email).";
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
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, active = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $hashedPassword, $active, $assistant_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, active = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $active, $assistant_id]);
                }
                header("Location: manage_assistants.php?success=updated");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Failed to update assistant. Please try again later.";
                error_log('DATABASE ERROR (edit_assistant): ' . $e->getMessage());
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
  <title>Edit Assistant - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional external CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
  <?php include('admin_header.php'); ?>
  <div class="admin-main-content">
  <!-- Main Content -->
  <div class="container">
    <div class="edit-card">
      <h2 class="text-center mb-4">Badilisha Taarifa za Assistant</h2>
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (isset($fatal_error)): ?>
        <div class="alert alert-danger text-center my-5"><?php echo $fatal_error; ?></div>
        <?php exit; ?>
      <?php endif; ?>
      <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($assistant['username']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email:</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($assistant['email']); ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password (Leave blank to keep unchanged):</label>
          <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="mb-4">
          <label for="confirm" class="form-label">Confirm Password:</label>
          <input type="password" name="confirm" id="confirm" class="form-control">
        </div>
        <div class="mb-4">
          <label class="form-label">Status:</label>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="activeSwitch" name="active" value="1" <?php if ($assistant['active']) echo 'checked'; ?>>
            <label class="form-check-label" for="activeSwitch">
              <?php echo $assistant['active'] ? 'Active' : 'Inactive'; ?>
            </label>
          </div>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Footer -->
  <?php include ('../includes/footer.php'); ?>
  </div>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
