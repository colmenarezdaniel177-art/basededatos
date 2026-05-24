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
    $color  = trim($_POST['color'] ?? 'secondary');
    $activo = (int)($_POST['activo'] ?? 1);

    if ($action === 'create' && $nombre) {
        $pdo->prepare("INSERT INTO tipo_antecedente (nombre,color,activo) VALUES (?,?,?)")->execute([$nombre,$color,$activo]);
        setFlash('success','Tipo creado.');
    } elseif ($action === 'update' && $id) {
        $pdo->prepare("UPDATE tipo_antecedente SET nombre=?,color=?,activo=? WHERE id=?")->execute([$nombre,$color,$activo,$id]);
        setFlash('success','Tipo actualizado.');
    } elseif ($action === 'delete' && $id > 5) {
        try {
            $pdo->prepare("DELETE FROM tipo_antecedente WHERE id=?")->execute([$id]);
            setFlash('success','Tipo eliminado.');
        } catch (PDOException $e) {
            setFlash('danger','No se puede eliminar: está siendo usado.');
        }
    }
    redirect(BASE_URL.'/admin/tipo_antecedente.php');
}

$tipos = $pdo->query("SELECT t.*, COUNT(a.id) AS total FROM tipo_antecedente t LEFT JOIN antecedentes a ON a.tipo_id=t.id GROUP BY t.id ORDER BY t.id")->fetchAll();
$colores = ['primary','secondary','success','danger','warning','info','dark'];

$pageTitle = 'Tipos de Antecedente';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-list-check me-2 text-primary"></i>Tipos de Antecedente</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Tipos</li></ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fa-solid fa-plus me-1"></i>Nuevo Tipo</button>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body p-0">
            <table class="table mb-0">
              <thead><tr><th>#</th><th>Nombre</th><th>Color</th><th>Registros</th><th>Acciones</th></tr></thead>
              <tbody>
              <?php foreach ($tipos as $t): ?>
                <tr>
                  <td><?= $t['id'] ?></td>
                  <td><span class="badge bg-<?= e($t['color']) ?> fs-6"><?= e($t['nombre']) ?></span></td>
                  <td><code><?= e($t['color']) ?></code></td>
                  <td><span class="badge bg-secondary"><?= $t['total'] ?></span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editTipo(<?= htmlspecialchars(json_encode($t), ENT_QUOTES) ?>)"><i class="fa-solid fa-pen"></i></button>
                    <?php if ($t['id'] > 5): ?>
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $t['id'] ?>">
                      <button class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar tipo?"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    <?php else: ?>
                    <span class="text-muted small">Base</span>
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

<?php foreach ([['modalCrear','create','Nuevo Tipo'],['modalEditar','update','Editar Tipo']] as [$mid,$act,$title]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><?= $title ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="<?= $act ?>">
      <?php if ($act === 'update'): ?><input type="hidden" name="id" id="<?= $mid ?>Id"><?php endif; ?>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="<?= $mid ?>Nombre" class="form-control" required></div>
        <div class="col-12">
          <label class="form-label">Color</label>
          <select name="color" id="<?= $mid ?>Color" class="form-select">
            <?php foreach ($colores as $c): ?><option value="<?= $c ?>"><?= $c ?></option><?php endforeach; ?>
          </select>
          <div class="mt-2"><span id="<?= $mid ?>Preview" class="badge bg-secondary">Vista previa</span></div>
        </div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="activo" value="1" id="<?= $mid ?>Activo" checked><label class="form-check-label" for="<?= $mid ?>Activo">Activo</label></div></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>
<?php endforeach; ?>

<script>
function editTipo(t) {
  document.getElementById('modalEditarId').value     = t.id;
  document.getElementById('modalEditarNombre').value = t.nombre;
  document.getElementById('modalEditarColor').value  = t.color;
  document.getElementById('modalEditarActivo').checked = t.activo == 1;
  updatePreview('modalEditar', t.nombre, t.color);
  new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
function updatePreview(mid, nombre, color) {
  const prev = document.getElementById(mid+'Preview');
  if(prev){ prev.className='badge bg-'+color; prev.textContent = nombre || 'Vista previa'; }
}
['modalCrear','modalEditar'].forEach(mid => {
  const col = document.getElementById(mid+'Color');
  const nom = document.getElementById(mid+'Nombre');
  if(col) col.addEventListener('change', ()=> updatePreview(mid, nom?.value, col.value));
  if(nom) nom.addEventListener('input',  ()=> updatePreview(mid, nom.value,  col?.value));
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
