<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$especialidades = $pdo->query("SELECT * FROM especialidades ORDER BY nombre")->fetchAll();
$diasSemana = ['0'=>'Domingo','1'=>'Lunes','2'=>'Martes','3'=>'Miércoles','4'=>'Jueves','5'=>'Viernes','6'=>'Sábado'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'create') {
        $pdo->prepare("INSERT INTO especialistas (nombre,apellido,especialidad_id,telefono,email,activo) VALUES (?,?,?,?,?,?)")
            ->execute([trim($_POST['nombre']),trim($_POST['apellido']),(int)$_POST['especialidad_id'],trim($_POST['telefono']??''),trim($_POST['email']??''),(int)($_POST['activo']??1)]);
        setFlash('success','Especialista creado.');
    } elseif ($action === 'update') {
        $pdo->prepare("UPDATE especialistas SET nombre=?,apellido=?,especialidad_id=?,telefono=?,email=?,activo=? WHERE id=?")
            ->execute([trim($_POST['nombre']),trim($_POST['apellido']),(int)$_POST['especialidad_id'],trim($_POST['telefono']??''),trim($_POST['email']??''),(int)($_POST['activo']??1),$id]);
        setFlash('success','Especialista actualizado.');
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM especialistas WHERE id=?")->execute([$id]);
        setFlash('success','Especialista eliminado.');
    } elseif ($action === 'save_horarios' && $id) {
        $pdo->prepare("DELETE FROM horarios_especialista WHERE especialistaId=?")->execute([$id]);
        $ins = $pdo->prepare("INSERT INTO horarios_especialista (especialistaId,dia_semana,hora_inicio,hora_fin) VALUES (?,?,?,?)");
        $dias = $_POST['dia_semana'] ?? [];
        foreach ($dias as $k => $dia) {
            $hi = $_POST['hora_inicio'][$k] ?? '';
            $hf = $_POST['hora_fin'][$k] ?? '';
            if ($dia !== '' && $hi && $hf) $ins->execute([$id,$dia,$hi,$hf]);
        }
        setFlash('success','Horarios guardados.');
    }
    redirect(BASE_URL.'/admin/especialistas.php');
}

$especialistas = $pdo->query(
    "SELECT e.*, esp.nombre AS especialidad
     FROM especialistas e LEFT JOIN especialidades esp ON e.especialidad_id=esp.id
     ORDER BY e.nombre"
)->fetchAll();

$pageTitle = 'Especialistas';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-user-doctor me-2 text-primary"></i>Especialistas</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/dashboard.php">Inicio</a></li><li class="breadcrumb-item active">Especialistas</li></ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fa-solid fa-plus me-1"></i>Nuevo</button>
  </div>
  <div class="content-area">
    <?php showFlash() ?>
    <div class="card">
      <div class="card-header">
        <input type="text" id="filterEsp" class="form-control form-control-sm" placeholder="🔍 Filtrar especialistas..." style="max-width:320px">
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="tablaEsp">
            <thead><tr><th>Nombre</th><th>Especialidad</th><th>Teléfono</th><th>Email</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($especialistas as $e): ?>
              <tr>
                <td><strong><?= e($e['nombre'].' '.$e['apellido']) ?></strong></td>
                <td><span class="badge bg-info text-dark"><?= e($e['especialidad']??'-') ?></span></td>
                <td><?= e($e['telefono']??'-') ?></td>
                <td><?= e($e['email']??'-') ?></td>
                <td><?= $e['activo']?'<span class="badge bg-success">Activo</span>':'<span class="badge bg-secondary">Inactivo</span>' ?></td>
                <td class="d-flex gap-1">
                  <button class="btn btn-sm btn-outline-primary" onclick="editEsp(<?= htmlspecialchars(json_encode($e),ENT_QUOTES) ?>)" title="Editar"><i class="fa-solid fa-pen"></i></button>
                  <button class="btn btn-sm btn-outline-info" onclick="verHorarios(<?= $e['id'] ?>,'<?= e($e['nombre'].' '.$e['apellido']) ?>')" title="Horarios"><i class="fa-solid fa-clock"></i></button>
                  <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $e['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar especialista?"><i class="fa-solid fa-trash"></i></button>
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

<!-- Modales Crear / Editar -->
<?php foreach ([['modalCrear','create','Nuevo Especialista'],['modalEditar','update','Editar Especialista']] as [$mid,$act,$title]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><?= $title ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><input type="hidden" name="action" value="<?= $act ?>">
      <?php if($act==='update'): ?><input type="hidden" name="id" id="<?= $mid ?>Id"><?php endif; ?>
      <div class="modal-body row g-3">
        <div class="col-md-6"><label class="form-label">Nombre *</label><input type="text" name="nombre" id="<?= $mid ?>Nombre" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Apellido *</label><input type="text" name="apellido" id="<?= $mid ?>Apellido" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Especialidad</label>
          <select name="especialidad_id" id="<?= $mid ?>Esp" class="form-select">
            <option value="">Sin especialidad</option>
            <?php foreach($especialidades as $esp): ?><option value="<?= $esp['id'] ?>"><?= e($esp['nombre']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" name="telefono" id="<?= $mid ?>Tel" class="form-control"></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" id="<?= $mid ?>Email" class="form-control"></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="activo" value="1" id="<?= $mid ?>Activo" checked><label class="form-check-label" for="<?= $mid ?>Activo">Activo</label></div></div>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>
<?php endforeach; ?>

<!-- Modal Horarios -->
<div class="modal fade" id="modalHorarios" tabindex="-1">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="horariosTitulo">Horarios</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" id="formHorarios">
      <input type="hidden" name="action" value="save_horarios">
      <input type="hidden" name="id" id="horariosEspId">
      <div class="modal-body">
        <p class="text-muted small mb-3">Define los días y horas de atención.</p>
        <div id="horariosContainer"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addHorarioRow()"><i class="fa-solid fa-plus me-1"></i>Agregar día</button>
      </div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Guardar horarios</button><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button></div>
    </form>
  </div></div>
</div>

<script>
document.getElementById('filterEsp').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaEsp tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

const dias = <?= json_encode($diasSemana) ?>;

function editEsp(e) {
    document.getElementById('modalEditarId').value       = e.id;
    document.getElementById('modalEditarNombre').value   = e.nombre;
    document.getElementById('modalEditarApellido').value = e.apellido;
    document.getElementById('modalEditarEsp').value      = e.especialidad_id || '';
    document.getElementById('modalEditarTel').value      = e.telefono || '';
    document.getElementById('modalEditarEmail').value    = e.email || '';
    document.getElementById('modalEditarActivo').checked = e.activo == 1;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

function verHorarios(espId, nombre) {
    document.getElementById('horariosEspId').value = espId;
    document.getElementById('horariosTitulo').textContent = 'Horarios — ' + nombre;
    document.getElementById('horariosContainer').innerHTML = '<p class="text-muted">Cargando...</p>';
    new bootstrap.Modal(document.getElementById('modalHorarios')).show();
    fetch('<?= BASE_URL ?>/admin/get_horarios.php?id=' + espId)
        .then(r=>r.json())
        .then(data => {
            const c = document.getElementById('horariosContainer');
            c.innerHTML = '';
            if (data.length===0) addHorarioRow();
            else data.forEach(h => addHorarioRow(h.dia_semana, h.hora_inicio, h.hora_fin));
        });
}

function diaOptions(selected='') {
    let opts = '<option value="">Día...</option>';
    for (const [v,l] of Object.entries(dias)) opts += `<option value="${v}" ${v===String(selected)?'selected':''}>${l}</option>`;
    return opts;
}
function addHorarioRow(dia='',hi='',hf='') {
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 horario-row';
    row.innerHTML = `
      <div class="col-md-4"><select name="dia_semana[]" class="form-select form-select-sm">${diaOptions(dia)}</select></div>
      <div class="col-md-3"><input type="time" name="hora_inicio[]" class="form-control form-control-sm" value="${hi}" placeholder="Inicio"></div>
      <div class="col-md-3"><input type="time" name="hora_fin[]" class="form-control form-control-sm" value="${hf}" placeholder="Fin"></div>
      <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.horario-row').remove()"><i class="fa-solid fa-times"></i></button></div>`;
    document.getElementById('horariosContainer').appendChild(row);
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
