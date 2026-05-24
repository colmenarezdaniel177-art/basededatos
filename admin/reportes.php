<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');

// Stats del período
$totalCitas = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha BETWEEN ? AND ?")->execute([$desde,$hasta]) ? $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha BETWEEN ? AND ?")->execute([$desde,$hasta]) : 0;
$s = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha BETWEEN ? AND ?"); $s->execute([$desde,$hasta]); $totalCitas = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha BETWEEN ? AND ? AND estado='Completada'"); $s->execute([$desde,$hasta]); $citasComp = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM citas WHERE fecha BETWEEN ? AND ? AND estado='Cancelada'"); $s->execute([$desde,$hasta]); $citasCanceladas = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM consultas WHERE DATE(fecha) BETWEEN ? AND ?"); $s->execute([$desde,$hasta]); $totalConsultas = $s->fetchColumn();
$s = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE DATE(created_at) BETWEEN ? AND ?"); $s->execute([$desde,$hasta]); $nuevoPacientes = $s->fetchColumn();

// Citas por especialidad
$porEsp = $pdo->prepare("SELECT esp.nombre, COUNT(c.id) AS total FROM citas c JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id WHERE c.fecha BETWEEN ? AND ? GROUP BY esp.id ORDER BY total DESC");
$porEsp->execute([$desde,$hasta]);
$porEsp = $porEsp->fetchAll();

// Top especialistas
$topEsp = $pdo->prepare("SELECT CONCAT(e.nombre,' ',e.apellido) AS especialista, COUNT(c.id) AS total, SUM(c.estado='Completada') AS completadas FROM citas c JOIN especialistas e ON c.especialista_id=e.id WHERE c.fecha BETWEEN ? AND ? GROUP BY e.id ORDER BY total DESC LIMIT 10");
$topEsp->execute([$desde,$hasta]);
$topEsp = $topEsp->fetchAll();

// Citas por estado
$porEstado = $pdo->prepare("SELECT estado, COUNT(*) AS total FROM citas WHERE fecha BETWEEN ? AND ? GROUP BY estado");
$porEstado->execute([$desde,$hasta]);
$porEstado = $porEstado->fetchAll();

// Últimas consultas
$ultConsultas = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista FROM consultas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id WHERE DATE(c.fecha) BETWEEN ? AND ? ORDER BY c.fecha DESC LIMIT 10");
$ultConsultas->execute([$desde,$hasta]);
$ultConsultas = $ultConsultas->fetchAll();

$pageTitle = 'Reportes';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Reportes</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Reportes</li></ol></nav>
    </div>
  </div>
  <div class="content-area">

    <!-- Filtro fechas -->
    <div class="card mb-3">
      <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
          <div class="col-auto"><label class="form-label mb-1">Desde</label><input type="date" name="desde" class="form-control" value="<?= e($desde) ?>"></div>
          <div class="col-auto"><label class="form-label mb-1">Hasta</label><input type="date" name="hasta" class="form-control" value="<?= e($hasta) ?>"></div>
          <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filtrar</button></div>
          <div class="col-auto"><small class="text-muted">Período: <?= formatDate($desde) ?> — <?= formatDate($hasta) ?></small></div>
        </form>
      </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-4"><div class="stat-card stat-blue"><p>Citas en período</p><h3><?= $totalCitas ?></h3><div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div></div></div>
      <div class="col-sm-6 col-lg-4"><div class="stat-card stat-green"><p>Citas completadas</p><h3><?= $citasComp ?></h3><div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div></div></div>
      <div class="col-sm-6 col-lg-4"><div class="stat-card stat-orange"><p>Citas canceladas</p><h3><?= $citasCanceladas ?></h3><div class="stat-icon"><i class="fa-solid fa-ban"></i></div></div></div>
      <div class="col-sm-6 col-lg-6"><div class="stat-card stat-purple"><p>Consultas realizadas</p><h3><?= $totalConsultas ?></h3><div class="stat-icon"><i class="fa-solid fa-stethoscope"></i></div></div></div>
      <div class="col-sm-6 col-lg-6"><div class="stat-card stat-teal"><p>Nuevos pacientes</p><h3><?= $nuevoPacientes ?></h3><div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div></div></div>
    </div>

    <div class="row g-3">
      <!-- Por especialidad -->
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-header"><h5>Citas por Especialidad</h5></div>
          <div class="card-body">
            <?php if (!$porEsp): ?><p class="text-muted text-center">Sin datos</p><?php endif; ?>
            <?php
            $maxEsp = $porEsp ? max(array_column($porEsp, 'total')) : 1;
            foreach ($porEsp as $e):
              $pct = round($e['total'] / $maxEsp * 100);
            ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small mb-1"><span><?= e($e['nombre']) ?></span><strong><?= $e['total'] ?></strong></div>
              <div class="progress" style="height:8px"><div class="progress-bar" style="width:<?= $pct ?>%"></div></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Top especialistas -->
      <div class="col-lg-7">
        <div class="card mb-3">
          <div class="card-header"><h5>Top Especialistas</h5></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead><tr><th>Especialista</th><th>Total Citas</th><th>Completadas</th></tr></thead>
                <tbody>
                <?php foreach ($topEsp as $e): ?>
                  <tr><td><?= e($e['especialista']) ?></td><td><?= $e['total'] ?></td><td><span class="badge bg-success"><?= $e['completadas'] ?></span></td></tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h5>Últimas Consultas</h5></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Especialista</th></tr></thead>
                <tbody>
                <?php foreach ($ultConsultas as $c): ?>
                  <tr>
                    <td class="small"><?= formatDate($c['fecha']) ?></td>
                    <td><a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $c['paciente_id'] ?>" class="text-decoration-none small"><?= e($c['paciente']) ?></a></td>
                    <td class="small"><?= e($c['especialista']) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
