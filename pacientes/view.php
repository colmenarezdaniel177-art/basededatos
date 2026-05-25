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
if (!$p) { setFlash('danger','Paciente no encontrado.'); redirect(BASE_URL.'/pacientes/index.php'); }
if (!isAdmin() && !isMedico() && $p['usuario_id'] != $_SESSION['user_id']) { redirect(BASE_URL.'/pacientes/index.php'); }

// Antecedentes con tipo desde tabla
$antecedentes = $pdo->prepare("SELECT a.*, t.nombre AS tipo_nombre, t.color AS tipo_color FROM antecedentes a LEFT JOIN tipo_antecedente t ON a.tipo_id=t.id WHERE a.paciente_id=? ORDER BY t.nombre, a.fecha DESC");
$antecedentes->execute([$id]);
$antecedentes = $antecedentes->fetchAll();

// Tipos activos para agrupar
$tiposAnte = $pdo->query("SELECT * FROM tipo_antecedente WHERE activo=1 ORDER BY nombre")->fetchAll();

// Consultas
$consultas = $pdo->prepare("SELECT c.*, CONCAT(e.nombre,' ',e.apellido) AS especialista, esp.nombre AS especialidad FROM consultas c JOIN especialistas e ON c.especialista_id=e.id JOIN especialidades esp ON e.especialidad_id=esp.id WHERE c.paciente_id=? ORDER BY c.fecha DESC");
$consultas->execute([$id]);
$consultas = $consultas->fetchAll();

// Citas recientes
$citas = $pdo->prepare("SELECT c.*, CONCAT(e.nombre,' ',e.apellido) AS especialista, sc.nombre AS estado_nombre, sc.color AS estado_color FROM citas c JOIN especialistas e ON c.especialista_id=e.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE c.paciente_id=? ORDER BY c.fecha DESC LIMIT 10");
$citas->execute([$id]);
$citas = $citas->fetchAll();

$pageTitle = 'Historial - ' . $p['nombre'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-file-medical me-2 text-primary"></i>Historial del Paciente</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pacientes/index.php">Pacientes</a></li><li class="breadcrumb-item active"><?= e($p['nombre'].' '.$p['apellido']) ?></li></ol></nav>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= BASE_URL ?>/pacientes/edit.php?id=<?= $id ?>" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-pen me-1"></i>Editar</a>
      <a href="<?= BASE_URL ?>/citas/add.php?paciente_id=<?= $id ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-calendar-plus me-1"></i>Nueva Cita</a>
    </div>
  </div>
  <div class="content-area">
    <?php showFlash() ?>

    <!-- Info paciente -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3 align-items-center">
          <div class="col-auto">
            <div style="width:70px;height:70px;border-radius:50%;background:var(--primary);color:#fff;font-size:1.8rem;display:flex;align-items:center;justify-content:center;font-weight:700">
              <?= strtoupper(substr($p['nombre'],0,1)) ?>
            </div>
          </div>
          <div class="col">
            <h4 class="mb-0"><?= e($p['nombre'].' '.$p['apellido']) ?></h4>
            <div class="text-muted small"><?= $p['genero']==='M'?'Masculino':($p['genero']==='F'?'Femenino':'Otro') ?> &bull; <?= calcularEdad($p['fecha_nacimiento']) ?> (<?= formatDate($p['fecha_nacimiento']) ?>)</div>
          </div>
          <div class="col-auto text-end small">
            <?php if ($p['cedula']): ?><div><i class="fa-solid fa-id-card me-1 text-muted"></i><?= e($p['cedula']) ?></div><?php endif; ?>
            <?php if ($p['telefono']): ?><div><i class="fa-solid fa-phone me-1 text-muted"></i><?= e($p['telefono']) ?></div><?php endif; ?>
            <?php if ($p['email']): ?><div><i class="fa-solid fa-envelope me-1 text-muted"></i><?= e($p['email']) ?></div><?php endif; ?>
            <?php if ($p['direccion']): ?><div><i class="fa-solid fa-location-dot me-1 text-muted"></i><?= e($p['direccion']) ?></div><?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <!-- Antecedentes -->
      <div class="col-lg-5">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fa-solid fa-clipboard-list me-2 text-warning"></i>Antecedentes</h5>
<?php if (isAdmin() || isMedico()): ?>

            <a href="<?= BASE_URL ?>/pacientes/antecedente_add.php?paciente_id=<?= $id ?>" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-plus me-1"></i>Agregar</a>
             <?php endif; ?>
          </div>
          <div class="card-body">
            <?php if (!$antecedentes): ?>
              <p class="text-muted text-center py-3">Sin antecedentes registrados</p>
            <?php else: ?>
              <?php
              // Group by tipo
              $grouped = [];
              foreach ($antecedentes as $ant) {
                  $grouped[$ant['tipo_nombre'] ?? 'Sin tipo'][] = $ant;
              }
              foreach ($grouped as $tipo => $ants):
                  $color = $ants[0]['tipo_color'] ?? 'secondary';
              ?>
              <h6 class="mt-2 mb-1 fw-bold text-<?= e($color) ?>"><?= e($tipo) ?></h6>
              <?php foreach ($ants as $ant): ?>
                <div class="antecedente-card border-<?= e($color) ?> mb-2">
                  <div class="d-flex justify-content-between">
                    <small class="fw-semibold"><?= e($ant['descripcion']) ?></small>
                    <?php if (isAdmin() || isMedico()): ?>
                      <a href="<?= BASE_URL ?>/pacientes/antecedente_delete.php?id=<?= $ant['id'] ?>&paciente_id=<?= $id ?>" class="text-danger" data-confirm="¿Eliminar antecedente?"><i class="fa-solid fa-times fa-xs"></i></a>
                    <?php endif; ?>
                  </div>
                  <?php if ($ant['fecha']): ?><div class="text-muted" style="font-size:.75rem"><?= formatDate($ant['fecha']) ?></div><?php endif; ?>
                </div>
              <?php endforeach; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <!-- Historial de consultas -->
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fa-solid fa-stethoscope me-2 text-info"></i>Historial de Consultas</h5>
            <a href="<?= BASE_URL ?>/consultas/add.php?paciente_id=<?= $id ?>" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-plus me-1"></i>Nueva</a>
          </div>
          <div class="card-body p-0">
            <?php if (!$consultas): ?>
              <p class="text-muted text-center py-3">Sin consultas registradas</p>
            <?php else: ?>
            <div class="timeline p-3">
              <?php foreach ($consultas as $c): ?>
              <div class="timeline-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <strong class="small"><?= formatDate($c['fecha']) ?></strong>
                    <span class="text-muted small ms-2">— <?= e($c['especialista']) ?></span>
                  </div>
                  <a href="<?= BASE_URL ?>/consultas/view.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary py-0">Ver</a>
                </div>
                <?php if ($c['diagnostico']): ?><p class="mb-1 small mt-1"><strong>Dx:</strong> <?= e($c['diagnostico']) ?></p><?php endif; ?>
                <?php if ($c['tratamiento']): ?><p class="mb-0 small text-muted"><strong>Tx:</strong> <?= e($c['tratamiento']) ?></p><?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Citas recientes -->
        <div class="card">
          <div class="card-header"><h5><i class="fa-solid fa-calendar me-2 text-success"></i>Citas Recientes</h5></div>
          <div class="card-body p-0">
            <?php if (!$citas): ?>
              <p class="text-muted text-center py-3">Sin citas</p>
            <?php else: ?>
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <thead><tr><th>Fecha</th><th>Especialista</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($citas as $c): ?>
                <tr>
                  <td><?= formatDate($c['fecha']) ?></td>
                  <td class="small"><?= e($c['especialista']) ?></td>
                  <td><span class="badge bg-<?= e($c['estado_color']??'secondary') ?>"><?= e($c['estado_nombre']??'-') ?></span></td>
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
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
