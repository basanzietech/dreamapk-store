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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appName     = clean($_POST['app_name'] ?? '');
    $description = clean($_POST['description'] ?? '');
    
    if (empty($appName) || empty($description)) {
        $errors[] = "App name and Description are required.";
    }
    
    $uploadsDir = "uploads/";
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    // 1. Upload Logo (App Icon)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $imageSize = getimagesize($_FILES['logo']['tmp_name']);
        $fileSize = $_FILES['logo']['size'];

        if (!$imageSize) {
            $errors[] = "The logo file is not a valid image.";
        } elseif ($imageSize[0] > $maxLogoSize || $imageSize[1] > $maxLogoSize) {
            $errors[] = "Logo dimensions must not exceed {$maxLogoSize}x{$maxLogoSize}px.";
        } elseif ($fileSize > $maxLogoFileSize) {
            $errors[] = "Logo file size must not exceed 500KB.";
        } else {
            $logoPath = $uploadsDir . time() . "_" . basename($_FILES['logo']['name']);
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
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
    if (isset($_FILES['apk']) && $_FILES['apk']['error'] == UPLOAD_ERR_OK) {
        $apkPath = $uploadsDir . time() . "_" . basename($_FILES['apk']['name']);
        if (!move_uploaded_file($_FILES['apk']['tmp_name'], $apkPath)) {
            $errors[] = "Failed to upload the APK file.";
        }
    } else {
        $apkPath = $editMode ? $app['apk_file'] : '';
        if (!$editMode) {
            $errors[] = "APK file is required.";
        }
    }

    // 3. Upload Screenshots (up to 4)
    $screenshots = [];
    for ($i = 1; $i <= 4; $i++) {
        if (isset($_FILES["screenshot$i"]) && $_FILES["screenshot$i"]['error'] == UPLOAD_ERR_OK) {
            $imageSize = getimagesize($_FILES["screenshot$i"]['tmp_name']);
            $fileSize = $_FILES["screenshot$i"]['size'];

            if (!$imageSize) {
                $errors[] = "Screenshot $i is not a valid image.";
            } elseif ($imageSize[0] > $maxScreenshotSize[0] || $imageSize[1] > $maxScreenshotSize[1]) {
                $errors[] = "Screenshot $i dimensions must not exceed {$maxScreenshotSize[0]}x{$maxScreenshotSize[1]}px.";
            } elseif ($fileSize > $maxScreenshotFileSize) {
                $errors[] = "Screenshot $i file size must not exceed 1MB.";
            } else {
                $shotPath = $uploadsDir . time() . "_{$i}_" . basename($_FILES["screenshot$i"]['name']);
                if (move_uploaded_file($_FILES["screenshot$i"]['tmp_name'], $shotPath)) {
                    $screenshots[] = $shotPath;
                } else {
                    $errors[] = "Failed to upload screenshot $i.";
                }
            }
        }
    }

    if ($editMode && empty($screenshots)) {
        $screenshotsJSON = $app['screenshots'];
    } else {
        $screenshotsJSON = json_encode($screenshots);
    }

    if (empty($errors)) {
        if ($editMode) {
            $stmt = $pdo->prepare("UPDATE apps SET app_name = ?, description = ?, logo = ?, apk_file = ?, screenshots = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$appName, $description, $logoPath, $apkPath, $screenshotsJSON, $app['id'], $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO apps (user_id, app_name, description, logo, apk_file, screenshots, downloads) VALUES (?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([$_SESSION['user_id'], $appName, $description, $logoPath, $apkPath, $screenshotsJSON]);
        }
        header("Location: dashboard.php");
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
  
  <meta name="google-site-verification" content="oru7iffKii3izNSxniby6XBD4hSKsG9bNzjqDsUHucw" />
  
  <meta name="google-adsense-account" content="ca-pub-4690089323418332">
  <title><?php echo $editMode ? "Edit App" : "Upload App"; ?> - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="assets/css/style.css">

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
  
  <!-- Main Container -->
  <div class="container">
    <div class="upload-card">
      <?php if ($errors): ?>
        <div class="alert alert-danger">
          <?php foreach($errors as $error): ?>
            <p class="mb-0"><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <form method="post" action="" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="app_name" class="form-label">App Name:</label>
          <input type="text" name="app_name" id="app_name" class="form-control" value="<?php echo $editMode ? htmlspecialchars($app['app_name']) : ''; ?>" required>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description:</label>
          <textarea name="description" id="description" class="form-control" rows="4" required><?php echo $editMode ? htmlspecialchars($app['description']) : ''; ?></textarea>
        </div>
        <div class="mb-3">
          <label for="logo" class="form-label">App Icon (Max: 512x512px, 500KB):</label>
          <input type="file" name="logo" id="logo" class="form-control" <?php echo $editMode ? '' : 'required'; ?>>
        </div>
        <div class="mb-3">
          <label for="apk" class="form-label">APK File:</label>
          <input type="file" name="apk" id="apk" class="form-control" <?php echo $editMode ? '' : 'required'; ?>>
        </div>
        <div class="mb-3">
          <label class="form-label">Screenshots (Max: 1080x1920px, 1MB each, up to 4):</label>
          <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="mb-2">
              <input type="file" name="screenshot<?php echo $i; ?>" class="form-control">
            </div>
          <?php endfor; ?>
        </div>
        <button type="submit" class="btn btn-primary w-100"><?php echo $editMode ? "Update" : "Upload"; ?></button>
        <!-- Progress Bar Container -->
        <div class="progress-container">
          <div class="progress-bar"></div>
        </div>
      </form>
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

      form.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        progressBarContainer.style.display = 'block'; // Show progress bar
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
            alert('App uploaded successfully!');
            window.location.href = 'dashboard.php'; // Redirect on success
          } else {
            alert('Error uploading the app.');
          }
          progressBarContainer.style.display = 'none';
        };

        xhr.onerror = function() {
          alert('An error occurred. Please try again.');
          progressBarContainer.style.display = 'none';
        };

        xhr.send(formData);
      });
    });
  </script>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
