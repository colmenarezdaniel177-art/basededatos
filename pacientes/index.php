<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$search = trim($_GET['q'] ?? '');
$uid = $_SESSION['user_id'];
$isAdm = isAdmin();
$isMed = isMedico();
$isUsr = ($_SESSION['rol'] ?? '') === 'usuario';

$where = ($isAdm || $isMed) ? '1=1' : 'p.usuario_id = ' . (int)$uid;
$params = [];
if ($search) {
    $where .= " AND (p.nombre LIKE ? OR p.apellido LIKE ? OR p.email LIKE ? OR p.telefono LIKE ?)";
    $params = array_fill(0, 4, "%$search%");
}

$stmt = $pdo->prepare("SELECT p.*, u.nombre AS creado_por FROM pacientes p LEFT JOIN usuarios u ON p.usuario_id=u.id WHERE $where ORDER BY p.nombre, p.apellido");
$stmt->execute($params);
$pacientes = $stmt->fetchAll();

$pageTitle = 'Pacientes';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-users me-2 text-primary"></i>Pacientes</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Pacientes</li></ol></nav>
    </div>
    <a href="<?= BASE_URL ?>/pacientes/add.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nuevo Paciente</a>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <form class="d-flex gap-2" method="GET">
          <input type="text" name="q" class="form-control" placeholder="Buscar paciente..." value="<?= e($search) ?>">
          <button class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
          <?php if ($search): ?><a href="?" class="btn btn-outline-secondary">Limpiar</a><?php endif; ?>
        </form>
      </div>
      <div class="card-body p-0">
        <?php if (!$pacientes): ?>
          <p class="text-center text-muted py-5"><i class="fa-solid fa-users-slash fa-2x d-block mb-2"></i>No hay pacientes registrados</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr><th>#</th><th>Nombre</th><th>Cédula</th><th>Fecha Nac.</th><th>Edad</th><th>Teléfono</th><th>Email</th><?php if($isAdm): ?><th>Registrado por</th><?php endif; ?><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($pacientes as $i => $p): ?>
              <tr>
                <td class="text-muted small"><?= $p['id'] ?></td>
                <td>
                  <strong><?= e($p['nombre'] . ' ' . $p['apellido']) ?></strong>
                  <div class="text-muted small"><?= $p['genero'] === 'M' ? 'Masculino' : 'Femenino' ?></div>
                </td>
                <td class="small"><?= e($p['cedula'] ?? '-') ?></td>
                <td><?= formatDate($p['fecha_nacimiento']) ?></td>
                <td><?= calcularEdad($p['fecha_nacimiento']) ?></td>
                <td><?= e($p['telefono'] ?? '-') ?></td>
                <td><?= e($p['email'] ?? '-') ?></td>
                <?php if($isAdm): ?><td class="text-muted small"><?= e($p['creado_por'] ?? '-') ?></td><?php endif; ?>
                <td>
                  <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-info me-1" title="Ver historial"><i class="fa-solid fa-eye"></i></a>
                  <a href="<?= BASE_URL ?>/pacientes/edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar"><i class="fa-solid fa-pen"></i></a>
                  <?php if (!$isUsr): ?>
                  <a href="<?= BASE_URL ?>/pacientes/delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar paciente? Esta acción no se puede deshacer." title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                  <?php endif; ?>
                </td>
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
<?php include __DIR__ . '/../includes/footer.php'; ?>
