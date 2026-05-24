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
    $dosis  = trim($_POST['dosis_sugerida'] ?? '');
    $activo = (int)($_POST['activo'] ?? 1);

    if ($action === 'create') {
        $pdo->prepare("INSERT INTO medicamentos (nombre,descripcion,dosis_sugerida,activo) VALUES (?,?,?,?)")->execute([$nombre,$desc,$dosis,$activo]);
        setFlash('success','Medicamento creado.');
    } elseif ($action === 'update') {
        $pdo->prepare("UPDATE medicamentos SET nombre=?,descripcion=?,dosis_sugerida=?,activo=? WHERE id=?")->execute([$nombre,$desc,$dosis,$activo,$id]);
        setFlash('success','Medicamento actualizado.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM medicamentos WHERE id=?")->execute([$id]);
        setFlash('success','Medicamento eliminado.');
    }
    redirect(BASE_URL.'/admin/medicamentos.php');
}

$meds = $pdo->query("SELECT * FROM medicamentos ORDER BY nombre")->fetchAll();

$pageTitle = 'Medicamentos';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-pills me-2 text-primary"></i>Medicamentos</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Medicamentos</li></ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fa-solid fa-plus me-1"></i>Nuevo</button>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <input type="text" id="filterMeds" class="form-control form-control-sm" placeholder="🔍 Filtrar medicamentos..." style="max-width:320px">
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="tablaMeds">
            <thead><tr><th>Nombre</th><th>Descripción</th><th>Dosis sugerida</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($meds as $m): ?>
              <tr>
                <td><strong><?= e($m['nombre']) ?></strong></td>
                <td class="small text-muted"><?= e(mb_strimwidth($m['descripcion']??'',0,60,'...')) ?></td>
                <td class="small"><?= e($m['dosis_sugerida']??'-') ?></td>
                <td><?= $m['activo']?'<span class="badge bg-success">Activo</span>':'<span class="badge bg-secondary">Inactivo</span>' ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary me-1" onclick="editMed(<?= htmlspecialchars(json_encode($m),ENT_QUOTES) ?>)"><i class="fa-solid fa-pen"></i></button>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar medicamento?"><i class="fa-solid fa-trash"></i></button>
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

<?php foreach ([['modalCrear','create','Nuevo Medicamento'],['modalEditar','update','Editar Medicamento']] as [$mid,$act,$title]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><?= $title ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="<?= $act ?>">
      <?php if($act==='update'): ?><input type="hidden" name="id" id="<?= $mid ?>Id"><?php endif; ?>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="<?= $mid ?>Nombre" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" id="<?= $mid ?>Desc" class="form-control" rows="2"></textarea></div>
        <div class="col-12"><label class="form-label">Dosis sugerida</label><input type="text" name="dosis_sugerida" id="<?= $mid ?>Dosis" class="form-control" placeholder="Ej: 500mg cada 8h"></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="activo" value="1" id="<?= $mid ?>Activo" checked><label class="form-check-label" for="<?= $mid ?>Activo">Activo</label></div></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>
<?php endforeach; ?>

<script>
document.getElementById('filterMeds').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaMeds tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
function editMed(m) {
    document.getElementById('modalEditarId').value    = m.id;
    document.getElementById('modalEditarNombre').value = m.nombre;
    document.getElementById('modalEditarDesc').value   = m.descripcion || '';
    document.getElementById('modalEditarDosis').value  = m.dosis_sugerida || '';
    document.getElementById('modalEditarActivo').checked = m.activo == 1;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
