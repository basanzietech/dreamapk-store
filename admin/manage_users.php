<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$error = '';
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to load users. Please try again later.';
    error_log('DATABASE ERROR (manage_users): ' . $e->getMessage());
    $users = [];
}
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
  <?php include('admin_header.php'); ?>
  <div class="admin-main-content">
  <!-- Main Content (Card) -->
  <div class="container">
    <div class="dashboard-card">
      <h2 class="mb-4">Orodha ya Watumiaji</h2>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center my-3"><?php echo $error; ?></div>
      <?php endif; ?>
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
  </div>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>