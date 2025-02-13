<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn();

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'assistant') {
    die("Hakuna ruhusa.");
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
        die("App haikupatikana.");
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appName     = clean($_POST['app_name'] ?? '');
    $description = clean($_POST['description'] ?? '');
    // Additional fields such as logo, APK, etc. can be processed here.

    if (empty($appName) || empty($description)) {
        $errors[] = "Jaza taarifa zote muhimu.";
    }

    if (empty($errors)) {
        // Update query – adjust this according to your database structure
        $stmt = $pdo->prepare("UPDATE apps SET app_name = ?, description = ? WHERE id = ?");
        $stmt->execute([$appName, $description, $app['id']]);
        header("Location: index.php");
        exit;
    }
}
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
  <!-- Header -->
  <header class="toolbar">
    <div class="container d-flex justify-content-between align-items-center">
      <h1><?php echo $editMode ? "Edit App" : "Add App"; ?></h1>
      <nav>
        <ul class="nav">
          <li class="nav-item"><a href="index.php" class="nav-link">Dashboard</a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

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
      <form method="post" action="">
        <div class="mb-3">
          <label for="app_name" class="form-label">Jina la App:</label>
          <input type="text" name="app_name" id="app_name" class="form-control" value="<?php echo $editMode ? htmlspecialchars($app['app_name']) : ''; ?>" required>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description:</label>
          <textarea name="description" id="description" class="form-control" rows="4" required><?php echo $editMode ? htmlspecialchars($app['description']) : ''; ?></textarea>
        </div>
        <!-- Additional file upload fields can be added here -->
        <button type="submit" class="btn btn-primary w-100"><?php echo $editMode ? "Update" : "Submit"; ?></button>
      </form>
    </div>
  </div>

  <!-- Footer -->
 <?php include ('../includes/footer.php'); ?>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
