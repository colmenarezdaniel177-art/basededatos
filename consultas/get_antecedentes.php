<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pid = (int)($_GET['paciente_id'] ?? 0);
if (!$pid) { echo '<p class="text-muted small">Sin paciente seleccionado</p>'; exit; }

$stmt = $pdo->prepare("SELECT a.*, t.nombre AS tipo_nombre, t.color AS tipo_color FROM antecedentes a LEFT JOIN tipo_antecedente t ON a.tipo_id=t.id WHERE a.paciente_id=? ORDER BY t.nombre");
$stmt->execute([$pid]);
$ants = $stmt->fetchAll();

if (!$ants) {
    echo '<p class="text-muted small text-center py-2">Sin antecedentes registrados</p>';
} else {
    foreach ($ants as $ant) {
        $color = e($ant['tipo_color'] ?? 'secondary');
        echo '<div class="antecedente-card border-' . $color . ' mb-2">';
        echo '<span class="badge bg-' . $color . ' mb-1">' . e($ant['tipo_nombre'] ?? '-') . '</span>';
        echo '<div class="small">' . e($ant['descripcion']) . '</div>';
        echo '</div>';
    }
}
