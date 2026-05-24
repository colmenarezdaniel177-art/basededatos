<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM citas WHERE id=?");
$stmt->execute([$id]);
$cita = $stmt->fetch();
if ($cita && (isAdmin() || $cita['usuario_id'] == $_SESSION['user_id'])) {
    $pdo->prepare("DELETE FROM citas WHERE id=?")->execute([$id]);
    setFlash('success','Cita eliminada.');
} else {
    setFlash('danger','No se pudo eliminar.');
}
redirect(BASE_URL . '/citas/index.php');
