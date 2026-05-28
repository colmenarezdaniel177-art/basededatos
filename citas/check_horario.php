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

    $stmtEsp = $pdo->prepare("SELECT citas_max_por_dia FROM especialistas WHERE id=?");
    $stmtEsp->execute([$espId]);
    $maxCitas = (int)($stmtEsp->fetchColumn() ?: 1);

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM citas INNER JOIN status_cita ON citas.status_id = status_cita.id WHERE especialista_id=? AND DATE(fecha)=DATE(?) AND nombre != 'Cancelada'");
    $stmtCount->execute([$espId, $fecha]);
    $citasAgendadas = (int)$stmtCount->fetchColumn();
    if ($citasAgendadas >= $maxCitas) {
        echo json_encode([
            'disponible' => false,
            'msg' => "✘ Cupo lleno: El especialista ya alcanzó el límite máximo de {$maxCitas} citas para este día.",
            'color' => 'danger',
        ]);
    }else{
        $disponibles = $maxCitas - $citasAgendadas;
        echo json_encode([
            'disponible' => true,
            'msg' => "✔ Disponible: {$diasNombre[$diaSemana]} de {$horario['hora_inicio']} a {$horario['hora_fin']} ({$disponibles} cupos restantes de {$maxCitas})",        
            'color' => 'success',
        ]);
    }
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
