<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$pacientes    = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes ORDER BY nombre")->fetchAll();
$especialistas = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM especialistas WHERE activo=1 ORDER BY nombre")->fetchAll();

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

    <!-- ── REPORTES BÁSICOS ── -->
    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-file-pdf me-2"></i>Reportes Generales</h5>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card h-100">
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
        <div class="card h-100">
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
        <div class="card h-100">
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

    <!-- ── DIRECCIÓN ── -->
    <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-compass me-2"></i>Dirección</h5>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-ranking-star fa-3x text-warning mb-3"></i>
            <h5>Especialidades Más Demandadas</h5>
            <p class="text-muted small">Evalúe qué áreas clínicas tienen mayor volumen para planificar contrataciones.</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_especialidades_demandadas_pdf.php" class="btn btn-warning btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-chart-line fa-3x text-success mb-3"></i>
            <h5>Crecimiento Base de Pacientes</h5>
            <p class="text-muted small">Análisis mensual de captación de usuarios en el sistema clínico.</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_crecimiento_pacientes_pdf.php" class="btn btn-success btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-chart-bar fa-3x text-info mb-3"></i>
            <h5>Volumen Mensual de Consultas</h5>
            <p class="text-muted small">Cantidad total de consultas procesadas por mes.</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_volumen_consultas_pdf.php" class="btn btn-info btn-sm text-white"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── SUPERVISIÓN ── -->
    <h5 class="fw-bold text-danger mb-3"><i class="fa-solid fa-shield-halved me-2"></i>Supervisión</h5>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-clipboard-list fa-3x text-orange mb-3" style="color:#fd7e14"></i>
            <h5>Carga Clínica por Antecedente</h5>
            <p class="text-muted small">Pacientes únicos por cada categoría de antecedente registrado.</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_carga_clinica_pdf.php" class="btn btn-sm" style="background:#fd7e14;color:#fff"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-user-doctor fa-3x mb-3" style="color:#6610f2"></i>
            <h5>Rendimiento de Especialistas</h5>
            <p class="text-muted small">Total de citas por especialista en el período seleccionado.</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_rendimiento_especialistas_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              <button type="submit" class="btn btn-sm text-white" style="background:#6610f2"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-calendar-xmark fa-3x text-danger mb-3"></i>
            <h5>Citas Canceladas / Ausencias</h5>
            <p class="text-muted small">Control de inasistencias y citas canceladas con motivo.</p>
            <div class="mt-3">
              <a href="<?= BASE_URL ?>/reportes/reporte_citas_canceladas_pdf.php" class="btn btn-danger btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-id-card fa-3x text-primary mb-3"></i>
            <h5>Ficha de Paciente</h5>
            <p class="text-muted small">Datos personales y antecedentes médicos completos del paciente.</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_ficha_paciente_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <select name="paciente_id" class="form-select form-select-sm" required>
                <option value="">Seleccionar paciente...</option>
                <?php foreach ($pacientes as $pac): ?>
                  <option value="<?= $pac['id'] ?>"><?= e($pac['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-calendar-days fa-3x text-secondary mb-3"></i>
            <h5>Agenda por Especialista</h5>
            <p class="text-muted small">Citas asignadas a un especialista en un rango de fechas.</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_agenda_especialista_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <select name="especialista_id" class="form-select form-select-sm" required>
                <option value="">Seleccionar especialista...</option>
                <?php foreach ($especialistas as $esp): ?>
                  <option value="<?= $esp['id'] ?>"><?= e($esp['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center py-4">
            <i class="fa-solid fa-user-check fa-3x text-success mb-3"></i>
            <h5>Pacientes Atendidos</h5>
            <p class="text-muted small">Listado de pacientes atendidos en consulta por período.</p>
            <form method="GET" action="<?= BASE_URL ?>/reportes/reporte_pacientes_atendidos_pdf.php" class="d-flex flex-column gap-2 mt-3">
              <input type="date" name="desde" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" required>
              <input type="date" name="hasta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              <button type="submit" class="btn btn-success btn-sm"><i class="fa-solid fa-file-pdf me-1"></i>Generar PDF</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
