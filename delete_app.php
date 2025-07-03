<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "App ID is required.";
    exit;
}

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    echo "Invalid CSRF token. Please refresh the page and try again.";
    exit;
}

$app_id = intval($_GET['id']);
// Hakikisha user ana ruhusa ya kufuta app
$stmt = $pdo->prepare("SELECT user_id FROM apps WHERE id = ?");
$stmt->execute([$app_id]);
$app = $stmt->fetch();
if (!$app) {
    echo "App not found.";
    exit;
}
if ($app['user_id'] != $_SESSION['user_id']) {
    echo "You do not have permission to delete this app.";
    exit;
}
try {
    $stmt = $pdo->prepare("DELETE FROM apps WHERE id = ?");
    $stmt->execute([$app_id]);
    header("Location: dashboard.php?success=deleted");
    exit;
} catch (PDOException $e) {
    error_log('DATABASE ERROR (delete_app): ' . $e->getMessage());
    echo "Failed to delete app. Please try again later.";
    exit;
}
?>
