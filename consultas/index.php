<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
if (!isAdmin() && !isMedico()) { redirect(BASE_URL . '/home.php'); }

$isAdm = isAdmin();
$isMed = isMedico();
$uid   = $_SESSION['user_id'];
$search = trim($_GET['q'] ?? '');

if ($isAdm) {
    $where = '1=1';
} elseif ($isMed && $_SESSION['especialista_id']) {
    $where = 'c.especialista_id = ' . (int)$_SESSION['especialista_id'];
} else {
    $where = '1=0';
}
$params = [];
if ($search) {
    $where .= " AND (p.nombre LIKE ? OR p.apellido LIKE ? OR c.diagnostico LIKE ?)";
    $params = ["%$search%","%$search%","%$search%"];
}

$stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad FROM consultas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id LEFT JOIN citas ci ON c.cita_id=ci.id WHERE $where ORDER BY c.fecha DESC");
$stmt->execute($params);
$consultas = $stmt->fetchAll();

$pageTitle = 'Consultas';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-stethoscope me-2 text-primary"></i>Consultas Médicas</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Consultas</li></ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/consultas/add.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nueva Consulta</a>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <form class="d-flex gap-2" method="GET">
          <input type="text" name="q" class="form-control" placeholder="Buscar paciente o diagnóstico..." value="<?= e($search) ?>">
          <button class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
          <?php if ($search): ?><a href="?" class="btn btn-outline-secondary">Limpiar</a><?php endif; ?>
        </form>
      </div>
      <div class="card-body p-0">
        <?php if (!$consultas): ?>
          <p class="text-center text-muted py-5"><i class="fa-solid fa-notes-medical fa-2x d-block mb-2"></i>No hay consultas registradas</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Especialista</th><th>Diagnóstico</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($consultas as $c): ?>
              <tr>
                <td><?= formatDateTime($c['fecha']) ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $c['paciente_id'] ?>" class="text-decoration-none">
                    <?= e($c['paciente']) ?>
                  </a>
                </td>
                <td>
                  <?= e($c['especialista']) ?>
                  <div class="text-muted small"><?= e($c['especialidad']) ?></div>
                </td>
                <td class="small"><?= e(mb_strimwidth($c['diagnostico'] ?? '-', 0, 60, '...')) ?></td>
                <td>
                  <a href="<?= BASE_URL ?>/consultas/view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-info me-1"><i class="fa-solid fa-eye"></i></a>
                  <a href="<?= BASE_URL ?>/consultas/delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar esta consulta?"><i class="fa-solid fa-trash"></i></a>
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
