<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("User ID is required.");
}

$user_id = intval($_GET['id']);

// Optional: Prevent deletion of your own account, if needed
// if ($user_id == $_SESSION['user_id']) {
//     die("You cannot delete your own account.");
// }

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
if ($stmt->execute([$user_id])) {
    header("Location: manage_users.php");
    exit;
} else {
    die("Failed to delete user.");
}
?>
