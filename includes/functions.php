<?php
// includes/functions.php
session_start();
require_once 'config.php';

// Angalia kama mtumiaji ameingia
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Angalia kama mtumiaji ana role za admin au assistant
function isAdmin() {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'assistant']);
}

// Redirect kama user hajafanyiwa login
function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Function ya kusafisha data
function clean($data) {
    return htmlspecialchars(trim($data));
}

// Mfano wa function ya pagination kwa meza ya apps
function paginate($pdo, $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    $stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM apps ORDER BY downloads DESC LIMIT :offset, :perPage");
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
    $stmt->execute();
    $apps = $stmt->fetchAll();
    $total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
    return ['apps' => $apps, 'total' => $total];
}
?>
