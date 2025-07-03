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
  <?php include('admin_header.php'); ?>
  <div class="admin-main-content">
  <!-- Main Content -->
  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Manage Assistants</h2>
      <button class="btn btn-success btn-lg px-4 shadow" data-bs-toggle="modal" data-bs-target="#addAssistantModal"><i class="fa fa-plus"></i> Add Assistant</button>
    </div>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger animate__animated animate__fadeInUp"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="card p-3 shadow-lg animate__animated animate__fadeInUp" style="border-radius:18px;">
      <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0 align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($assistants as $a): ?>
              <tr>
                <td class="align-middle"><?php echo htmlspecialchars($a['name']); ?></td>
                <td class="align-middle"><?php echo htmlspecialchars($a['email']); ?></td>
                <td class="align-middle"><span class="badge bg-info text-dark"><?php echo htmlspecialchars($a['role']); ?></span></td>
                <td class="align-middle">
                  <div class="form-check form-switch">
                    <input class="form-check-input status-toggle" type="checkbox" role="switch" id="statusSwitch<?php echo $a['id']; ?>" data-id="<?php echo $a['id']; ?>" <?php if ($a['active']) echo 'checked'; ?> >
                    <label class="form-check-label" for="statusSwitch<?php echo $a['id']; ?>">
                      <?php if ($a['active']): ?>
                        <span class="badge bg-success">Active</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Inactive</span>
                      <?php endif; ?>
                    </label>
                  </div>
                </td>
                <td class="align-middle">
                  <a class="btn btn-primary btn-sm px-3 me-2" href="edit_assistant.php?id=<?php echo $a['id']; ?>"><i class="fa fa-edit"></i> Edit</a>
                  <a class="btn btn-danger btn-sm px-3" href="delete_assistant.php?id=<?php echo $a['id']; ?>" onclick="return confirm('Are you sure you want to delete this assistant?')"><i class="fa fa-trash"></i> Delete</a>
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

<!-- Modal: Add Assistant -->
<div class="modal fade" id="addAssistantModal" tabindex="-1" aria-labelledby="addAssistantModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
        <div class="modal-header">
          <h5 class="modal-title" id="addAssistantModalLabel">Add New Assistant</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger animate__animated animate__fadeInUp">
              <?php foreach ($errors as $error): ?>
                <div><?php echo $error; ?></div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add Assistant</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.status-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
      var id = this.getAttribute('data-id');
      var status = this.checked ? 1 : 0;
      fetch('toggle_assistant_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&active=' + status
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.closest('td').querySelector('span').className = status ? 'badge bg-success' : 'badge bg-danger';
          this.closest('td').querySelector('span').textContent = status ? 'Active' : 'Inactive';
        } else {
          alert('Failed to update status');
          this.checked = !status;
        }
      });
    });
  });
});
</script>
</body>
</html>
