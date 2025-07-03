<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$active = intval($_POST['active'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ? AND role = 'assistant'");
    if ($stmt->execute([$active, $id])) {
        echo json_encode(['success' => true]);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'Failed to update']); 