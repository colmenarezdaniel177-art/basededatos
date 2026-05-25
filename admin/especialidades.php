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
    $desc   = trim($_POST['descripcion'] ?? '');

    if ($action === 'create' && $nombre) {
        $pdo->prepare("INSERT INTO especialidades (nombre,descripcion) VALUES (?,?)")->execute([$nombre,$desc]);
        setFlash('success','Especialidad creada.');
    } elseif ($action === 'update' && $id) {
        $pdo->prepare("UPDATE especialidades SET nombre=?,descripcion=? WHERE id=?")->execute([$nombre,$desc,$id]);
        setFlash('success','Especialidad actualizada.');
    } elseif ($action === 'delete' && $id) {

        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM especialistas WHERE especialidad_id = ?");
        $stmtCheck->execute([$id]);
        $relaciones = $stmtCheck->fetchColumn();
        if ($relaciones > 0) {        
          setFlash('danger', 'No se puede eliminar la especialidad porque tiene especialistas registrados.');
        } else {
          $pdo->prepare("DELETE FROM especialidades WHERE id=?")->execute([$id]);
          setFlash('success','Especialidad eliminada.');
      }
    }
    redirect(BASE_URL.'/admin/especialidades.php');
}

$especialidades = $pdo->query(
    "SELECT e.*, COUNT(es.id) AS total_especialistas
     FROM especialidades e LEFT JOIN especialistas es ON e.id=es.especialidad_id
     GROUP BY e.id ORDER BY e.nombre"
)->fetchAll();

$pageTitle = 'Especialidades';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-briefcase-medical me-2 text-primary"></i>Especialidades</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Especialidades</li></ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fa-solid fa-plus me-1"></i>Nueva</button>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <input type="text" id="filterEsp" class="form-control form-control-sm" placeholder="🔍 Filtrar especialidades..." style="max-width:320px">
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="tablaEsp">
            <thead><tr><th>#</th><th>Nombre</th><th>Descripción</th><th>Especialistas</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($especialidades as $esp): ?>
              <tr>
                <td><?= $esp['id'] ?></td>
                <td><strong><?= e($esp['nombre']) ?></strong></td>
                <td class="small text-muted"><?= e($esp['descripcion'] ?? '-') ?></td>
                <td><span class="badge bg-secondary"><?= $esp['total_especialistas'] ?></span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="editEsp(<?= htmlspecialchars(json_encode($esp),ENT_QUOTES) ?>)"><i class="fa-solid fa-pen"></i></button>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $esp['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar especialidad?"><i class="fa-solid fa-trash"></i></button>
                  </form>
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

<?php foreach ([['modalCrear','create','Nueva Especialidad'],['modalEditar','update','Editar Especialidad']] as [$mid,$act,$title]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><?= $title ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="<?= $act ?>">
      <?php if ($act==='update'): ?><input type="hidden" name="id" id="<?= $mid ?>Id"><?php endif; ?>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="<?= $mid ?>Nombre" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" id="<?= $mid ?>Desc" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>
<?php endforeach; ?>

<script>
document.getElementById('filterEsp').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaEsp tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
function editEsp(e) {
    document.getElementById('modalEditarId').value   = e.id;
    document.getElementById('modalEditarNombre').value = e.nombre;
    document.getElementById('modalEditarDesc').value   = e.descripcion || '';
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
