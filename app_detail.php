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

// Chukua jina la developer
$stmt2 = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt2->execute([$app['user_id']]);
$developer = $stmt2->fetch();

$screenshots = json_decode($app['screenshots'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title><?php echo htmlspecialchars($app['app_name']); ?> - dream-apkstore</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS -->
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
  </script>
</head>
<body>
  <!-- HEADER -->
  <header class="toolbar d-flex justify-content-between align-items-center bg-dark text-white p-2">
    <div class="ms-3">
      <h1 class="h4 m-0"><?php echo htmlspecialchars($app['app_name']); ?></h1>
    </div>
    <div class="me-3">
      <nav>
        <ul class="nav">
          <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container container-custom my-4">
    <div class="row">
      <!-- Left Column: App Icon -->
      <div class="col-md-4 text-center mb-3">
        <img src="<?php echo htmlspecialchars($app['logo']); ?>" alt="<?php echo htmlspecialchars($app['app_name']); ?>" class="img-fluid app-icon">
      </div>
      <!-- Right Column: Developer Info, Downloads, Buttons, Description -->
      <div class="col-md-8">
        <p><strong>Developer:</strong> <?php echo htmlspecialchars($developer['username'] ?? 'Unknown'); ?></p>
        <p><strong>Downloads:</strong> <?php echo $app['downloads']; ?></p>
        <div class="mb-3">
          <a class="btn btn-primary btn-sm" href="download.php?id=<?php echo $app['id']; ?>">Download APK</a>
          <button class="btn btn-share btn-sm" onclick="shareAPK('<?php echo 'https://benja.yzz.me/app_detail.php?id=' . $app['id']; ?>')">Share Link</button>
        </div>
        <p><strong>Description:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($app['description'])); ?></p>
      </div>
    </div>
    
    <h3>Screenshots</h3>
    <div class="screenshots-container d-flex overflow-auto gap-2 py-2">
      <?php if ($screenshots && is_array($screenshots)): ?>
        <?php foreach ($screenshots as $shot): ?>
          <div class="screenshot-item flex-shrink-0">
            <img src="<?php echo htmlspecialchars($shot); ?>" alt="Screenshot" class="img-thumbnail" style="max-width:150px; cursor: pointer;" onclick="openModal('<?php echo htmlspecialchars($shot); ?>')">
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No screenshots available.</p>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Modal for full image view -->
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

  <footer class="footer bg-dark text-white p-3">
    <div class="footer-col">
      <a href="privacy.php" class="text-white me-3">Privacy Policy</a>
      <a href="contact.php" class="text-white">Contact Us</a>
    </div>
    <div class="footer-col mt-2">
      <p class="m-0">&copy; <?php echo date("Y"); ?> dreamapkstore. All rights reserved.</p>
    </div>
  </footer>

  <script>
    function openModal(imgSrc) {
      document.getElementById('modalImage').src = imgSrc;
      var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
      myModal.show();
    }
  </script>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
