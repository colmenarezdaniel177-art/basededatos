<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM consultas WHERE id=?")->execute([$id]);
setFlash('success','Consulta eliminada.');
redirect(BASE_URL.'/consultas/index.php');
