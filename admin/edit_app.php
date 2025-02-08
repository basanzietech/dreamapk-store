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
    // Zaidi ya fields kama logo, APK, etc.

    if (empty($appName) || empty($description)) {
        $errors[] = "Jaza taarifa zote muhimu.";
    }

    if (empty($errors)) {
        // Update query (badilisha kulingana na data unayohifadhi)
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
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Responsive design -->
  <title><?php echo $editMode ? "Edit App" : "Add App"; ?> - Admin</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container my-5">
    <h2><?php echo $editMode ? "Edit App" : "Add App"; ?></h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach($errors as $error): ?>
          <p class="mb-0"><?php echo $error; ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" action="">
      <div class="mb-3">
        <label for="app_name" class="form-label">Jina la App:</label>
        <input type="text" class="form-control" name="app_name" id="app_name" value="<?php echo $editMode ? htmlspecialchars($app['app_name']) : ''; ?>" required>
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Description:</label>
        <textarea class="form-control" name="description" id="description" required><?php echo $editMode ? htmlspecialchars($app['description']) : ''; ?></textarea>
      </div>
      <!-- Ongeza sehemu za file uploads kama logo, APK, etc. kama inahitajika -->
      <button type="submit" class="btn btn-primary"><?php echo $editMode ? "Update" : "Submit"; ?></button>
    </form>
  </div>
  
  <footer class="footer">
  <div class="footer-col">
    <a href="privacy.php">Privacy Policy</a>
    <a href="contact.php">Contact Us</a>
  </div>
  <div class="footer-col">
    <p>&copy; <?php echo date("Y"); ?> dream-apkstore. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
