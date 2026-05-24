<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id  = (int)($_GET['id'] ?? 0);
$uid = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM citas WHERE id=?");
$stmt->execute([$id]);
$cita = $stmt->fetch();

if (!$cita) { setFlash('danger','Cita no encontrada.'); redirect(BASE_URL.'/citas/index.php'); }
if ($cita['usuario_id'] != $uid && !isAdmin() && !isMedico()) { redirect(BASE_URL.'/citas/index.php'); }

$canceladaId = $pdo->query("SELECT id FROM status_cita WHERE nombre='Cancelada' LIMIT 1")->fetchColumn();
$pdo->prepare("UPDATE citas SET status_id=?, estado='Cancelada' WHERE id=?")->execute([$canceladaId ?: null, $id]);

setFlash('success','Cita cancelada.');
redirect(BASE_URL.'/citas/index.php');
