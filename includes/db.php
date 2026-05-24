<?php
if (!defined('DB_HOST')) {
    $cfg = dirname(__DIR__) . '/config.php';
    if (file_exists($cfg)) require_once $cfg;
}
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#c00"><strong>Error de conexión:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>');
}
