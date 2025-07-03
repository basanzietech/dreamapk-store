<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'assistant') {
    // die("Hakuna ruhusa.");
    $fatal_error = "Hakuna ruhusa.";
}

$app = null;
$editMode = false;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $app = $stmt->fetch();
    if ($app) {
        $editMode = true;
    } else {
        // die("App haikupatikana.");
        $fatal_error = "App haikupatikana.";
    }
}

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token. Please refresh the page and try again.';
    } else {
        $appName     = clean($_POST['app_name'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $apkPath = $app['apk_path'] ?? '';
        if (isset($_FILES['apk_file']) && $_FILES['apk_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['apk_file']['name'], PATHINFO_EXTENSION));
            if ($ext === 'apk') {
                $newName = time() . '_' . uniqid() . '.apk';
                $target = '../uploads/' . $newName;
                if (move_uploaded_file($_FILES['apk_file']['tmp_name'], $target)) {
                    $apkPath = $target;
                } else {
                    $errors[] = 'APK upload failed.';
                }
            } else {
                $errors[] = 'Only APK files are allowed.';
            }
        }
        if (empty($appName) || empty($description)) {
            $errors[] = "Jaza taarifa zote muhimu.";
        }
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("UPDATE apps SET app_name = ?, description = ?, apk_path = ? WHERE id = ?");
                $stmt->execute([$appName, $description, $apkPath, $app['id']]);
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $errors[] = "Failed to update app. Please try again later.";
                error_log('DATABASE ERROR (edit_app): ' . $e->getMessage());
            }
        }
    }
}

if (isset($fatal_error)): ?>
  <div class="alert alert-danger text-center my-5"><?php echo $fatal_error; ?></div>
  <?php exit; ?>
<?php endif; ?>
?>
<!DOCTYPE html>
<html lang="sw">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $editMode ? "Edit App" : "Add App"; ?> - Admin</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Optional custom CSS file -->
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
  <?php include('admin_header.php'); ?>
  <div class="admin-main-content">
    <!-- Main Content -->
    <div class="container">
      <div class="upload-card">
        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <p class="mb-0"><?php echo $error; ?></p>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <div class="mb-3">
            <label for="app_name" class="form-label">Jina la App:</label>
            <input type="text" name="app_name" id="app_name" class="form-control" value="<?php echo $editMode ? htmlspecialchars($app['app_name']) : ''; ?>" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" id="description" class="form-control" rows="4" required><?php echo $editMode ? htmlspecialchars($app['description']) : ''; ?></textarea>
          </div>
          <div class="mb-3">
            <label for="apk_file" class="form-label">APK File (optional):</label>
            <input type="file" name="apk_file" id="apk_file" class="form-control" accept=".apk">
            <?php if ($editMode && !empty($app['apk_path'])): ?>
              <small class="text-muted">Current APK: <?php echo htmlspecialchars(basename($app['apk_path'])); ?></small>
            <?php endif; ?>
          </div>
          <button type="submit" class="btn btn-primary w-100"><?php echo $editMode ? "Update" : "Submit"; ?></button>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
 <?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
