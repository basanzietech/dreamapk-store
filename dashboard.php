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
  <!-- Responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive website you can upload apks and you can download apks for free simple to use you can get direct download link try it now enjoy amazing design more people will love it. Dream apk store for better weather.">
  <meta name="keywords" content="direct link, secure site, upload, download, benjamini omary, apk, apks, app, apps, store, dream">
  <meta name="author" content="benjamini omary">
  
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title>Dashboard - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN (optional) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
  <!-- HEADER -->
  <header class="toolbar d-flex justify-content-between align-items-center p-3">
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
  
  <!-- MAIN CONTENT -->
  <div class="container my-4">
    <h2 class="mb-4">Your Apps</h2>
    <?php if ($user_apps): ?>
      <div class="card dashboard-card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
              <thead>
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
        </div>
      </div>
    <?php else: ?>
      <p>You don't have any uploaded apps yet.</p>
    <?php endif; ?>
  </div>
  
  <!-- FOOTER -->
  <?php include ('includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
