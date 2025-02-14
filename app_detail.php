<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("App ID is required.");
}

$app_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ?");
$stmt->execute([$app_id]);
$app = $stmt->fetch();
if (!$app) {
    die("The app was not found.");
}

// Get developer username
$stmt2 = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt2->execute([$app['user_id']]);
$developer = $stmt2->fetch();

$screenshots = json_decode($app['screenshots'], true);
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
  <title><?php echo htmlspecialchars($app['app_name']); ?> - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/style.css">
  <script>
    function shareAPK(url) {
      if (navigator.share) {
        navigator.share({
          title: 'Download this APK',
          text: 'Check out this APK from dreamapkstore',
          url: url
        }).then(() => console.log('Shared successfully'))
        .catch((error) => {
          console.log('Error sharing', error);
          alert('Error sharing: ' + error);
        });
      } else {
        navigator.clipboard.writeText(url)
          .then(() => alert('APK download link copied to clipboard.'))
          .catch(() => alert('Failed to copy link.'));
      }
    }
    function openModal(imgSrc) {
      document.getElementById('modalImage').src = imgSrc;
      var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
      myModal.show();
    }
  </script>
</head>
<body>
  <!-- HEADER -->
  <header class="toolbar d-flex justify-content-between align-items-center">
    <div class="ms-3">
      <h1 class="h4 m-0"><?php echo htmlspecialchars($app['app_name']); ?></h1>
    </div>
    <div class="me-3">
      <nav>
        <ul class="nav">
          <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <div class="container my-4">
    <div class="row g-4">
      <!-- Left Column: App Icon -->
      <div class="col-md-4 text-center">
        <img src="<?php echo htmlspecialchars($app['logo']); ?>" alt="<?php echo htmlspecialchars($app['app_name']); ?>" class="img-fluid app-icon">
      </div>
      <!-- Right Column: App Info -->
      <div class="col-md-8 app-details">
        <h2><?php echo htmlspecialchars($app['app_name']); ?></h2>
        <p><strong>Developer:</strong> <?php echo htmlspecialchars($developer['username'] ?? 'Unknown'); ?></p>
        <p><strong>Downloads:</strong> <?php echo $app['downloads']; ?></p>
        <div class="my-3">
          <a class="btn btn-primary btn-sm me-2" href="download.php?id=<?php echo $app['id']; ?>">Download APK</a>          
          <button class="btn btn-secondary btn-sm" onclick="shareAPK('<?php echo DOMAIN . '/app_detail.php?id=' . $app['id']; ?>')">Share Link</button>         
        </div>
        <div>
          <h5>Description:</h5>
          <p><?php echo nl2br(htmlspecialchars($app['description'])); ?></p>
        </div>
      </div>
    </div>
    
    <!-- SCREENSHOTS SECTION as Carousel -->
    <h3 class="mt-5">Screenshots</h3>
    <?php if ($screenshots && is_array($screenshots)): ?>
      <div id="screenshotCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php $active = true; ?>
          <?php foreach ($screenshots as $shot): ?>
            <div class="carousel-item <?php if($active){ echo 'active'; $active = false; } ?>">
              <img src="<?php echo htmlspecialchars($shot); ?>" alt="Screenshot" onclick="openModal('<?php echo htmlspecialchars($shot); ?>')">
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#screenshotCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#screenshotCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    <?php else: ?>
      <p>No screenshots available.</p>
    <?php endif; ?>
  </div>
  
  <!-- MODAL FOR FULL IMAGE VIEW -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="imageModalLabel">Screenshot</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modalImage" src="" alt="Full View" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
  
  <!-- FOOTER -->
<?php include ('includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
