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

// Ongeza comments section
// Chukua comments za app hii
$stmt3 = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.app_id = ? ORDER BY c.created_at DESC");
$stmt3->execute([$app_id]);
$comments = $stmt3->fetchAll();

// Tuma comment mpya
$comment_error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isLoggedIn()) {
    $comment_text = trim($_POST['comment']);
    if (empty($comment_text)) {
        $comment_error = 'Comment cannot be empty!';
    } else {
        $stmt4 = $pdo->prepare("INSERT INTO comments (app_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt4->execute([$app_id, $_SESSION['user_id'], $comment_text]);
        $success = 'Comment posted successfully!';
        header("Location: app_detail.php?id=$app_id#comments");
        exit;
    }
}
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
  <link rel="canonical" href="https://dreamapk.store/app_detail.php?id=<?php echo urlencode($app['id']); ?>" />
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title><?php echo htmlspecialchars($app['app_name']); ?> - dream-apkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .comment-item {
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .comment-item:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      transform: translateY(-3px) scale(1.01);
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
  </div> -->

  <!-- MAIN CONTENT -->
  <div class="container my-4">
    <div class="row g-4 align-items-center">
      <!-- Left Column: App Icon -->
      <div class="col-12 col-md-4 text-center">
        <img src="<?php echo htmlspecialchars($app['logo']); ?>" alt="<?php echo htmlspecialchars($app['app_name']); ?>" class="app-icon" style="width:120px;height:120px;object-fit:cover;border-radius:24%;box-shadow:0 6px 16px rgba(0,0,0,0.13);">
      </div>
      <!-- Right Column: App Info -->
      <div class="col-12 col-md-8 app-details animate__animated animate__fadeInUp">
        <h2 class="mb-1"><?php echo htmlspecialchars($app['app_name']); ?></h2>
        <div class="mb-2">
          <span class="badge bg-info text-dark me-2"> <?php echo htmlspecialchars($app['category'] ?? ''); ?> </span>
          <span class="small text-muted">by <strong><?php echo htmlspecialchars($developer['username'] ?? 'Unknown'); ?></strong></span>
        </div>
        <div class="mb-2">
          <?php if (!empty($app['tags'])): ?>
            <?php foreach (explode(',', $app['tags']) as $tag): ?>
              <span class="badge bg-secondary">#<?php echo htmlspecialchars(trim($tag)); ?></span>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        <div class="mb-3">
          <span class="text-muted">Downloads: <?php echo $app['downloads']; ?></span>
        </div>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a class="btn btn-primary btn-lg px-4" href="download.php?id=<?php echo $app['id']; ?>"><i class="fa fa-download"></i> Download APK</a>
          <button class="btn btn-success btn-lg px-4" onclick="shareAPK('<?php echo DOMAIN . '/app_detail.php?id=' . $app['id'] . '.apk' ; ?>')"><i class="fa fa-share"></i> Share</button>
        </div>
        <div>
          <h5>Description:</h5>
          <p class="lead"><?php echo nl2br(htmlspecialchars($app['description'])); ?></p>
        </div>
      </div>
    </div>
    <!-- SCREENSHOTS SECTION as horizontal scroll -->
    <h3 class="mt-5">Screenshots</h3>
    <?php if ($screenshots && is_array($screenshots)): ?>
      <div class="d-flex flex-row flex-nowrap overflow-auto gap-3 py-2">
        <?php foreach ($screenshots as $shot): ?>
          <div style="min-width:180px;">
            <img src="<?php echo htmlspecialchars($shot); ?>" alt="Screenshot" class="rounded shadow-sm" style="width:180px;height:110px;object-fit:cover;cursor:pointer;" onclick="openModal('<?php echo htmlspecialchars($shot); ?>')">
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>No screenshots available.</p>
    <?php endif; ?>
    <!-- COMMENTS SECTION -->
    <div class="comments-section mt-5" id="comments">
      <h3>Comments</h3>
      <?php if (isLoggedIn()): ?>
        <form method="post" class="mb-4 animate__animated animate__fadeInUp">
          <div class="mb-2 d-flex align-items-start">
            <div class="me-2">
              <span class="avatar bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:1.2rem;"><i class="fa fa-user"></i></span>
            </div>
            <textarea name="comment" class="form-control" rows="2" maxlength="500" placeholder="Write a comment..." required></textarea>
          </div>
          <?php if (!empty($success)): ?>
            <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
          <?php endif; ?>
          <?php if (!empty($comment_error)): ?>
            <div class="alert alert-danger animate__animated animate__fadeInUp"><?php echo $comment_error; ?></div>
          <?php endif; ?>
          <button type="submit" class="btn btn-primary btn-lg px-4">Post Comment</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Login</a> to post a comment.</p>
      <?php endif; ?>
      <div class="comments-list mt-3">
        <?php if ($comments): ?>
          <?php foreach ($comments as $c): ?>
            <div class="comment-item d-flex align-items-start p-3 mb-3 rounded bg-light shadow-sm animate__animated animate__fadeInUp">
              <div class="me-3">
                <span class="avatar bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;font-size:1.2rem;"><i class="fa fa-user"></i></span>
              </div>
              <div class="flex-grow-1">
                <div class="mb-1"><strong><?php echo htmlspecialchars($c['username']); ?></strong> <span class="text-muted small ms-2"><?php echo date('M d, Y H:i', strtotime($c['created_at'])); ?></span></div>
                <div><?php echo nl2br(htmlspecialchars($c['comment'])); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
      </div>
    </div>
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
