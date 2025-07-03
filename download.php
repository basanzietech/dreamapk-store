<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("App ID not found!.");
}

$app_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM apps WHERE id = ?");
$stmt->execute([$app_id]);
$app = $stmt->fetch();
if (!$app) {
    die("App not found!.");
}

// Ongeza downloads count
$stmt = $pdo->prepare("UPDATE apps SET downloads = downloads + 1 WHERE id = ?");
$stmt->execute([$app_id]);

$file = $app['apk_file'];
if (!file_exists($file)) {
    die("File not available.");
}

// Safeguard: clear output buffer
if (ob_get_level()) ob_end_clean();

// Set headers for safe APK download
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . filesize($file));

// Disable caching for extra safety
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

flush();
readfile($file);
exit;
?>