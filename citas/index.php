<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$uid   = $_SESSION['user_id'];
$isAdm = isAdmin();
$isMed = isMedico();
$isUsr = ($_SESSION['rol'] ?? '') === 'usuario';
$filtro = (int)($_GET['status'] ?? 0);

if ($isAdm) {
    $where = '1=1';
} elseif ($isMed && $_SESSION['especialista_id']) {
    $where = 'c.especialista_id = ' . (int)$_SESSION['especialista_id'];
} else {
    $where = 'c.usuario_id = ' . (int)$uid;
}
$params = [];
if ($filtro) { $where .= ' AND c.status_id = ?'; $params[] = $filtro; }

$stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad, sc.nombre AS estado_nombre, sc.color AS estado_color FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE $where ORDER BY c.fecha DESC");
$stmt->execute($params);
$citas = $stmt->fetchAll();

$statuses = $pdo->query("SELECT * FROM status_cita WHERE activo=1 ORDER BY id")->fetchAll();

$pageTitle = 'Citas';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Citas Médicas</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Citas</li></ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/citas/add.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nueva Cita</a>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card mb-3">
      <div class="card-body py-2">
        <div class="d-flex gap-2 flex-wrap">
          <a href="?" class="btn btn-sm <?= !$filtro ? 'btn-primary' : 'btn-outline-secondary' ?>">Todas</a>
          <?php foreach ($statuses as $s): ?>
            <a href="?status=<?= $s['id'] ?>" class="btn btn-sm <?= $filtro == $s['id'] ? 'btn-'.$s['color'] : 'btn-outline-secondary' ?>">
              <?= e($s['nombre']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-body p-0">
        <?php if (!$citas): ?>
          <p class="text-center text-muted py-5"><i class="fa-regular fa-calendar-xmark fa-2x d-block mb-2"></i>No hay citas</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Especialista</th><th>Especialidad</th><th>Motivo</th><th>Status</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($citas as $c): 
              $isCompletada = (isset($c['estado_nombre']) && strtolower(trim($c['estado_nombre'])) === 'completada');
            ?>
              <tr>
                <td><?= formatDate($c['fecha']) ?></td>
                <td><a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $c['paciente_id'] ?>" class="text-decoration-none"><?= e($c['paciente']) ?></a></td>
                <td><?= e($c['especialista']) ?></td>
                <td><small class="text-muted"><?= e($c['especialidad']) ?></small></td>
                <td class="small"><?= e(mb_strimwidth($c['motivo']??'',0,40,'...')) ?></td>
                <td><span class="badge bg-<?= e($c['estado_color']??'secondary') ?>"><?= e($c['estado_nombre']??'-') ?></span></td>
                <td>
                  <?php if (!$isUsr): ?>
                    <?php if ($isCompletada): ?>
                      <button class="btn btn-sm btn-light text-muted me-1" title="Cita completada - No se puede editar" disabled><i class="fa-solid fa-pen"></i></button>
                      <button class="btn btn-sm btn-light text-muted me-1" title="Esta consulta ya fue realizada" disabled><i class="fa-solid fa-stethoscope"></i></button>
                    <?php else: ?>
                      <a href="<?= BASE_URL ?>/citas/edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="fa-solid fa-pen"></i></a>
                      <a href="<?= BASE_URL ?>/consultas/add.php?cita_id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-success me-1" title="Iniciar consulta"><i class="fa-solid fa-stethoscope"></i></a>
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php if ($isUsr): ?>
                    <?php
                    $canceladaId = null;
                    foreach ($statuses as $s) { if (strtolower($s['nombre']) === 'cancelada') { $canceladaId = $s['id']; break; } }
                    $yaCancel = strtolower($c['estado_nombre'] ?? '') === 'cancelada';
                    ?>
                    <?php if (!$yaCancel && $canceladaId && !$isCompletada): ?>
                      <a href="<?= BASE_URL ?>/citas/cancel.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Cancelar esta cita?"><i class="fa-solid fa-ban me-1"></i>Cancelar</a>
                    <?php else: ?>
                      <small class="text-muted"><i class="fa-solid fa-lock"></i> Sin acciones</small>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="<?= BASE_URL ?>/citas/delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar esta cita?"><i class="fa-solid fa-trash"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
