<?php
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
  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (optional) -->
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
  <!-- HEADER -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>Admin Dashboard</h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a href="index.php" class="nav-link">Dashboard</a></li>
          <li class="nav-item"><a href="manage_users.php" class="nav-link">Manage Users</a></li>
          <li class="nav-item"><a href="manage_assistants.php" class="nav-link">Manage Assistants</a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>
  
  <!-- MAIN CONTENT -->
  <div class="container">
    <div class="dashboard-card">
      <h2 class="mb-4">Orodha ya Apps</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
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
                <a href="edit_app.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="../delete_app.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Una uhakika unataka kufuta app hii?')" class="btn btn-sm btn-danger">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- FOOTER -->
<?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
