<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'assistant')) {
    header("Location: ../login.php");
    exit;
}

$error = '';
try {
    // Chukua apps zote kutoka database
    $stmt = $pdo->query("SELECT * FROM apps ORDER BY id DESC");
    $apps = $stmt->fetchAll();
    // Data for charts
    // Apps per category
    $catStmt = $pdo->query("SELECT category, COUNT(*) as count FROM apps GROUP BY category");
    $catData = $catStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    // Downloads per month (last 6 months)
    $dlStmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(downloads) as total FROM apps GROUP BY month ORDER BY month DESC LIMIT 6");
    $dlData = $dlStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to load dashboard data. Please try again later.';
    error_log('DATABASE ERROR (admin/index): ' . $e->getMessage());
    $apps = [];
    $catData = [];
    $dlData = [];
}
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
  <style>
    .admin-row {
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .admin-row:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      transform: translateY(-4px) scale(1.01);
      z-index: 2;
    }
    .animate__animated {
      animation-duration: 0.7s;
      animation-fill-mode: both;
    }
    .animate__fadeInUp {
      animation-name: fadeInUp;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translate3d(0, 40px, 0);
      }
      to {
        opacity: 1;
        transform: none;
      }
    }
  </style>
</head>
<body>
  <?php include('admin_header.php'); ?>
  <div class="admin-main-content">
    <!-- Admin Drawer for mobile -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="adminDrawer" aria-labelledby="adminDrawerLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminDrawerLabel">Admin Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="nav flex-column">
          <li class="nav-item mb-2"><a href="index.php" class="nav-link">Dashboard</a></li>
          <li class="nav-item mb-2"><a href="manage_users.php" class="nav-link">Manage Users</a></li>
          <li class="nav-item mb-2"><a href="manage_assistants.php" class="nav-link">Manage Assistants</a></li>
          <li class="nav-item mb-2"><a href="../logout.php" class="nav-link">Logout</a></li>
        </ul>
      </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="container my-4">
      <h2 class="mb-4">All Apps</h2>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center my-3"><?php echo $error; ?></div>
      <?php endif; ?>
      <div class="row mb-4">
        <div class="col-md-6 mb-3">
          <canvas id="catChart" height="120"></canvas>
        </div>
        <div class="col-md-6 mb-3">
          <canvas id="dlChart" height="120"></canvas>
        </div>
      </div>
      <div class="card admin-card p-3 animate__animated animate__fadeInUp" style="box-shadow:0 4px 24px rgba(39,174,96,0.10);border-radius:18px;">
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0 align-middle">
            <thead>
              <tr>
                <th>App Name</th>
                <th>Category</th>
                <th>Tags</th>
                <th>Downloads</th>
                <th>Developer</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($apps as $app): ?>
                <tr class="animate__animated animate__fadeInUp admin-row">
                  <td class="align-middle"><span class="fw-bold"><?php echo htmlspecialchars($app['app_name']); ?></span></td>
                  <td class="align-middle"><span class="badge bg-info text-dark"> <?php echo htmlspecialchars($app['category'] ?? ''); ?> </span></td>
                  <td class="align-middle">
                    <?php if (!empty($app['tags'])): ?>
                      <?php foreach (explode(',', $app['tags']) as $tag): ?>
                        <span class="badge bg-secondary">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </td>
                  <td class="align-middle text-muted"><?php echo $app['downloads']; ?></td>
                  <td class="align-middle text-muted"><?php echo htmlspecialchars($app['user_id']); ?></td>
                  <td class="align-middle">
                    <a class="btn btn-primary btn-sm px-3 me-2" href="edit_app.php?id=<?php echo $app['id']; ?>"><i class="fa fa-edit"></i> Edit</a>
                    <a class="btn btn-danger btn-sm px-3" href="../delete_app.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this app?')"><i class="fa fa-trash"></i> Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <!-- FOOTER -->
<?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Category Pie Chart
    const catCtx = document.getElementById('catChart').getContext('2d');
    new Chart(catCtx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode(array_keys($catData)); ?>,
        datasets: [{
          data: <?php echo json_encode(array_values($catData)); ?>,
          backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6f42c1','#17a2b8','#fd7e14'],
        }]
      },
      options: {responsive:true, plugins:{legend:{position:'bottom'}}}
    });
    // Downloads Line Chart
    const dlCtx = document.getElementById('dlChart').getContext('2d');
    new Chart(dlCtx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode(array_reverse(array_column($dlData,'month'))); ?>,
        datasets: [{
          label: 'Downloads',
          data: <?php echo json_encode(array_reverse(array_column($dlData,'total'))); ?>,
          fill: true,
          borderColor: '#007bff',
          backgroundColor: 'rgba(0,123,255,0.1)',
          tension: 0.3
        }]
      },
      options: {responsive:true, plugins:{legend:{display:false}}}
    });
  </script>
</body>
</html>
