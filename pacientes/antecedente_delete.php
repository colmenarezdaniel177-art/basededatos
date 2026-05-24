<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id  = (int)($_GET['id'] ?? 0);
$pid = (int)($_GET['paciente_id'] ?? 0);
$del = $pdo->prepare("DELETE FROM antecedentes WHERE id=?");
$del->execute([$id]);
setFlash('success', 'Antecedente eliminado.');
redirect(BASE_URL . '/pacientes/view.php?id=' . $pid);
