<?php
// includes/config.php
$host = '0.0.0.0:3306';
$db   = 'dream_apkstore';  // Badilisha kama jina la database yako
$user = 'root';            // Database username yako
$pass = 'root';                // Database password yako
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
define('DOMAIN', 'https://dreamapk.store');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<?php
// includes/config.php
$host = '0.0.0.0:3306';
$db   = 'dream_apkstore';  // Badilisha kama jina la database yako
$user = 'root';            // Database username yako
$pass = 'root';                // Database password yako
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
define('DOMAIN', 'https://dreamapk.store');

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>