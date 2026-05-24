<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$pid = (int)($_GET['paciente_id'] ?? $_POST['paciente_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id=?");
$stmt->execute([$pid]);
$p = $stmt->fetch();
if (!$p) { redirect(BASE_URL . '/pacientes/index.php'); }

$tiposAnte = $pdo->query("SELECT * FROM tipo_antecedente WHERE activo=1 ORDER BY nombre")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_id = (int)($_POST['tipo_id'] ?? 0);
    $desc    = trim($_POST['descripcion'] ?? '');
    $fecha   = $_POST['fecha'] ?? '';

    if (!$tipo_id) $errors[] = 'Selecciona el tipo de antecedente.';
    if (!$desc)    $errors[] = 'La descripción es obligatoria.';

    if (!$errors) {
        $ins = $pdo->prepare("INSERT INTO antecedentes (paciente_id, tipo_id, descripcion, fecha) VALUES (?,?,?,?)");
        $ins->execute([$pid, $tipo_id, $desc, $fecha ?: null]);
        setFlash('success', 'Antecedente agregado.');
        $back = $_POST['redirect_url'] ?? '';
        redirect($back ?: BASE_URL . '/pacientes/view.php?id=' . $pid);
    }
}

$pageTitle = 'Agregar Antecedente';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-clipboard-list me-2 text-warning"></i>Agregar Antecedente</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pacientes/index.php">Pacientes</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $pid ?>"><?= e($p['nombre'].' '.$p['apellido']) ?></a></li>
        <li class="breadcrumb-item active">Antecedente</li>
      </ol></nav>
    </div>
  </div>
  <div class="content-area">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-header"><h5>Nuevo Antecedente — <?= e($p['nombre'].' '.$p['apellido']) ?></h5></div>
          <div class="card-body">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div><?php endif; ?>
            <form method="POST">
              <input type="hidden" name="paciente_id" value="<?= $pid ?>">
              <div class="mb-3">
                <label class="form-label">Tipo de antecedente *</label>
                <select name="tipo_id" class="form-select" required>
                  <option value="">Seleccionar...</option>
                  <?php foreach ($tiposAnte as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= (($_POST['tipo_id'] ?? 0) == $t['id']) ? 'selected' : '' ?>>
                      <?= e($t['nombre']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción *</label>
                <textarea name="descripcion" class="form-control" rows="3" required><?= e($_POST['descripcion'] ?? '') ?></textarea>
              </div>
              <div class="mb-4">
                <label class="form-label">Fecha (opcional)</label>
                <input type="date" name="fecha" class="form-control" value="<?= e($_POST['fecha'] ?? '') ?>">
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning text-white"><i class="fa-solid fa-save me-2"></i>Guardar</button>
                <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $pid ?>" class="btn btn-outline-secondary">Cancelar</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
