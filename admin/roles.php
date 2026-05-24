<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');

    if ($action === 'create' && $nombre) {
        $pdo->prepare("INSERT INTO roles (nombre, es_sistema) VALUES (?, 0)")->execute([$nombre]);
        setFlash('success','Rol creado.');
    } elseif ($action === 'delete' && $id) {
        // Verificar que no sea de sistema
        $sys = $pdo->prepare("SELECT es_sistema FROM roles WHERE id=?");
        $sys->execute([$id]);
        $r = $sys->fetch();
        if ($r && $r['es_sistema']) {
            setFlash('danger','No se puede eliminar un rol de sistema.');
        } else {
            $pdo->prepare("DELETE FROM roles WHERE id=?")->execute([$id]);
            setFlash('success','Rol eliminado.');
        }
    }
    redirect(BASE_URL.'/admin/roles.php');
}

$roles = $pdo->query("SELECT r.*, COUNT(u.id) AS total_usuarios FROM roles r LEFT JOIN usuarios u ON r.id=u.rol_id GROUP BY r.id")->fetchAll();

$pageTitle = 'Roles';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-id-badge me-2 text-primary"></i>Roles</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Roles</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="row g-3 justify-content-center">
      <div class="col-lg-6">
        <div class="card mb-3">
          <div class="card-header"><h5>Agregar Rol personalizado</h5></div>
          <div class="card-body">
            <form method="POST" class="d-flex gap-2">
              <input type="hidden" name="action" value="create">
              <input type="text" name="nombre" class="form-control" placeholder="Nombre del rol" required>
              <button type="submit" class="btn btn-primary">Agregar</button>
            </form>
          </div>
        </div>
        <div class="card">
          <div class="card-body p-0">
            <table class="table mb-0">
              <thead><tr><th>#</th><th>Nombre</th><th>Tipo</th><th>Usuarios</th><th>Acciones</th></tr></thead>
              <tbody>
              <?php foreach ($roles as $r): ?>
                <tr>
                  <td><?= $r['id'] ?></td>
                  <td><strong><?= e($r['nombre']) ?></strong></td>
                  <td>
                    <?php if ($r['es_sistema']): ?>
                      <span class="badge bg-danger"><i class="fa-solid fa-lock me-1"></i>Sistema</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Personalizado</span>
                    <?php endif; ?>
                  </td>
                  <td><span class="badge bg-secondary"><?= $r['total_usuarios'] ?></span></td>
                  <td>
                    <?php if (!$r['es_sistema']): ?>
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $r['id'] ?>">
                      <button class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar rol?"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    <?php else: ?>
                    <span class="text-muted small">No eliminable</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
