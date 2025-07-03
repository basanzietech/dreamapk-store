<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
redirectIfNotLoggedIn();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("App ID is Required!.");
}

$app_id = intval($_GET['id']);

// Kwa ulinzi, unaweza kuangalia kama user ana ruhusa ya kufuta
$stmt = $pdo->prepare("DELETE FROM apps WHERE id = ?");
$stmt->execute([$app_id]);

header("Location: index.php");
exit;
?>
