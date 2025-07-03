<?php
// dashboard.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

// Chukua apps za mtumiaji kutoka database
$stmt = $pdo->prepare("SELECT * FROM apps WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_apps = $stmt->fetchAll();

// Data for chart: downloads per app
$appNames = array_map(function($a){return $a['app_name'];}, $user_apps);
$appDownloads = array_map(function($a){return (int)$a['downloads'];}, $user_apps);
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
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://dreamapk.store/dashboard.php" />
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title>Dashboard - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN (optional) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .dashboard-row {
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .dashboard-row:hover {
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
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4690089323418332" crossorigin="anonymous"></script>
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
  
  <!-- ADSENSE BANNER -->
<!--  <div class="container my-2">
    <ins class="adsbygoogle"
         style="display:block; text-align:center; margin: 1rem auto;"
         data-ad-client="ca-pub-4690089323418332"
         data-ad-slot="1234567890"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
  </div>-->
  
  <!-- MAIN CONTENT -->
  <div class="container my-4">
    <h2 class="mb-4">Your Apps</h2>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger animate__animated animate__fadeInUp"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="mb-4">
      <canvas id="downloadsChart" height="120"></canvas>
    </div>
    <?php if ($user_apps): ?>
      <div class="card dashboard-card p-3 animate__animated animate__fadeInUp" style="box-shadow:0 4px 24px rgba(39,174,96,0.10);border-radius:18px;">
        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>App Name</th>
                <th>Category</th>
                <th>Tags</th>
                <th>Downloads</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($user_apps as $app): ?>
                <tr class="animate__animated animate__fadeInUp dashboard-row">
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
                  <td class="align-middle">
                    <a class="btn btn-primary btn-sm px-3 me-2" href="upload_app.php?id=<?php echo $app['id']; ?>"><i class="fa fa-edit"></i> Edit</a>
                    <a class="btn btn-danger btn-sm px-3" href="delete_app.php?id=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this app?')"><i class="fa fa-trash"></i> Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-info">You have not uploaded any apps yet.</div>
    <?php endif; ?>
  </div>
  
  <!-- FOOTER -->
  <?php include ('includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Downloads Bar Chart
    const ctx = document.getElementById('downloadsChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($appNames); ?>,
        datasets: [{
          label: 'Downloads',
          data: <?php echo json_encode($appDownloads); ?>,
          backgroundColor: '#007bff',
        }]
      },
      options: {responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}
    });
  </script>
</body>
</html>
