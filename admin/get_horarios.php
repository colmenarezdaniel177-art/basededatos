<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
header('Content-Type: application/json');
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM horarios_especialista WHERE especialistaId=? ORDER BY dia_semana, hora_inicio");
$stmt->execute([$id]);
echo json_encode($stmt->fetchAll());
