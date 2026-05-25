<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if ($p && (isAdmin() || $p['usuario_id'] == $_SESSION['user_id'])) {

    $stmtCheck = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM citas WHERE paciente_id = ?) + 
            (SELECT COUNT(*) FROM consultas WHERE paciente_id = ?) AS total_relaciones
    ");
    $stmtCheck->execute([$id, $id]);
    $relaciones = $stmtCheck->fetchColumn();
    if ($relaciones > 0) {        
        setFlash('danger', 'No se puede eliminar el paciente porque tiene citas o consultas registradas.');
    } else {

        $pdo->prepare("DELETE FROM pacientes WHERE id=?")->execute([$id]);
        setFlash('success', 'Paciente eliminado.');
    }
} else {
    setFlash('danger', 'No se pudo eliminar el paciente.');
}
redirect(BASE_URL . '/pacientes/index.php');
