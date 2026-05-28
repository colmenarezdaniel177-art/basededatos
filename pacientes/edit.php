<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { setFlash('danger', 'Paciente no encontrado.'); redirect(BASE_URL . '/pacientes/index.php'); }
if (!isAdmin() && $p['usuario_id'] != $_SESSION['user_id']) { redirect(BASE_URL . '/pacientes/index.php'); }

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
    if (!ctype_digit($cedula)) {
        $errors[] = 'La cédula debe contener solo números.';
    } elseif (strlen($cedula) > 8) {
        $errors[] = 'La cédula no puede superar los 8 caracteres.';
    }
    if (!ctype_digit($tel)) {
        $errors[] = 'El teléfono debe contener solo números.';
    } elseif (strlen($tel) > 11) {
        $errors[] = 'El teléfono no puede superar los 11 caracteres.';
    }

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM pacientes WHERE cedula = ? AND id != ?");
        $stmtCheck->execute([$cedula, $id]); // $id es el ID del paciente que se está editando
        $cedulaExiste = $stmtCheck->fetchColumn();

        if ($cedulaExiste > 0) {
            $errors[] = 'La cédula ingresada ya pertenece a otro paciente registrado.';
        }

    if (!$errors) {
        $upd = $pdo->prepare("UPDATE pacientes SET nombre=?,apellido=?,cedula=?,fecha_nacimiento=?,genero=?,telefono=?,email=?,direccion=? WHERE id=?");
        $upd->execute([$nombre, $apellido, $cedula ?: null, $fnac ?: null, $genero, $tel, $email, $dir, $id]);
        setFlash('success', 'Paciente actualizado.');
        redirect(BASE_URL . '/pacientes/view.php?id=' . $id);
    }
    $p = array_merge($p, $_POST);
}

$pageTitle = 'Editar Paciente';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-pen me-2 text-primary"></i>Editar Paciente</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pacientes/index.php">Pacientes</a></li><li class="breadcrumb-item active">Editar</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><h5>Editar: <?= e($p['nombre'] . ' ' . $p['apellido']) ?></h5></div>
          <div class="card-body">
            <?php if ($errors): ?>
              <div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div>
            <?php endif; ?>
            <form method="POST">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Nombre *</label>
                  <input type="text" name="nombre" class="form-control" required value="<?= e($p['nombre']) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apellido *</label>
                  <input type="text" name="apellido" class="form-control" required value="<?= e($p['apellido']) ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Cédula</label>
                  <input type="text" name="cedula" pattern="\d+" title="Solo se permiten números (máximo 8 dígitos)"  class="form-control" placeholder="Ej. 12345678" value="<?= e($_POST['cedula'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Fecha de nacimiento</label>
                  <input type="date" name="fecha_nacimiento" class="form-control" value="<?= e($p['fecha_nacimiento'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Género</label>
                  <select name="genero" class="form-select">
                    <option value="M" <?= $p['genero'] === 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= $p['genero'] === 'F' ? 'selected' : '' ?>>Femenino</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Teléfono</label>
                                    <input type="text" 
                         name="telefono" 
                         class="form-control" 
                         placeholder="Ej. 04121234567" 
                         maxlength="11" 
                         pattern="\d+" 
                         title="Solo se permiten números (máximo 11 dígitos)"                           
                         value="<?= e($_POST['telefono'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Correo electrónico</label>
                  <input type="email" name="email" class="form-control" value="<?= e($p['email'] ?? '') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label">Dirección</label>
                  <textarea name="direccion" class="form-control" rows="2"><?= e($p['direccion'] ?? '') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Actualizar</button>
                  <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $id ?>" class="btn btn-outline-secondary">Cancelar</a>
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
