<?php
// admin/manage_users.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Manage Users - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <header class="toolbar d-flex justify-content-between align-items-center bg-dark text-white p-2">
    <div class="toolbar-left">
      <h1 class="h4 m-0">Manage Users</h1>
    </div>
    <div class="toolbar-right">
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link text-white" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="manage_assistants.php">Manage Assistants</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <div class="container container-custom my-4">
    <h2 class="mb-4">Orodha ya Watumiaji</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?php echo $user['id']; ?></td>
              <td><?php echo htmlspecialchars($user['username']); ?></td>
              <td><?php echo htmlspecialchars($user['email']); ?></td>
              <td><?php echo $user['role']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <footer class="footer">
  <div class="footer-col">
    <a href="privacy.php">Privacy Policy</a>
    <a href="contact.php">Contact Us</a>
  </div>
  <div class="footer-col">
    <p>&copy; <?php echo date("Y"); ?> dream-apkstore. All rights reserved.</p>
  </div>
</footer>

  <!-- Bootstrap JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
