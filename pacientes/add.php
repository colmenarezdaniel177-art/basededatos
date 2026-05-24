<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $cedula   = trim($_POST['cedula'] ?? '');
    $fnac     = $_POST['fecha_nacimiento'] ?? '';
    $genero   = $_POST['genero'] ?? 'M';
    $tel      = trim($_POST['telefono'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $dir      = trim($_POST['direccion'] ?? '');

    if (!$nombre) $errors[] = 'El nombre es obligatorio.';
    if (!$apellido) $errors[] = 'El apellido es obligatorio.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO pacientes (usuario_id, nombre, apellido, cedula, fecha_nacimiento, genero, telefono, email, direccion) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'], $nombre, $apellido, $cedula ?: null, $fnac ?: null, $genero, $tel, $email, $dir]);
        $id = $pdo->lastInsertId();
        setFlash('success', 'Paciente registrado correctamente.');
        redirect(BASE_URL . '/pacientes/view.php?id=' . $id);
    }
}

$pageTitle = 'Nuevo Paciente';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-user-plus me-2 text-primary"></i>Nuevo Paciente</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pacientes/index.php">Pacientes</a></li><li class="breadcrumb-item active">Nuevo</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><h5>Datos del Paciente</h5></div>
          <div class="card-body">
            <?php if ($errors): ?>
              <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div>
            <?php endif; ?>
            <form method="POST">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre *</label>
                  <input type="text" name="nombre" class="form-control" required value="<?= e($_POST['nombre'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apellido *</label>
                  <input type="text" name="apellido" class="form-control" required value="<?= e($_POST['apellido'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cédula</label>
                  <input type="text" name="cedula" class="form-control" value="<?= e($_POST['cedula'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Fecha de nacimiento</label>
                  <input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($_POST['fecha_nacimiento'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Género</label>
                  <select name="genero" class="form-select">
                    <option value="M" <?= ($_POST['genero'] ?? 'M') === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= ($_POST['genero'] ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Teléfono</label>
                  <input type="text" name="telefono" class="form-control" value="<?= e($_POST['telefono'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Correo electrónico</label>
                  <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label">Dirección</label>
                  <textarea name="direccion" class="form-control" rows="2"><?= e($_POST['direccion'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Guardar</button>
                  <a href="<?= BASE_URL ?>/pacientes/index.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
