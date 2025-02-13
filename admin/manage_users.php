<?php
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
  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <!-- Header -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1>Manage Users</h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="manage_assistants.php">Manage Assistants</a></li>
          <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Main Content (Card) -->
  <div class="container">
    <div class="dashboard-card">
      <h2 class="mb-4">Orodha ya Watumiaji</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                  <a class="btn btn-sm btn-warning" href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                  <a class="btn btn-sm btn-danger" href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Una uhakika unataka kufuta user hii?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>