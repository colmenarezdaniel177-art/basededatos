<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    $rol = $_SESSION['rol'] ?? 'usuario';
    redirect(in_array($rol, ['admin','medico']) ? BASE_URL.'/dashboard.php' : BASE_URL.'/home.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credential = trim($_POST['credential'] ?? '');
    $password   = $_POST['password'] ?? '';
    if (login($pdo, $credential, $password)) {
        $rol = $_SESSION['rol'] ?? 'usuario';
        redirect(in_array($rol, ['admin','medico']) ? BASE_URL.'/dashboard.php' : BASE_URL.'/home.php');
    } else {
        $error = 'Credenciales incorrectas.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar Sesión — <?= SITE_NAME ?></title>
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
      <small class="opacity-75">OzonoVital — Sistema de Gestión Médica</small>
    </div>
    <div class="auth-body">
      <h5 class="fw-bold mb-4 text-center text-muted">Iniciar Sesión</h5>
      <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark me-2"></i><?= e($error) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Correo electrónico o nombre de usuario</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="credential" class="form-control"
                   placeholder="correo@ejemplo.com o nombre" required autofocus
                   value="<?= e($_POST['credential'] ?? '') ?>">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Contraseña</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">
          <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar
        </button>
      </form>
      <hr>
      <p class="text-center mb-0 small">¿No tienes cuenta? <a href="<?= BASE_URL ?>/register.php">Regístrate</a></p>
      <p class="text-center mb-0 small mt-1"><a href="<?= BASE_URL ?>/home.php">← Volver al inicio</a></p>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
