<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM citas WHERE id=?");
$stmt->execute([$id]);
$cita = $stmt->fetch();
if (!$cita) { setFlash('danger','Cita no encontrada.'); redirect(BASE_URL.'/citas/index.php'); }
if (!isAdmin() && !isMedico() && $cita['usuario_id'] != $_SESSION['user_id']) { redirect(BASE_URL.'/citas/index.php'); }

$pacientes     = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes ORDER BY nombre")->fetchAll();
$especialistas = $pdo->query("SELECT e.id, CONCAT(e.nombre,' ',e.apellido) AS nombre, esp.nombre AS especialidad FROM especialistas e JOIN especialidades esp ON e.especialidad_id=esp.id WHERE e.activo=1 ORDER BY e.nombre")->fetchAll();
$statuses      = $pdo->query("SELECT * FROM status_cita WHERE activo=1 ORDER BY id")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id    = (int)$_POST['paciente_id'];
    $especialista_id = (int)$_POST['especialista_id'];
    $fecha    = $_POST['fecha'] ?? '';
    $motivo   = trim($_POST['motivo'] ?? '');
    $status_id = (int)($_POST['status_id'] ?? 1);

    if (!$paciente_id)     $errors[] = 'Selecciona un paciente válido de la lista.';
    if (!$especialista_id) $errors[] = 'Selecciona un especialista válido de la lista.';
    if (!$fecha)           $errors[] = 'La fecha es obligatoria.';

    if (!$errors) {
        $sNom = $pdo->prepare("SELECT nombre FROM status_cita WHERE id=?"); $sNom->execute([$status_id]);
        $estadoNom = $sNom->fetchColumn() ?: 'Pendiente';
        $upd = $pdo->prepare("UPDATE citas SET paciente_id=?,especialista_id=?,fecha=?,motivo=?,status_id=?,estado=? WHERE id=?");
        $upd->execute([$paciente_id,$especialista_id,$fecha,$motivo,$status_id,$estadoNom,$id]);
        setFlash('success','Cita actualizada.');
        redirect(BASE_URL.'/citas/index.php');
    }
    $cita = array_merge($cita, $_POST);
}

// 🛑 Obtener los nombres iniciales para mostrarlos en los inputs de texto interactivos
$currentPacienteNombre = '';
foreach ($pacientes as $pac) {
    if ($cita['paciente_id'] == $pac['id']) {
        $currentPacienteNombre = $pac['nombre'];
        break;
    }
}

$currentEspNombre = '';
foreach ($especialistas as $esp) {
    if ($cita['especialista_id'] == $esp['id']) {
        $currentEspNombre = $esp['nombre'] . ' — ' . $esp['especialidad'];
        break;
    }
}

$pageTitle = 'Editar Cita';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-pen me-2 text-primary"></i>Editar Cita</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/citas/index.php">Citas</a></li><li class="breadcrumb-item active">Editar</li></ol></nav>
    </div>
  </div>
  <div class="content-area">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="card">
          <div class="card-header"><h5>Editar Cita #<?= $id ?></h5></div>
          <div class="card-body">
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div><?php endif; ?>
            
            <form method="POST" onsubmit="return validarAntesDeEnviar();">
              <div class="row g-3">
                
                <div class="col-12">
                  <label class="form-label">Paciente *</label>
                  <input type="text" 
                         id="pacienteInput" 
                         class="form-control" 
                         list="listaPacientes" 
                         placeholder="Escribe para buscar paciente..." 
                         autocomplete="off"
                         oninput="syncPacienteId()"
                         value="<?= e($currentPacienteNombre) ?>"
                         required>
                  <input type="hidden" name="paciente_id" id="pacienteId" value="<?= (int)$cita['paciente_id'] ?>">
                </div>
                
                <div class="col-12">
                  <label class="form-label">Especialista *</label>
                  <input type="text" 
                         id="especialistaInput" 
                         class="form-control" 
                         list="listaEspecialistas" 
                         placeholder="Escribe para buscar especialista..." 
                         autocomplete="off"
                         oninput="syncEspecialistaId()"
                         value="<?= e($currentEspNombre) ?>"
                         required>
                  <input type="hidden" name="especialista_id" id="selectEsp" value="<?= (int)$cita['especialista_id'] ?>">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Fecha *</label>
                  <input type="date" name="fecha" id="fechaCita" class="form-control" required value="<?= e($cita['fecha']) ?>" onchange="validarHorario()">
                  <div id="horarioMsg" class="mt-1 small"></div>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <select name="status_id" class="form-select">
                    <?php foreach ($statuses as $s): ?>
                      <option value="<?= $s['id'] ?>" <?= ($cita['status_id']==$s['id'])?'selected':'' ?>><?= e($s['nombre']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Motivo</label>
                  <textarea name="motivo" class="form-control" rows="2"><?= e($cita['motivo']??'') ?></textarea>
                </div>
                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Actualizar</button>
                  <a href="<?= BASE_URL ?>/citas/index.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<datalist id="listaPacientes">
  <?php foreach ($pacientes as $pac): ?>
    <option class="opcion-paciente" data-id="<?= $pac['id'] ?>" value="<?= e($pac['nombre']) ?>"></option>
  <?php endforeach; ?>
</datalist>

<datalist id="listaEspecialistas">
  <?php foreach($especialistas as $esp): ?>
    <option class="opcion-especialista" data-id="<?= $esp['id'] ?>" value="<?= e($esp['nombre']) ?> — <?= e($esp['especialidad']) ?>"></option>
  <?php endforeach; ?>
</datalist>

<script>
function syncPacienteId() {
    const input = document.getElementById('pacienteInput');
    const hidden = document.getElementById('pacienteId');
    const options = document.querySelectorAll('.opcion-paciente');
    
    hidden.value = ""; 
    let mostrados = 0;
    const maxVisibles = 10; 
    const filtro = input.value.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {
            if (valorOpcion === filtro) {
                hidden.value = option.getAttribute('data-id');
            }
            if (mostrados < maxVisibles) {
                option.disabled = false; 
                mostrados++;
            } else {
                option.disabled = true; 
            }
        } else {
            option.disabled = true; 
        }
    }
}


function syncEspecialistaId() {
    const input = document.getElementById('especialistaInput');
    const hidden = document.getElementById('selectEsp');
    if (!input) return; 
    
    const options = document.querySelectorAll('.opcion-especialista');
    
    hidden.value = ""; 
    let mostrados = 0;
    const maxVisibles = 10;
    const filtro = input.value.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {            
            if (valorOpcion === filtro) {
                hidden.value = option.getAttribute('data-id');
            }            
            if (mostrados < maxVisibles) {
                option.disabled = false; 
                mostrados++;
            } else {
                option.disabled = true; 
            }
        } else {
            option.disabled = true; 
        }
    }
    validarHorario(); 
}

function validarHorario() {
  const espId = document.getElementById('selectEsp').value;
  const fecha = document.getElementById('fechaCita').value;
  const msg   = document.getElementById('horarioMsg');
  const inputFecha = document.getElementById('fechaCita');
  
  if (!espId || !fecha) { 
    msg.innerHTML = ''; 
    inputFecha.setAttribute('data-valido', 'true');
    return; 
  }
  
  fetch(`<?= BASE_URL ?>/citas/check_horario.php?especialista_id=${espId}&fecha=${fecha}`)
    .then(r => r.json())
    .then(d => {
      if (!d.msg) { 
        msg.innerHTML = ''; 
        inputFecha.setAttribute('data-valido', 'true');
        return; 
      }
      
      msg.innerHTML = `<span class="text-${d.color}">${d.msg}</span>`;
      
      if (d.color === 'danger') {
        inputFecha.setAttribute('data-valido', 'false');
      } else {
        inputFecha.setAttribute('data-valido', 'true');
      }
    });
}

function validarAntesDeEnviar() {
  const pId = document.getElementById('pacienteId').value;
  const eId = document.getElementById('selectEsp').value;
  const inputFecha = document.getElementById('fechaCita');
  const esValido = inputFecha.getAttribute('data-valido');
  
  if (!pId) {
    alert('Por favor, selecciona un paciente válido de la lista.');
    document.getElementById('pacienteInput').focus();
    return false;
  }
  
  if (!eId) {
    alert('Por favor, selecciona un especialista válido de la lista.');
    document.getElementById('especialistaInput').focus();
    return false;
  }
  
  if (esValido === 'false') {
    alert('Por favor, selecciona una fecha u horario disponible antes de guardar.');
    return false; 
  }
  return true; 
}

// Ejecutar validación inicial al cargar la página
document.addEventListener("DOMContentLoaded", function() {
    syncPacienteId();
    syncEspecialistaId();
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>