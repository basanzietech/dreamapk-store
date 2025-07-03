<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

$errors = [];
$editMode = false;
$app = null;
$maxLogoSize = 512; // Maximum dimension (width/height) for logo
$maxScreenshotSize = [1080, 1920]; // Maximum width x height for screenshots
$maxLogoFileSize = 500 * 1024; // 500KB
$maxScreenshotFileSize = 1 * 1024 * 1024; // 1MB

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $app = $stmt->fetch();
    if ($app) {
        $editMode = true;
    } else {
        die("You do not have permission to edit this app.");
    }
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set default values to avoid undefined variable notices
$category = isset($category) ? $category : ($editMode ? $app['category'] : '');
$custom_category = isset($custom_category) ? $custom_category : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    } else {
        $appName     = clean($_POST['app_name'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $category    = clean($_POST['category'] ?? '');
        $tags        = clean($_POST['tags'] ?? '');
        
        if (empty($appName) || empty($description) || empty($category)) {
            $errors[] = "App name, Description, and Category are required.";
        }
        
        $uploadsDir = "uploads/";
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
// 1. Upload Logo (App Icon)
if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
    $imageSize = getimagesize($_FILES['logo']['tmp_name']);
    $fileSize = $_FILES['logo']['size'];
    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileType = mime_content_type($_FILES['logo']['tmp_name']);
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    if (!$imageSize) {
        $errors[] = "The logo file is not a valid image.";
    } elseif (!in_array($fileType, $allowedImageTypes) || !in_array($ext, ['jpg','jpeg','png','webp'])) {
        $errors[] = "Logo must be a JPG, PNG, or WEBP image.";
    } elseif ($fileSize > $maxLogoFileSize) {
        $errors[] = "Logo file size must not exceed 500KB.";
    } else {
        $logoPath = $uploadsDir . uniqid() . "_" . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
            // Resize the logo image if it exceeds 512x512 pixels
            list($width, $height) = $imageSize;
            if ($width > 512 || $height > 512) {
                // Resize the image
                if ($width > $height) {
                    $newWidth = 512;
                    $newHeight = intval($height * ($newWidth / $width));
                } else {
                    $newHeight = 512;
                    $newWidth = intval($width * ($newHeight / $height));
                }
                $image = null;
                switch ($fileType) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($logoPath);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($logoPath);
                        break;
                    case 'image/webp':
                        $image = imagecreatefromwebp($logoPath);
                        break;
                }
                if ($image) {
                    $newImage = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    switch ($fileType) {
                        case 'image/jpeg':
                            imagejpeg($newImage, $logoPath, 80);
                            break;
                        case 'image/png':
                            imagepng($newImage, $logoPath, 8);
                            break;
                        case 'image/webp':
                            imagewebp($newImage, $logoPath, 80);
                            break;
                    }
                    imagedestroy($image);
                    imagedestroy($newImage);
                }
            }
        } else {
            $errors[] = "Failed to upload the logo.";
        }
    }
} else {
    $logoPath = $editMode ? $app['logo'] : '';
    if (!$editMode) {
        $errors[] = "App logo is required.";
    }
}

// 2. Upload APK file
if (isset($_FILES['apk'])) {
    if ($_FILES['apk']['error'] != UPLOAD_ERR_OK) {
        switch ($_FILES['apk']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "APK file size exceeds the maximum allowed size.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = "The APK file was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                if (!$editMode) {
                    $errors[] = "APK file is required.";
                }
                break;
            default:
                $errors[] = "An error occurred during APK file upload.";
                break;
        }
    } else {
        $apkExt = strtolower(pathinfo($_FILES['apk']['name'], PATHINFO_EXTENSION));
        $apkSize = $_FILES['apk']['size'];
        $maxApkSize = 100 * 1024 * 1024; // 100MB
        if ($apkExt !== 'apk') {
            $errors[] = "Only APK files are allowed.";
        } elseif ($apkSize > $maxApkSize) {
            $errors[] = "APK file size must not exceed 100MB.";
        } else {
            $apkPath = $uploadsDir . uniqid() . "_" . basename($_FILES['apk']['name']);
            if (!move_uploaded_file($_FILES['apk']['tmp_name'], $apkPath)) {
                $errors[] = "Failed to upload the APK file.";
            }
        }
    }
} else {
    $apkPath = $editMode ? $app['apk_file'] : '';
    if (!$editMode) {
        $errors[] = "APK file is required.";
    }
}
        // 3. Upload Screenshots (up to 4)
        $screenshots = [];
        if (isset($_FILES['screenshots']) && is_array($_FILES['screenshots']['name'])) {
            $count = count($_FILES['screenshots']['name']);
            for ($i = 0; $i < $count && $i < 4; $i++) {
                if ($_FILES['screenshots']['error'][$i] == UPLOAD_ERR_OK) {
                    $imageSize = getimagesize($_FILES['screenshots']['tmp_name'][$i]);
                    $fileSize = $_FILES['screenshots']['size'][$i];
                    $allowedImageTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    $fileType = mime_content_type($_FILES['screenshots']['tmp_name'][$i]);
                    $ext = strtolower(pathinfo($_FILES['screenshots']['name'][$i], PATHINFO_EXTENSION));
                    if (!$imageSize) {
                        $errors[] = "Screenshot ".($i+1)." is not a valid image.";
                    } elseif (!in_array($fileType, $allowedImageTypes) || !in_array($ext, ['jpg','jpeg','png','webp'])) {
                        $errors[] = "Screenshot ".($i+1)." must be a JPG, PNG, or WEBP image.";
                    } elseif ($imageSize[0] > $maxScreenshotSize[0] || $imageSize[1] > $maxScreenshotSize[1]) {
                        $errors[] = "Screenshot ".($i+1)." dimensions must not exceed {$maxScreenshotSize[0]}x{$maxScreenshotSize[1]}px.";
                    } elseif ($fileSize > $maxScreenshotFileSize) {
                        $errors[] = "Screenshot ".($i+1)." file size must not exceed 1MB.";
                    } else {
                        $shotPath = $uploadsDir . uniqid() . "_".($i+1)."_".basename($_FILES['screenshots']['name'][$i]);
                        if (move_uploaded_file($_FILES['screenshots']['tmp_name'][$i], $shotPath)) {
                            $screenshots[] = $shotPath;
                        } else {
                            $errors[] = "Failed to upload screenshot ".($i+1).".";
                        }
                    }
                }
            }
        }

        if ($editMode && empty($screenshots)) {
            $screenshotsJSON = $app['screenshots'];
        } else {
            $screenshotsJSON = json_encode($screenshots);
        }

        // Kabla ya INSERT, hakikisha user_id ipo kwenye users table
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $errors[] = "User not logged in. Please login again.";
        } else {
            $checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $checkUser->execute([$_SESSION['user_id']]);
            if (!$checkUser->fetch()) {
                $errors[] = "User ID is invalid or does not exist in users table.";
            }
        }
if (empty($errors)) {
    try {
        if ($editMode) {
            $stmt = $pdo->prepare("UPDATE apps SET app_name = ?, description = ?, logo = ?, apk_file = ?, screenshots = ?, category = ?, tags = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$appName, $description, $logoPath, $apkPath, $screenshotsJSON, $category, $tags, $app['id'], $_SESSION['user_id']]);
        } else {
            // Check for duplicate submissions
            $stmt = $pdo->prepare("SELECT id FROM apps WHERE app_name = ? AND user_id = ?");
            $stmt->execute([$appName, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $errors[] = "App already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO apps (user_id, app_name, description, logo, apk_file, screenshots, downloads, category, tags) VALUES (?, ?, ?, ?, ?, ?, 0, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $appName, $description, $logoPath, $apkPath, $screenshotsJSON, $category, $tags]);
            }
        }
        if (empty($errors)) {
            $success = 'App uploaded successfully!';
            // Redirect after submission
            header("Location: dashboard.php");
            exit;
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
        error_log('DATABASE ERROR: ' . $e->getMessage());
      }
     }
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
  <link rel="canonical" href="https://dreamapk.store/upload_app.php" />
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title><?php echo $editMode ? "Edit App" : "Upload App"; ?> - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .upload-card {
      transition: box-shadow 0.2s, transform 0.2s;
    }
    .upload-card:hover {
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      transform: translateY(-6px) scale(1.02);
      z-index: 2;
    }
    .btn-primary {
      transition: background 0.2s, transform 0.2s;
    }
    .btn-primary:hover {
      background: #0056b3;
      transform: scale(1.04);
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
  <!-- Header -->
  <header class="toolbar d-flex justify-content-between align-items-center p-3">
    <div class="ms-3">
      <h1 class="h4 m-0"><?php echo $editMode ? "Edit App" : "Upload App"; ?></h1>
    </div>
    <div class="me-3">
      <nav>
        <ul class="nav">
          <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
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
  </div>-->
  
  <!-- Main Container -->
  <div class="container my-5">
    <div class="upload-card card shadow-lg p-4 animate__animated animate__fadeInUp" style="max-width:520px;margin:auto;border-radius:20px;">
      <h2 class="mb-3 text-center"><?php echo $editMode ? 'Edit App' : 'Upload New App'; ?></h2>
      <?php if (!empty($success)): ?>
        <div class="alert alert-success animate__animated animate__fadeInUp"><?php echo $success; ?></div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger animate__animated animate__fadeInUp">
          <?php foreach($errors as $e) echo $e . '<br>'; ?>
        </div>
      <?php endif; ?>
      <!-- Onyesha user_id ya sasa kwa debugging -->
    <!-- <div class="alert alert-info">Current user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NONE'; ?></div> -->
      
      <form method="post" enctype="multipart/form-data" autocomplete="off">
        <!-- CSRF token placeholder -->
        <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-font me-1"></i> App Name</label>
<input type="text" name="app_name" class="form-control" value="<?php echo htmlspecialchars($app['app_name'] ?? ''); ?>" required maxlength="100">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-align-left me-1"></i> Description</label>
        <textarea name="description" class="form-control" rows="3" required maxlength="1000"><?php echo htmlspecialchars($app['description'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-image me-1"></i> App Icon (max 512x512px)</label>
          <input type="file" name="logo" class="form-control" accept="image/jpeg,image/png,image/webp" onchange="validateLogo(this)">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-file-archive me-1"></i> APK File</label>
          <input type="file" name="apk" class="form-control" accept=".apk">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-images me-1"></i> Screenshots (max 4)</label>
          <input type="file" name="screenshots[]" class="form-control" accept="image/jpeg,image/png,image/webp" multiple onchange="validateScreenshots(this)">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-list me-1"></i> Category</label>
          <div class="d-flex flex-wrap gap-3">
            <?php $categories = ['Sport','Games','Productivity','Education','Social','Tools','Entertainment','Other']; ?>
            <?php foreach ($categories as $cat): ?>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="category" id="cat_<?php echo $cat; ?>" value="<?php echo $cat; ?>" <?php if($category===$cat) echo 'checked'; ?> required>
                <label class="form-check-label" for="cat_<?php echo $cat; ?>"><?php echo $cat; ?></label>
              </div>
            <?php endforeach; ?>
          </div>
          <div id="customCategoryDiv" class="mt-2" style="display:<?php echo ($category==='Other') ? 'block':'none'; ?>;">
            <input type="text" name="custom_category" class="form-control" placeholder="Enter custom category" value="<?php echo ($category==='Other') ? htmlspecialchars($custom_category ?? '') : ''; ?>">
          </div>
        </div>
        <script>
          document.querySelectorAll('input[name="category"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
              document.getElementById('customCategoryDiv').style.display = (this.value === 'Other') ? 'block' : 'none';
            });
          });
        </script>
        <div class="mb-3">
          <label class="form-label"><i class="fa fa-tags me-1"></i> Tags (comma separated)</label>
     <input type="text" name="tags" class="form-control" value="<?php echo htmlspecialchars($app['tags'] ?? ''); ?>" maxlength="100">
        </div>
        <div class="progress-container mb-3" style="display:none;">
          <div class="progress-bar"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-2"><i class="fa fa-upload me-2"></i><?php echo $editMode ? 'Update App' : 'Upload App'; ?></button>
      </form>
      <div id="toast" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3" style="z-index:9999;display:none;min-width:220px;">
        <div class="d-flex">
          <div class="toast-body"></div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Footer -->
  <?php include ('includes/footer.php'); ?>
  
  <!-- JavaScript for Progress Bar Upload -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      const progressBarContainer = document.querySelector('.progress-container');
      const progressBar = document.querySelector('.progress-bar');
      let isSubmitting = false;

      form.addEventListener('submit', function(event) {
        if (isSubmitting) {
          event.preventDefault();
          return false;
        }
        isSubmitting = true;
        progressBarContainer.style.display = 'block';
        progressBar.style.width = '0%';
        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.upload.onprogress = function(e) {
          if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            progressBar.style.width = percent + '%';
          }
        };
        xhr.onload = function() {
          if (xhr.status === 200) {
            showToast('App uploaded successfully!');
            setTimeout(() => { window.location.href = 'dashboard.php'; }, 1500);
          } else {
            showToast('Error uploading the app.', true);
          }
          progressBarContainer.style.display = 'none';
        };
        xhr.onerror = function() {
          showToast('An error occurred. Please try again.', true);
          progressBarContainer.style.display = 'none';
        };
        xhr.send(formData);
      });

      // Animation for form
      document.querySelector('.upload-card').classList.add('animate__animated', 'animate__fadeInUp');
    });

    // Toast notification
    function showToast(message, isError = false) {
      const toast = document.getElementById('toast');
      toast.querySelector('.toast-body').textContent = message;
      toast.classList.remove('text-bg-success', 'text-bg-danger');
      toast.classList.add(isError ? 'text-bg-danger' : 'text-bg-success');
      toast.style.display = 'block';
      setTimeout(() => { toast.style.display = 'none'; }, 4000);
    }
  </script>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
  <script>
  function validateLogo(input) {
    const file = input.files[0];
    if (file) {
      if (file.size > 500 * 1024) {
        alert('Logo file size must not exceed 500KB.');
        input.value = '';
      }
      const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!allowedTypes.includes(file.type)) {
        alert('Logo must be JPG, PNG, or WEBP.');
        input.value = '';
      }
    }
  }
  function validateScreenshots(input) {
    const files = input.files;
    for (let i = 0; i < files.length; i++) {
      if (files[i].size > 1024 * 1024) {
        alert('Each screenshot must not exceed 1MB.');
        input.value = '';
        break;
      }
      const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      if (!allowedTypes.includes(files[i].type)) {
        alert('Screenshots must be JPG, PNG, or WEBP.');
        input.value = '';
        break;
      }
    }
  }
  </script>
  <?php if (!empty($errors)) { echo '<pre>'; print_r($errors); echo '</pre>'; } ?>
  <?php if (isset($e) && $e instanceof PDOException) { echo '<pre>PDO ERROR: ' . $e->getMessage() . '</pre>'; } ?>
</body>
</html>
