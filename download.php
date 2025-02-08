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

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.android.package-archive');
// Ongeza header hii ili kuzuia MIME sniffing:
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));

readfile($file);
exit;
?>