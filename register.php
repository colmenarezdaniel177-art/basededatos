<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) { redirect(BASE_URL . '/dashboard.php'); }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$nombre || !$email || !$password) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $chk = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'Ese correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol_id, activo) VALUES (?, ?, ?, 2, 0)");
            $stmt->execute([$nombre, $email, $hash]);
            setFlash('info', 'Cuenta creada. Un administrador debe activarla antes de que puedas iniciar sesión.');
            redirect(BASE_URL . '/home.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro — <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card shadow-lg">
    <div class="auth-header">
      <div class="brand-icon"><i class="fa-solid fa-hospital-user"></i></div>
      <h4 class="mb-0 mt-1 fw-bold"><?= SITE_NAME ?></h4>
      <small class="opacity-75">Sistema de Gestión Médica</small>
    </div>
    <div class="auth-body">
      <h5 class="fw-bold mb-4 text-center text-muted">Crear Cuenta</h5>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php else: ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nombre completo</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="nombre" class="form-control" required value="<?= e($_POST['nombre'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Confirmar contraseña</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="confirm" class="form-control" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">
          <i class="fa-solid fa-user-plus me-2"></i>Registrarse
        </button>
      </form>
      <?php endif; ?>
      <hr>
      <p class="text-center mb-0 small">¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/index.php">Inicia sesión</a></p>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
