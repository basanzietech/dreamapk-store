<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token. Please refresh the page and try again.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$active = intval($_POST['active'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ? AND role = 'assistant'");
        if ($stmt->execute([$active, $id])) {
            echo json_encode(['success' => true]);
            exit;
        }
    } catch (PDOException $e) {
        error_log('DATABASE ERROR (toggle_assistant_status): ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error. Please try again later.']);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Failed to update']); 