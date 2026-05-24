<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pageTitle = 'Reportes';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Reportes PDF</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Reportes</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <?php showFlash() ?>

    <?php if (!file_exists(__DIR__ . '/../vendor/fpdf/fpdf.php')): ?>
    <div class="alert alert-warning">
      <h5><i class="fa-solid fa-triangle-exclamation me-2"></i>FPDF no instalado</h5>
      <p class="mb-1">Para generar reportes PDF debes:</p>
      <ol class="mb-0">
        <li>Descarga FPDF desde <strong>http://www.fpdf.org/</strong></li>
        <li>Coloca <code>fpdf.php</code> en <code>vendor/fpdf/fpdf.php</code></li>
      </ol>
    </div>
    <?php endif; ?>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-calendar-check fa-3x text-primary mb-3"></i>
            <h5>Reporte de Citas</h5>
            <p class="text-muted small">Lista de citas por rango de fechas</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_citas_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-users fa-3x text-success mb-3"></i>
            <h5>Reporte de Pacientes</h5>
            <p class="text-muted small">Listado completo de pacientes registrados</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_pacientes_pdf.php" class="btn btn-success btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-stethoscope fa-3x text-info mb-3"></i>
            <h5>Reporte de Consultas</h5>
            <p class="text-muted small">Consultas realizadas por período</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_consultas_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              <button type="submit" class="btn btn-info btn-sm text-white"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
