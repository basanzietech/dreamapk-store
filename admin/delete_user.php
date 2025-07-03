<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "User ID is required.";
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

$user_id = intval($_GET['id']);
// Zuia kufuta account yako mwenyewe
if ($user_id == $_SESSION['user_id']) {
    echo "You cannot delete your own account.";
    exit;
}
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$user_id])) {
        header("Location: manage_users.php?success=deleted");
        exit;
    } else {
        echo "Failed to delete user.";
        exit;
    }
} catch (PDOException $e) {
    error_log('DATABASE ERROR (delete_user): ' . $e->getMessage());
    echo "Failed to delete user. Please try again later.";
    exit;
}
?>
