<?php
// dashboard.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

// Chukua apps za mtumiaji kutoka database
$stmt = $pdo->prepare("SELECT * FROM apps WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_apps = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Hii inasaidia responsive design -->

  <title>Dashboard - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- Toolbar -->
  <header class="toolbar d-flex justify-content-between align-items-center bg-dark text-white p-2">
    <div class="toolbar-left">
      <h1 class="h4 m-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    </div>
    <div class="toolbar-right">
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link text-white" href="upload_app.php">Upload APK</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  
  <div class="container my-4">
    <h2>Your Apps</h2>
    <?php if ($user_apps): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>App Name</th>
              <th>Downloads</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($user_apps as $app): ?>
              <tr>
                <td><?php echo htmlspecialchars($app['app_name']); ?></td>
                <td><?php echo $app['downloads']; ?></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="upload_app.php?id=<?php echo $app['id']; ?>">Edit</a>
                  <a class="btn btn-sm btn-danger" href="delete_app.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this app?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p>You don't have uploaded apps yet.</p>
    <?php endif; ?>
  </div>
  
  <footer class="footer">
  <div class="footer-col">
    <a href="privacy.php">Privacy Policy</a>
    <a href="contact.php">Contact Us</a>
  </div>
  <div class="footer-col">
    <p>&copy; <?php echo date("Y"); ?> dreamapkstore. All rights reserved.</p>
  </div>
</footer>
  
  <!-- Bootstrap JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
