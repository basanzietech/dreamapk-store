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
    die("Assistant ID is required.");
}

$assistant_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'assistant'");
$stmt->execute([$assistant_id]);
$assistant = $stmt->fetch();
if (!$assistant) {
    die("Assistant not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (empty($username) || empty($email)) {
        $errors[] = "Tafadhali jaza taarifa zote muhimu (Username na Email).";
    }
    if (!empty($password) && $password !== $confirm) {
        $errors[] = "Passwords hazilingani.";
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $email, $hashedPassword, $assistant_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $email, $assistant_id]);
        }
        header("Location: manage_assistants.php");
        exit;
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
  <!-- Header -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>Edit Assistant</h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="manage_assistants.php">Manage Assistants</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  
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
      <form method="post" action="">
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
