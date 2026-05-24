<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
requireLogin();

// Usuarios normales van al home
if ($_SESSION['rol'] === 'usuario') {
    redirect(BASE_URL . '/home.php');
}

$uid   = $_SESSION['user_id'];
$isAdm = isAdmin();

$totalPacientes     = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
$totalEspecialistas = $pdo->query("SELECT COUNT(*) FROM especialistas WHERE activo=1")->fetchColumn();
$totalConsultas     = $pdo->query("SELECT COUNT(*) FROM consultas")->fetchColumn();
$totalCitas         = $pdo->query("SELECT COUNT(*) FROM citas c JOIN status_cita s ON c.status_id=s.id WHERE s.nombre IN ('Pendiente','Confirmada')")->fetchColumn();

if ($isAdm) {
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
}

// Próximas citas
$citasQ = $isAdm
    ? $pdo->query("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad, sc.nombre AS estado_nombre, sc.color AS estado_color FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE c.fecha >= CURDATE() ORDER BY c.fecha LIMIT 8")
    : $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad, sc.nombre AS estado_nombre, sc.color AS estado_color FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE c.usuario_id=? AND c.fecha >= CURDATE() ORDER BY c.fecha LIMIT 8");

if (!$isAdm) { $citasQ->execute([$uid]); }
$proximasCitas = $isAdm ? $citasQ->fetchAll() : $citasQ->fetchAll();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-gauge-high me-2 text-primary"></i>Dashboard</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active">Inicio</li></ol></nav>
    </div>
    <div class="text-muted small">Bienvenido, <strong><?= e($_SESSION['user_name']) ?></strong>
      <span class="badge bg-<?= isAdmin() ? 'danger' : 'info' ?> ms-2"><?= ucfirst($_SESSION['rol']) ?></span>
    </div>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3"><div class="stat-card stat-blue"><p>Pacientes</p><h3><?= $totalPacientes ?></h3><div class="stat-icon"><i class="fa-solid fa-users"></i></div></div></div>
      <div class="col-sm-6 col-xl-3"><div class="stat-card stat-orange"><p>Citas activas</p><h3><?= $totalCitas ?></h3><div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div></div></div>
      <div class="col-sm-6 col-xl-3"><div class="stat-card stat-green"><p>Especialistas</p><h3><?= $totalEspecialistas ?></h3><div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div></div></div>
      <div class="col-sm-6 col-xl-3"><div class="stat-card stat-purple"><p>Consultas</p><h3><?= $totalConsultas ?></h3><div class="stat-icon"><i class="fa-solid fa-stethoscope"></i></div></div></div>
      <?php if ($isAdm): ?>
      <div class="col-sm-6 col-xl-3"><div class="stat-card stat-teal"><p>Usuarios</p><h3><?= $totalUsuarios ?></h3><div class="stat-icon"><i class="fa-solid fa-user-shield"></i></div></div></div>
      <?php endif; ?>
    </div>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Próximas Citas</h5>
            <a href="<?= BASE_URL ?>/citas/add.php" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Nueva cita</a>
          </div>
          <div class="card-body p-0">
            <?php if (!$proximasCitas): ?>
              <p class="text-muted text-center py-4"><i class="fa-regular fa-calendar fa-2x d-block mb-2"></i>No hay citas próximas</p>
            <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Especialista</th><th>Estado</th></tr></thead>
                <tbody>
                <?php foreach ($proximasCitas as $c): ?>
                <tr>
                  <td><?= formatDate($c['fecha']) ?></td>
                  <td><?= e($c['paciente']) ?></td>
                  <td><small><?= e($c['especialista']) ?><br><span class="text-muted"><?= e($c['especialidad']) ?></span></small></td>
                  <td><span class="badge bg-<?= e($c['estado_color'] ?? 'secondary') ?>"><?= e($c['estado_nombre'] ?? '-') ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h5><i class="fa-solid fa-bolt me-2 text-warning"></i>Acciones rápidas</h5></div>
          <div class="card-body d-grid gap-2">
            <a href="<?= BASE_URL ?>/pacientes/add.php" class="btn btn-outline-primary"><i class="fa-solid fa-user-plus me-2"></i>Nuevo Paciente</a>
            <a href="<?= BASE_URL ?>/citas/add.php" class="btn btn-outline-success"><i class="fa-solid fa-calendar-plus me-2"></i>Agendar Cita</a>
            <a href="<?= BASE_URL ?>/consultas/add.php" class="btn btn-outline-info"><i class="fa-solid fa-notes-medical me-2"></i>Registrar Consulta</a>
            <?php if ($isAdm): ?>
            <a href="<?= BASE_URL ?>/reportes/index.php" class="btn btn-outline-secondary"><i class="fa-solid fa-chart-bar me-2"></i>Reportes</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
