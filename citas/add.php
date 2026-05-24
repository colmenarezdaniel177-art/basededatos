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

if ($isAdm || $isMed) {
    $pacientes = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes ORDER BY nombre")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes WHERE usuario_id=? ORDER BY nombre");
    $stmt->execute([$uid]);
    $pacientes = $stmt->fetchAll();
}

// Para médico: cargar su especialista asignado
$medicoEspecialista = null;
if ($isMed && $_SESSION['especialista_id']) {
    $stmtMe = $pdo->prepare("SELECT e.id, CONCAT(e.nombre,' ',e.apellido) AS nombre, esp.nombre AS especialidad FROM especialistas e JOIN especialidades esp ON e.especialidad_id=esp.id WHERE e.id=?");
    $stmtMe->execute([$_SESSION['especialista_id']]);
    $medicoEspecialista = $stmtMe->fetch();
}

$especialistas = $pdo->query("SELECT e.id, CONCAT(e.nombre,' ',e.apellido) AS nombre, esp.nombre AS especialidad FROM especialistas e JOIN especialidades esp ON e.especialidad_id=esp.id WHERE e.activo=1 ORDER BY e.nombre")->fetchAll();
$statuses      = $pdo->query("SELECT * FROM status_cita WHERE activo=1 ORDER BY id")->fetchAll();

$errors = [];
$preselPaciente = (int)($_GET['paciente_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id    = (int)$_POST['paciente_id'];
    $especialista_id = (int)$_POST['especialista_id'];
    $fecha    = $_POST['fecha'] ?? '';
    $motivo   = trim($_POST['motivo'] ?? '');
    $status_id = (int)($_POST['status_id'] ?? 1);

    if (!$paciente_id)     $errors[] = 'Selecciona un paciente.';
    if (!$especialista_id) $errors[] = 'Selecciona un especialista.';
    if (!$fecha)           $errors[] = 'La fecha es obligatoria.';

    if (!$errors) {
        $ins = $pdo->prepare("INSERT INTO citas (paciente_id,especialista_id,usuario_id,fecha,motivo,status_id,estado) VALUES (?,?,?,?,?,?,?)");
        // Get status name for estado field compatibility
        $sNom = $pdo->prepare("SELECT nombre FROM status_cita WHERE id=?"); $sNom->execute([$status_id]);
        $estadoNom = $sNom->fetchColumn() ?: 'Pendiente';
        $ins->execute([$paciente_id,$especialista_id,$uid,$fecha,$motivo,$status_id,$estadoNom]);
        setFlash('success','Cita agendada correctamente.');
        redirect(BASE_URL.'/citas/index.php');
    }
}

$pageTitle = 'Nueva Cita';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-calendar-plus me-2 text-primary"></i>Agendar Cita</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/citas/index.php">Citas</a></li><li class="breadcrumb-item active">Nueva</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h5>Datos de la Cita</h5></div>
          <div class="card-body">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div><?php endif; ?>
            <?php if (!$pacientes): ?><div class="alert alert-warning">No hay pacientes. <a href="<?= BASE_URL ?>/pacientes/add.php">Agrega uno primero</a>.</div><?php endif; ?>
            <form method="POST">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Paciente *</label>
                  <select name="paciente_id" class="form-select" required>
                    <option value="">Seleccionar paciente...</option>
                    <?php foreach ($pacientes as $pac): ?>
                      <option value="<?= $pac['id'] ?>" <?= (($_POST['paciente_id'] ?? $preselPaciente) == $pac['id']) ? 'selected' : '' ?>><?= e($pac['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Especialista *</label>
                  <?php if ($medicoEspecialista): ?>
                    <input type="hidden" name="especialista_id" id="selectEsp" value="<?= $medicoEspecialista['id'] ?>">
                    <input type="text" class="form-control" value="<?= e($medicoEspecialista['nombre']) ?> — <?= e($medicoEspecialista['especialidad']) ?>" readonly>
                  <?php else: ?>
                    <select name="especialista_id" id="selectEsp" class="form-select" required onchange="validarHorario()">
                      <option value="">Seleccionar especialista...</option>
                      <?php foreach ($especialistas as $esp): ?>
                        <option value="<?= $esp['id'] ?>" <?= (($_POST['especialista_id'] ?? 0) == $esp['id']) ? 'selected' : '' ?>>
                          <?= e($esp['nombre']) ?> — <?= e($esp['especialidad']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  <?php endif; ?>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Fecha *</label>
                  <input type="date" name="fecha" id="fechaCita" class="form-control" required min="<?= date('Y-m-d') ?>" value="<?= e($_POST['fecha'] ?? '') ?>" onchange="validarHorario()">
                  <div id="horarioMsg" class="mt-1 small"></div>
                </div>
                <?php if (!$isMed && !$isAdm): ?>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <input type="hidden" name="status_id" value="1">
                  <input type="text" class="form-control" value="Pendiente" readonly>
                </div>
                <?php else: ?>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <select name="status_id" class="form-select">
                    <?php foreach ($statuses as $s): ?>
                      <option value="<?= $s['id'] ?>" <?= (($_POST['status_id'] ?? 1) == $s['id']) ? 'selected' : '' ?>>
                        <?= e($s['nombre']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <?php endif; ?>
                <div class="col-12">
                  <label class="form-label">Motivo</label>
                  <textarea name="motivo" class="form-control" rows="2"><?= e($_POST['motivo'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Guardar</button>
                  <a href="<?= BASE_URL ?>/citas/index.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function validarHorario() {
  const espId = document.getElementById('selectEsp').value;
  const fecha  = document.getElementById('fechaCita').value;
  const msg    = document.getElementById('horarioMsg');
  if (!espId || !fecha) { msg.innerHTML=''; return; }
  fetch(`<?= BASE_URL ?>/citas/check_horario.php?especialista_id=${espId}&fecha=${fecha}`)
    .then(r => r.json())
    .then(d => {
      if (!d.msg) { msg.innerHTML=''; return; }
      msg.innerHTML = `<span class="text-${d.color}">${d.msg}</span>`;
    });
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
