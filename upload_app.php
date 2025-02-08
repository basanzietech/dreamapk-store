<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

redirectIfNotLoggedIn();

$errors = [];
$editMode = false;
$app = null;
$maxLogoSize = 512; // Ukubwa wa max kwa logo (width/height)
$maxScreenshotSize = [1080, 1920]; // Max width x height ya screenshots
$maxLogoFileSize = 500 * 1024; // Max 500KB
$maxScreenshotFileSize = 1 * 1024 * 1024; // Max 1MB

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $app = $stmt->fetch();
    if ($app) {
        $editMode = true;
    } else {
        die("There is no permission to edit this app.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appName     = clean($_POST['app_name'] ?? '');
    $description = clean($_POST['description'] ?? '');
    
    if (empty($appName) || empty($description)) {
        $errors[] = "App name and Description are required";
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
            $errors[] = "Logo size do not exceed {$maxLogoSize}x{$maxLogoSize}px.";
        } elseif ($fileSize > $maxLogoFileSize) {
            $errors[] = "Logo file is not exceeding 500KB.";
        } else {
            $logoPath = $uploadsDir . time() . "_" . basename($_FILES['logo']['name']);
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
                $errors[] = "Failed to load the logo.";
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
            $errors[] = "Failed to load APK File.";
        }
    } else {
        $apkPath = $editMode ? $app['apk_file'] : '';
        if (!$editMode) {
            $errors[] = "APK file is required.";
        }
    }

    // 3. Upload Screenshots
    $screenshots = [];
    for ($i = 1; $i <= 4; $i++) {
        if (isset($_FILES["screenshot$i"]) && $_FILES["screenshot$i"]['error'] == UPLOAD_ERR_OK) {
            $imageSize = getimagesize($_FILES["screenshot$i"]['tmp_name']);
            $fileSize = $_FILES["screenshot$i"]['size'];

            if (!$imageSize) {
                $errors[] = "Screenshot $i Not a valid image.";
            } elseif ($imageSize[0] > $maxScreenshotSize[0] || $imageSize[1] > $maxScreenshotSize[1]) {
                $errors[] = "Screenshot $i Do not increase {$maxScreenshotSize[0]}x{$maxScreenshotSize[1]} px.";
            } elseif ($fileSize > $maxScreenshotFileSize) {
                $errors[] = "Screenshot $i Do not increase 1MB.";
            } else {
                $shotPath = $uploadsDir . time() . "_{$i}_" . basename($_FILES["screenshot$i"]['name']);
                if (move_uploaded_file($_FILES["screenshot$i"]['tmp_name'], $shotPath)) {
                    $screenshots[] = $shotPath;
                } else {
                    $errors[] = "Failed to load the screenshot $i.";
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
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Hii inasaidia responsive design -->

  <title><?php echo $editMode ? "Edit App" : "Upload App"; ?> - dreamapkstore</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- Toolbar -->
  <header class="toolbar d-flex justify-content-between align-items-center bg-dark text-white p-2">
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
  
  <div class="container container-custom my-4">
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
      <button type="submit" class="btn btn-primary"><?php echo $editMode ? "Update" : "Upload"; ?></button>
    </form>
  </div>
  
  <footer class="footer">
  <div class="footer-col">
    <a href="privacy.php">Privacy Policy</a>
    <a href="contact.php">Contact Us</a>
  </div>
  <div class="footer-col">
    <p>&copy; <?php echo date("Y"); ?> dreamapkstore. All rights reserved.</p>
  </div>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const progressBarContainer = document.createElement('div');
    progressBarContainer.style.width = '100%';
    progressBarContainer.style.height = '20px';
    progressBarContainer.style.backgroundColor = '#f3f3f3';
    progressBarContainer.style.marginTop = '20px';
    progressBarContainer.style.display = 'none'; // Initially hidden
    
    const progressBar = document.createElement('div');
    progressBar.style.height = '100%';
    progressBar.style.width = '0%';
    progressBar.style.backgroundColor = '#4caf50';
    progressBar.style.transition = 'width 0.2s ease-in-out';
    
    progressBarContainer.appendChild(progressBar);
    form.appendChild(progressBarContainer);

    form.addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent normal form submission
      
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
          window.location.href = 'dashboard.php'; // Redirect after success
        } else {
          alert('Error uploading the app.');
        }
        progressBarContainer.style.display = 'none'; // Hide after completion
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
