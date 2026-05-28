<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$roles        = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$especialistas = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM especialistas WHERE activo=1 ORDER BY nombre")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action         = $_POST['action'] ?? '';
    $uid            = (int)($_POST['uid'] ?? 0);
    $nombre         = trim($_POST['nombre'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $rol_id         = (int)($_POST['rol_id'] ?? 2);
    $activo         = isset($_POST['activo']) ? 1 : 0;
    $password       = $_POST['password'] ?? '';
    $especialista_id = (int)($_POST['especialista_id'] ?? 0) ?: null;

    if ($action === 'create') {
        $hash = password_hash($password ?: 'changeme', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO usuarios (nombre,email,password,rol_id,activo,especialista_id) VALUES (?,?,?,?,?,?)")
            ->execute([$nombre,$email,$hash,$rol_id,$activo,$especialista_id]);
        setFlash('success','Usuario creado.');
    } elseif ($action === 'update') {
        if ($uid == $_SESSION['user_id']) {
            setFlash('danger','No puedes modificar tu propio usuario desde este panel de administración.');
        } elseif ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE usuarios SET nombre=?,email=?,rol_id=?,activo=?,password=?,especialista_id=? WHERE id=?")
                ->execute([$nombre,$email,$rol_id,$activo,$hash,$especialista_id,$uid]);
        } else {
            $pdo->prepare("UPDATE usuarios SET nombre=?,email=?,rol_id=?,activo=?,especialista_id=? WHERE id=?")
                ->execute([$nombre,$email,$rol_id,$activo,$especialista_id,$uid]);
        }
        setFlash('success','Usuario actualizado.');
    } elseif ($action === 'delete') {
        if ($uid != $_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM usuarios WHERE id=?")->execute([$uid]);
            setFlash('success','Usuario eliminado.');
        } else {
            setFlash('danger','No puedes eliminar tu propia cuenta.');
        }
    }
    redirect(BASE_URL.'/admin/usuarios.php');
}

$usuarios = $pdo->query(
    "SELECT u.*, r.nombre AS rol_nombre, r.es_sistema,
            CONCAT(e.nombre,' ',e.apellido) AS especialista_nombre
     FROM usuarios u
     JOIN roles r ON u.rol_id=r.id
     LEFT JOIN especialistas e ON u.especialista_id=e.id
     ORDER BY u.created_at DESC"
)->fetchAll();

$pageTitle = 'Usuarios';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-user-shield me-2 text-primary"></i>Usuarios</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Usuarios</li></ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
      <i class="fa-solid fa-plus me-1"></i>Nuevo Usuario
    </button>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <input type="text" id="filterUsuarios" class="form-control form-control-sm" placeholder="🔍 Filtrar usuarios..." style="max-width:320px">
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="tablaUsuarios">
            <thead><tr><th>#</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Especialista asignado</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td class="text-muted small"><?= $u['id'] ?></td>
                <td><strong><?= e($u['nombre']) ?></strong></td>
                <td><?= e($u['email']) ?></td>
                <td>
                  <span class="badge bg-<?= $u['rol_nombre']==='admin'?'danger':($u['rol_nombre']==='medico'?'info':'secondary') ?>">
                    <?= e($u['rol_nombre']) ?>
                  </span>
                  <?php if ($u['es_sistema']): ?><span class="badge bg-light text-muted ms-1" title="Rol de sistema"><i class="fa-solid fa-lock fa-xs"></i></span><?php endif; ?>
                </td>
                <td class="small text-muted"><?= e($u['especialista_nombre'] ?? '—') ?></td>
                <td><?= $u['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                <td>
                  <?php if ($u['id'] != $_SESSION['user_id']): ?>
                  <button class="btn btn-sm btn-outline-primary me-1"
                    onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)"
                    title="Editar"><i class="fa-solid fa-pen"></i></button>
                  
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar usuario <?= e($u['nombre']) ?>?"><i class="fa-solid fa-trash"></i></button>
                  </form>
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

<!-- Modal Crear -->
<div class="modal fade" id="modalCrear" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Nuevo Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST">
      <input type="hidden" name="action" value="create">
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre *</label><input type="text" name="nombre" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6">
          <label class="form-label">Contraseña</label>
          <input type="password" name="password" class="form-control" placeholder="(default: changeme)">
        </div>
        <div class="col-md-6">
          <label class="form-label">Rol</label>
          <select name="rol_id" class="form-select" id="rolCrear" onchange="toggleEspSelect('crearEspWrap', this.value)">
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>"><?= e($r['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-none" id="crearEspWrap">
          <label class="form-label"><i class="fa-solid fa-user-doctor me-1 text-info"></i>Especialista asignado (requerido para médico)</label>
          <select name="especialista_id" class="form-select">
            <option value="">Sin asignar</option>
            <?php foreach ($especialistas as $e): ?>
              <option value="<?= $e['id'] ?>"><?= e($e['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <div class="form-check"><input class="form-check-input" type="checkbox" name="activo" value="1" id="chkActiCrear" checked><label class="form-check-label" for="chkActiCrear">Activo</label></div>
        </div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Editar Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="uid" id="editUid">
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="editNombre" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Email *</label><input type="email" name="email" id="editEmail" class="form-control" required></div>
        <div class="col-md-6">
          <label class="form-label">Nueva Contraseña</label>
          <input type="password" name="password" class="form-control" placeholder="(dejar vacío = sin cambio)">
        </div>
        <div class="col-md-6">
          <label class="form-label">Rol</label>
          <select name="rol_id" id="editRol" class="form-select" onchange="toggleEspSelect('editEspWrap', this.value)">
            <?php foreach ($roles as $r): ?>
              <option value="<?= $r['id'] ?>"><?= e($r['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-none" id="editEspWrap">
          <label class="form-label"><i class="fa-solid fa-user-doctor me-1 text-info"></i>Especialista asignado</label>
          <select name="especialista_id" id="editEspId" class="form-select">
            <option value="">Sin asignar</option>
            <?php foreach ($especialistas as $e): ?>
              <option value="<?= $e['id'] ?>"><?= e($e['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <div class="form-check"><input class="form-check-input" type="checkbox" name="activo" value="1" id="editActivo"><label class="form-check-label" for="editActivo">Activo</label></div>
        </div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Actualizar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>

<script>
// Filtro client-side
document.getElementById('filterUsuarios').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaUsuarios tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Mostrar/ocultar selector de especialista según rol médico
const medicoRolId = '<?= array_values(array_filter($roles, fn($r) => $r['nombre']==='medico'))[0]['id'] ?? 3 ?>';
function toggleEspSelect(wrapId, rolId) {
    const wrap = document.getElementById(wrapId);
    wrap.classList.toggle('d-none', String(rolId) !== String(medicoRolId));
}

// Limpiar modal crear al abrirlo
document.getElementById('modalCrear').addEventListener('show.bs.modal', function() {
    this.querySelector('form').reset();
    toggleEspSelect('crearEspWrap', document.getElementById('rolCrear').value);
});

function editUser(u) {
    document.getElementById('editUid').value     = u.id;
    document.getElementById('editNombre').value  = u.nombre;
    document.getElementById('editEmail').value   = u.email;
    document.getElementById('editRol').value     = u.rol_id;
    document.getElementById('editEspId').value   = u.especialista_id || '';
    document.getElementById('editActivo').checked = u.activo == 1;
    toggleEspSelect('editEspWrap', u.rol_id);
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
