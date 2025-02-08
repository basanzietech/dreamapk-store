<?php
// admin/index.php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Chukua apps zote kutoka database
$stmt = $pdo->query("SELECT * FROM apps ORDER BY id DESC");
$apps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title>Admin Dashboard - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Toolbar ya admin -->
  <header class="toolbar d-flex justify-content-between align-items-center bg-dark text-white p-2">
    <div class="toolbar-left">
      <h1 class="h4 m-0">Admin Dashboard</h1>
    </div>
    <div class="toolbar-right d-flex align-items-center">
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link text-white" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="manage_users.php">Manage Users</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="manage_assistants.php">Manage Assistants</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container container-custom my-4">
    <h2 class="mb-4">Orodha ya Apps</h2>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>App Name</th>
            <th>Developer</th>
            <th>Downloads</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($apps as $app): ?>
          <tr>
            <td><?php echo $app['id']; ?></td>
            <td><?php echo htmlspecialchars($app['app_name']); ?></td>
            <td>
              <?php
                $stmt2 = $pdo->prepare("SELECT username FROM users WHERE id = ?");
                $stmt2->execute([$app['user_id']]);
                $dev = $stmt2->fetch();
                echo htmlspecialchars($dev['username'] ?? 'Unknown');
              ?>
            </td>
            <td><?php echo $app['downloads']; ?></td>
            <td>
              <a class="btn btn-sm btn-primary" href="edit_app.php?id=<?php echo $app['id']; ?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="../delete_app.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Una uhakika unataka kufuta app hii?')">Delete</a>
            </td>
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
