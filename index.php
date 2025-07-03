<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Get all categories for filter
$catStmt = $pdo->query("SELECT DISTINCT category FROM apps WHERE category IS NOT NULL AND category != ''");
$allCategories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// Filtering by category
$selectedCategory = isset($_GET['category']) ? clean($_GET['category']) : '';
$searchTerm = isset($_GET['search']) ? clean($_GET['search']) : '';

// Filtering by tag
$selectedTag = isset($_GET['tag']) ? clean($_GET['tag']) : '';

// Sorting
$sort = isset($_GET['sort']) ? clean($_GET['sort']) : 'downloads_desc';

if (!empty($searchTerm)) {
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM apps WHERE app_name LIKE :search";
    $params = [':search' => "%$searchTerm%"];
    if (!empty($selectedCategory)) {
        $sql .= " AND category = :category";
        $params[':category'] = $selectedCategory;
    }
    if (!empty($selectedTag)) {
        $sql .= " AND tags LIKE :tag";
        $params[':tag'] = "%$selectedTag%";
    }
    // Sorting
    if ($sort === 'newest') {
        $sql .= " ORDER BY created_at DESC LIMIT 21";
    } elseif ($sort === 'oldest') {
        $sql .= " ORDER BY created_at ASC LIMIT 21";
    } else {
        $sql .= " ORDER BY downloads DESC LIMIT 21";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $apps = $stmt->fetchAll();
    $totalApps = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
    $totalPages = ceil($totalApps / 12);
} else {
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $perPage = 21;
    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM apps";
    $params = [];
    $where = [];
    if (!empty($selectedCategory)) {
        $where[] = "category = :category";
        $params[':category'] = $selectedCategory;
    }
    if (!empty($selectedTag)) {
        $where[] = "tags LIKE :tag";
        $params[':tag'] = "%$selectedTag%";
    }
    if ($where) {
        $sql .= " WHERE ".implode(' AND ', $where);
    }
    // Sorting
    if ($sort === 'newest') {
        $sql .= " ORDER BY created_at DESC LIMIT :offset, :perPage";
    } elseif ($sort === 'oldest') {
        $sql .= " ORDER BY created_at ASC LIMIT :offset, :perPage";
    } else {
        $sql .= " ORDER BY downloads DESC LIMIT :offset, :perPage";
    }
    $offset = ($page - 1) * $perPage;
    $params[':offset'] = (int)$offset;
    $params[':perPage'] = (int)$perPage;
    $stmt = $pdo->prepare($sql);
    // Bind all params
    foreach ($params as $key => $val) {
        $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key, $val, $type);
    }
    $stmt->execute();
    $apps = $stmt->fetchAll();
    $totalApps = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
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
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://dreamapk.store/index.php" />
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title>dreamapk store</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Custom CSS (ikiwa unatumia faili ya nje) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Animate.css fallback for fadeInUp -->
  <style>
    .app-card {
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .app-card:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      transform: translateY(-6px) scale(1.03);
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
  <header class="toolbar d-flex align-items-center justify-content-between px-2 py-1" style="min-height:60px;">
    <!-- Toggle Menu Icon (Drawer) -->
    <button class="btn btn-link d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu">
      <i class="fa-solid fa-bars fa-lg"></i>
    </button>
    <!-- Jina la Site -->
    <div class="flex-grow-1 text-center">
      <h1 class="h5 m-0">dreamapk.store</h1>
    </div>
    <!-- Search Icon -->
    <button class="btn btn-link ms-auto" id="searchIcon" onclick="toggleSearch()">
      <i class="fa-solid fa-magnifying-glass fa-lg"></i>
    </button>
  </header>

  <!-- ADSENSE BANNER -->
 <!-- <div class="container my-2">
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
      <p>You can only download APK file here. If you are a developer, please <a href="register.php">register</a> to find the developer console.</p>
    </div>
  <?php endif; ?>

  <!-- MAIN CONTENT: GRID YA APPS -->
  <div class="container-fluid my-4">
    <h2 class="mb-4">Popular apps</h2>
    <form class="row mb-3" method="get" action="index.php">
      <div class="col-md-3 col-8 mb-2">
        <select name="category" class="form-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php foreach ($allCategories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat); ?>" <?php if($selectedCategory==$cat) echo 'selected'; ?>><?php echo htmlspecialchars($cat); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 col-12 mb-2">
        <input type="text" name="search" class="form-control" placeholder="Search APK..." value="<?php echo htmlspecialchars($searchTerm); ?>">
      </div>
      <div class="col-md-3 col-8 mb-2">
        <input type="text" name="tag" class="form-control" placeholder="Filter by tag (e.g. chat)" value="<?php echo htmlspecialchars($selectedTag); ?>">
      </div>
      <div class="col-md-2 col-4 mb-2">
        <select name="sort" class="form-select" onchange="this.form.submit()">
          <option value="downloads_desc" <?php if($sort==='downloads_desc') echo 'selected'; ?>>Most Downloaded</option>
          <option value="newest" <?php if($sort==='newest') echo 'selected'; ?>>Newest</option>
          <option value="oldest" <?php if($sort==='oldest') echo 'selected'; ?>>Oldest</option>
        </select>
      </div>
      <div class="col-12 mb-2 d-md-none">
        <button class="btn btn-primary w-100" type="submit">Filter</button>
      </div>
    </form>
    <div class="row g-4 apps-grid">
      <?php if ($apps): ?>
        <?php foreach ($apps as $app): ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="app-card d-flex flex-row align-items-center p-3 animate__animated animate__fadeInUp" style="min-height:140px;">
              <div class="flex-shrink-0 me-3">
                <img src="<?php echo htmlspecialchars($app['logo']); ?>" alt="<?php echo htmlspecialchars($app['app_name']); ?>" class="app-icon" style="width:80px;height:80px;object-fit:cover;border-radius:20%;box-shadow:0 4px 8px rgba(0,0,0,0.12);">
              </div>
              <div class="flex-grow-1">
                <h5 class="mb-1"><?php echo htmlspecialchars($app['app_name']); ?></h5>
                <span class="badge bg-info text-dark mb-2"> <?php echo htmlspecialchars($app['category'] ?? ''); ?> </span>
                <div class="mb-2">
                  <?php if (!empty($app['tags'])): ?>
                    <?php foreach (explode(',', $app['tags']) as $tag): ?>
                      <span class="badge bg-secondary">#<?php echo htmlspecialchars(trim($tag)); ?></span>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                  <a class="btn btn-primary btn-lg px-4" href="app_detail.php?id=<?php echo $app['id']; ?>">See more</a>
                  <button class="btn btn-success btn-lg px-4" onclick="shareAPK('<?php echo DOMAIN . '/app_detail.php?id=' . $app['id'] . '.apk' ; ?>')"><i class="fa fa-share"></i> Share</button>
                </div>
                <div class="small text-muted">Downloads: <?php echo $app['downloads']; ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No search results.</p>
      <?php endif; ?>
    </div>
    
    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php if(isset($page) && $i==$page) echo 'active'; ?>">
              <a class="page-link" href="?page=<?php echo $i; ?><?php if($selectedCategory) echo '&category='.urlencode($selectedCategory); ?><?php if($searchTerm) echo '&search='.urlencode($searchTerm); ?><?php if($selectedTag) echo '&tag='.urlencode($selectedTag); ?><?php if($sort) echo '&sort='.urlencode($sort); ?>"><?php echo $i; ?></a>
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
