<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Tafadhali jaza taarifa zote.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'assistant')");
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            header("Location: manage_assistants.php");
            exit;
        } else {
            $errors[] = "Tatizo lilitokea wakati wa kuongeza assistant.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'assistant' ORDER BY id DESC");
$stmt->execute();
$assistants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Assistants - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
  <!-- Header -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>Manage Assistants</h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  
  <!-- Main Content -->
  <div class="assistant-card">
    <h2 class="text-center mb-4">Ongeza Assistant Admin</h2>
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
        <input type="text" name="username" id="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" id="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Ongeza</button>
      </div>
    </form>
    
    <hr>
    
    <h2 class="text-center mb-4">Orodha ya Assistants</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($assistants as $assistant): ?>
            <tr>
              <td><?php echo $assistant['id']; ?></td>
              <td><?php echo htmlspecialchars($assistant['username']); ?></td>
              <td><?php echo htmlspecialchars($assistant['email']); ?></td>
              <td>
                <a class="btn btn-sm btn-edit" href="edit_assistant.php?id=<?php echo $assistant['id']; ?>">Edit</a>
                <a class="btn btn-sm btn-delete" href="../delete_user.php?id=<?php echo $assistant['id']; ?>" onclick="return confirm('Una uhakika unataka kufuta assistant huyu?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <!-- Footer -->
<?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
