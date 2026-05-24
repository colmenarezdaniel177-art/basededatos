<?php
function navLink(string $href, string $icon, string $label, string $page, string $dir = ''): void {
    $scriptPath = $_SERVER['PHP_SELF'] ?? '';
    $active = (str_contains($scriptPath, $page) || ($dir && str_contains($scriptPath, '/'.$dir.'/'))) ? ' active' : '';
    echo "<a href=\"" . BASE_URL . $href . "\" class=\"{$active}\"><i class=\"fa-solid {$icon}\"></i> {$label}</a>";
}

$rol = $_SESSION['rol'] ?? 'usuario';
?>
<nav class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fa-solid fa-hospital-user"></i></div>
    <span><?= SITE_NAME ?></span>
  </div>

  <div class="sidebar-nav">
    <div class="nav-section">Principal</div>
    <?php navLink('/dashboard.php', 'fa-gauge-high', 'Dashboard', 'dashboard.php') ?>

    <div class="nav-section">Módulos</div>
    <?php navLink('/pacientes/index.php', 'fa-users', 'Pacientes', '', 'pacientes') ?>
    <?php navLink('/citas/index.php', 'fa-calendar-check', 'Citas', '', 'citas') ?>
    <?php if ($rol !== 'usuario'): ?>
    <?php navLink('/consultas/index.php', 'fa-stethoscope', 'Consultas', '', 'consultas') ?>
    <?php endif; ?>

    <?php if ($rol === 'admin'): ?>
    <div class="nav-section">Administración</div>
    <?php navLink('/admin/usuarios.php',        'fa-user-shield',       'Usuarios',          'usuarios.php',        'admin') ?>
    <?php navLink('/admin/roles.php',           'fa-id-badge',          'Roles',              'roles.php',           'admin') ?>
    <?php navLink('/admin/especialidades.php',  'fa-briefcase-medical', 'Especialidades',     'especialidades.php',  'admin') ?>
    <?php navLink('/admin/especialistas.php',   'fa-user-doctor',       'Especialistas',      'especialistas.php',   'admin') ?>
    <?php navLink('/admin/medicamentos.php',    'fa-pills',             'Medicamentos',       'medicamentos.php',    'admin') ?>
    <?php navLink('/admin/status_cita.php',     'fa-tags',              'Status de Cita',     'status_cita.php',     'admin') ?>
    <?php navLink('/admin/tipo_antecedente.php','fa-list-check',        'Tipos Antecedente',  'tipo_antecedente.php','admin') ?>
    <?php navLink('/reportes/index.php',        'fa-chart-bar',         'Reportes',           '',                    'reportes') ?>
    <?php endif; ?>
  </div>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= e($_SESSION['user_name'] ?? '') ?></div>
        <div class="user-role"><?= e(ucfirst($rol)) ?></div>
      </div>
      <a href="<?= BASE_URL ?>/logout.php" class="ms-auto text-white opacity-50" title="Cerrar sesión">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>
  </div>
</nav>
