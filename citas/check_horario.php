<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
header('Content-Type: application/json');

$espId = (int)($_GET['especialista_id'] ?? 0);
$fecha = $_GET['fecha'] ?? '';

if (!$espId || !$fecha) {
    echo json_encode(['disponible' => null, 'msg' => '']);
    exit;
}

// dia_semana: 0=Dom, 1=Lun... (igual que PHP date('w'))
$diaSemana = (int)date('w', strtotime($fecha));

$stmt = $pdo->prepare("SELECT * FROM horarios_especialista WHERE especialistaId=? AND dia_semana=?");
$stmt->execute([$espId, $diaSemana]);
$horario = $stmt->fetch();

$diasNombre = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];

if ($horario) {
    echo json_encode([
        'disponible' => true,
        'msg' => "✔ Disponible: {$diasNombre[$diaSemana]} de {$horario['hora_inicio']} a {$horario['hora_fin']}",
        'color' => 'success',
    ]);
} else {
    // Check if specialist has ANY schedule
    $total = $pdo->prepare("SELECT COUNT(*) FROM horarios_especialista WHERE especialistaId=?");
    $total->execute([$espId]);
    $tiene = $total->fetchColumn() > 0;

    if (!$tiene) {
        echo json_encode(['disponible' => null, 'msg' => '⚠ Este especialista no tiene horario configurado.', 'color' => 'warning']);
    } else {
        echo json_encode(['disponible' => false, 'msg' => "✘ El especialista NO atiende los {$diasNombre[$diaSemana]}.", 'color' => 'danger']);
    }
}
