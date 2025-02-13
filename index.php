<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Utafutaji (search) kama parameter
$searchTerm = isset($_GET['search']) ? clean($_GET['search']) : '';

if (!empty($searchTerm)) {
    $stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM apps WHERE app_name LIKE ? ORDER BY downloads DESC LIMIT 21");
    $stmt->execute(["%$searchTerm%"]);
    $apps = $stmt->fetchAll();
    $totalApps = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
    $totalPages = ceil($totalApps / 12);
} else {
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $perPage = 21;
    $data = paginate($pdo, $page, $perPage);
    $apps = $data['apps'];
    $totalApps = $data['total'];
    $totalPages = ceil($totalApps / $perPage);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- Meta viewport ili kuhakikisha responsive design -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Responsive website you can upload apks and you can download apks for free simple to use you can get direct download link try it now enjoy amazing design more people will love it. Dream apk store for better weather.">
  <meta name="keywords" content="direct link, secure site, upload, download, benjamini omary, apk, apks, app, apps, store, dream">
  <meta name="author" content="benjamini omary">
  
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title>dreamapk store</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS (ikiwa unatumia faili ya nje) -->
  <link rel="stylesheet" href="assets/css/style.css">

  <script>
    // Toggle search form visibility
    function toggleSearch() {
      var searchContainer = document.querySelector('.search-container');
      searchContainer.classList.toggle('d-none');
      if (!searchContainer.classList.contains('d-none')) {
        document.getElementById('searchInput').focus();
      }
    }
    
    // Share function
    function shareAPK(url) {
      if (navigator.share) {
        navigator.share({
          title: 'Download this APK',
          text: 'Check out this APK from dream-apkstore',
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
  <header class="toolbar d-flex justify-content-between align-items-center p-2">
    <!-- Toggle Menu Icon (Drawer) -->
    <button class="btn btn-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <!-- Jina la Site -->
    <h1 class="h5 m-0 flex-grow-1 text-center">dreamapk.store</h1>
    <!-- Search Icon -->
    <button class="btn btn-link" id="searchIcon" onclick="toggleSearch()">
      <i class="fa-solid fa-magnifying-glass"></i>
    </button>
  </header>

  <!-- SEARCH FORM (inayofichwa kwa default) -->
  <div class="search-container d-none">
    <div class="container py-2">
      <form class="d-flex" action="index.php" method="GET">
        <input class="form-control form-control-sm" type="text" name="search" id="searchInput" placeholder="Search APK..." value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button class="btn btn-sm ms-2" type="submit">Search</button>
      </form>
    </div>
  </div>

  <!-- OFFCANVAS DRAWER (MENU) -->
  <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="nav flex-column">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item mb-2">
            <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> Developer Console</a>
          </li>
          <li class="nav-item mb-2">
            <a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item mb-2">
            <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-user"></i> Developer Console</a>
          </li>
        <?php endif; ?>
        
     <li class="nav-item mb-2">
          <a class="nav-link" href="terms.php"><i class="fa-solid fa-gavel"></i> Term and Condition</a>
        </li>   
        
        <li class="nav-item mb-2">
          <a class="nav-link" href="privacy.php"><i class="fa-solid fa-file-lines"></i> Privacy Policy</a>
        </li>              
                    <li class="nav-item mb-2">
          <a class="nav-link" href="about.php"><i class="fa-solid fa-info-circle"></i> About Us</a>
        </li>
            
        <li class="nav-item mb-2">
          <a class="nav-link" href="contact.php"><i class="fa-solid fa-headset"></i> Contact Us</a>
        </li>
               
      </ul>
    </div>
  </div>

  <!-- Ujumbe kwa watazamaji ambao hawajaingia -->
  <?php if (!isLoggedIn()): ?>
    <div class="container my-4">
      <p>You can only download APK. If you are a developer, please <a href="register.php">register</a> to find the developer console.</p>
    </div>
  <?php endif; ?>

  <!-- MAIN CONTENT: GRID YA APPS -->
  <div class="container-fluid my-4">
    <h2 class="mb-4">Popular apps</h2>
    <div class="row">
      <?php if ($apps): ?>
        <?php foreach ($apps as $app): ?>
          <div class="col-6 col-md-4 col-lg-3 mb-4">
            <div class="app-card p-2 border">
              <img src="<?php echo htmlspecialchars($app['logo']); ?>" alt="<?php echo htmlspecialchars($app['app_name']); ?>" class="img-fluid">
              <h5 class="mt-2"><?php echo htmlspecialchars($app['app_name']); ?></h5>
              <p>Downloads: <?php echo $app['downloads']; ?></p>
              <div class="d-flex justify-content-center gap-2">
                <a class="btn btn-primary btn-sm" href="app_detail.php?id=<?php echo $app['id']; ?>">See more</a>
                <button class="btn btn-secondary btn-sm" onclick="shareAPK('<?php echo 'https://benja.yzz.me/app_detail.php?id=' . $app['id']; ?>')">Share Link</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No search results.</p>
      <?php endif; ?>
    </div>
    
    <!-- PAGINATION -->
    <?php if (empty($searchTerm) && $totalPages > 1): ?>
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if(isset($page) && $i==$page) echo 'active'; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
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
