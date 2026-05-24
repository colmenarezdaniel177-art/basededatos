<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente_nombre, p.id AS pid, p.fecha_nacimiento, p.genero, CONCAT(e.nombre,' ',e.apellido) AS especialista_nombre, esp.nombre AS especialidad FROM consultas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id WHERE c.id=?");
$stmt->execute([$id]);
$c = $stmt->fetch();
if (!$c) { setFlash('danger','Consulta no encontrada.'); redirect(BASE_URL.'/consultas/index.php'); }

$meds = $pdo->prepare("SELECT cm.*, m.nombre AS medicamento FROM consulta_medicamentos cm JOIN medicamentos m ON cm.medicamento_id=m.id WHERE cm.consulta_id=?");
$meds->execute([$id]);
$meds = $meds->fetchAll();

$antecedentes = $pdo->prepare("SELECT a.*, ta.nombre AS tipo_nombre, ta.color AS tipo_color FROM antecedentes a LEFT JOIN tipo_antecedente ta ON a.tipo_id=ta.id WHERE a.paciente_id=? ORDER BY ta.nombre");
$antecedentes->execute([$c['paciente_id']]);
$antecedentes = $antecedentes->fetchAll();

$pageTitle = 'Consulta #' . $id;
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-file-medical me-2 text-primary"></i>Detalle de Consulta</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/consultas/index.php">Consultas</a></li><li class="breadcrumb-item active">#<?= $id ?></li></ol></nav>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-print me-1"></i>Imprimir</button>
  </div>
  <div class="content-area">
    <div class="row g-3">
      <div class="col-lg-8">

        <!-- Header paciente/doctor -->
        <div class="card mb-3">
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6 class="text-muted text-uppercase small mb-1">Paciente</h6>
                <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $c['pid'] ?>" class="text-decoration-none">
                  <h5 class="mb-0"><?= e($c['paciente_nombre']) ?></h5>
                </a>
                <small class="text-muted"><?= calcularEdad($c['fecha_nacimiento']) ?> &bull; <?= $c['genero'] === 'M' ? 'Masculino' : ($c['genero'] === 'F' ? 'Femenino' : 'Otro') ?></small>
              </div>
              <div class="col-md-6 text-md-end">
                <h6 class="text-muted text-uppercase small mb-1">Especialista</h6>
                <h5 class="mb-0"><?= e($c['especialista_nombre']) ?></h5>
                <small class="text-muted"><?= e($c['especialidad']) ?></small>
              </div>
            </div>
            <hr class="my-2">
            <small class="text-muted"><i class="fa-regular fa-clock me-1"></i><?= formatDateTime($c['fecha']) ?></small>
          </div>
        </div>

        <?php foreach ([['Motivo de Consulta','motivo_consulta','fa-comment-medical','primary'],['Diagnóstico','diagnostico','fa-diagnoses','danger'],['Tratamiento','tratamiento','fa-prescription','success'],['Observaciones','observaciones','fa-sticky-note','secondary']] as [$label,$field,$icon,$color]): ?>
        <?php if (!empty($c[$field])): ?>
        <div class="card mb-3">
          <div class="card-header"><h5><i class="fa-solid <?= $icon ?> me-2 text-<?= $color ?>"></i><?= $label ?></h5></div>
          <div class="card-body"><p class="mb-0"><?= nl2br(e($c[$field])) ?></p></div>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>

        <!-- Medicamentos -->
        <?php if ($meds): ?>
        <div class="card">
          <div class="card-header"><h5><i class="fa-solid fa-pills me-2 text-info"></i>Medicamentos Recetados</h5></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table mb-0">
                <thead><tr><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th></tr></thead>
                <tbody>
                <?php foreach ($meds as $m): ?>
                  <tr>
                    <td><?= e($m['medicamento']) ?></td>
                    <td><?= e($m['dosis'] ?? '-') ?></td>
                    <td><?= e($m['frecuencia'] ?? '-') ?></td>
                    <td><?= e($m['duracion'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>

      <!-- Sidebar antecedentes -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h5><i class="fa-solid fa-clipboard-list me-2 text-warning"></i>Antecedentes</h5></div>
          <div class="card-body">
            <?php if (!$antecedentes): ?>
              <p class="text-muted small text-center">Sin antecedentes</p>
            <?php else: ?>
              <?php foreach ($antecedentes as $ant): ?>
                <div class="antecedente-card border-<?= e($ant['tipo_color'] ?? 'secondary') ?> mb-2">
                  <span class="badge bg-<?= e($ant['tipo_color'] ?? 'secondary') ?> mb-1"><?= e($ant['tipo_nombre'] ?? '-') ?></span>
                  <div class="small"><?= e($ant['descripcion']) ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
