<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
if (!isAdmin() && !isMedico()) { redirect(BASE_URL . '/home.php'); }

$uid   = $_SESSION['user_id'];
$isAdm = isAdmin();
$isMed = isMedico();

// Para médico: cargar su especialista asignado
$medicoEspecialista = null;
if ($isMed && $_SESSION['especialista_id']) {
    $stmtMe = $pdo->prepare("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM專 especialistas WHERE id=?");
    $stmtMe->execute([$_SESSION['especialista_id']]);
    $medicoEspecialista = $stmtMe->fetch();
}

$citaId = (int)($_GET['cita_id'] ?? 0);
$cita   = null;
$preselPaciente = (int)($_GET['paciente_id'] ?? 0);

if ($citaId) {
    $stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente_nombre, CONCAT(e.nombre,' ',e.apellido) AS especialista_nombre FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id WHERE c.id=?");
    $stmt->execute([$citaId]);
    $cita = $stmt->fetch();
}

if ($isAdm || isMedico()) {
    $pacientes = $pdo->query("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes ORDER BY nombre")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT id, CONCAT(nombre,' ',apellido) AS nombre FROM pacientes WHERE usuario_id=? ORDER BY nombre");
    $stmt->execute([$uid]);
    $pacientes = $stmt->fetchAll();
}

$especialistas = $pdo->query("SELECT e.id, CONCAT(e.nombre,' ',e.apellido) AS nombre FROM especialistas e WHERE e.activo=1 ORDER BY e.nombre")->fetchAll();
$medicamentos  = $pdo->query("SELECT id, nombre, dosis_sugerida FROM medicamentos WHERE activo=1 ORDER BY nombre")->fetchAll();

$errors = [];
$emergenciaMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id    = (int)($_POST['paciente_id'] ?? 0);
    $especialista_id = (int)($_POST['especialista_id'] ?? 0);
    $motivo         = trim($_POST['motivo_consulta'] ?? '');
    $diagnostico    = trim($_POST['diagnostico'] ?? '');
    $tratamiento    = trim($_POST['tratamiento'] ?? '');
    $obs            = trim($_POST['observaciones'] ?? '');
    $cid            = (int)($_POST['cita_id'] ?? 0);

    // Crear paciente de emergencia si se proporcionó y no existe
    if (!$paciente_id && !empty($_POST['em_nombre']) && !empty($_POST['em_apellido'])) {
        $emNombre   = trim($_POST['em_nombre']);
        $emApellido = trim($_POST['em_apellido']);
        $emCedula   = trim($_POST['em_cedula'] ?? '');
        $ins = $pdo->prepare("INSERT INTO pacientes (usuario_id, nombre, apellido, cedula) VALUES (?,?,?,?)");
        $ins->execute([$uid, $emNombre, $emApellido, $emCedula ?: null]);
        $paciente_id = (int)$pdo->lastInsertId();
        $emergenciaMsg = "Paciente '$emNombre $emApellido' creado automáticamente.";
    }

    if (!$paciente_id)    $errors[] = 'Selecciona o registra un paciente.';
    if (!$especialista_id) $errors[] = 'Selecciona un especialista.';

    if (!$errors) {
        $ins = $pdo->prepare("INSERT INTO consultas (cita_id,paciente_id,especialista_id,motivo_consulta,diagnostico,tratamiento,observaciones) VALUES (?,?,?,?,?,?,?)");
        $ins->execute([$cid ?: null, $paciente_id, $especialista_id, $motivo, $diagnostico, $tratamiento, $obs]);
        $consultaId = (int)$pdo->lastInsertId();

        // Medicamentos
        $meds = $_POST['med_id'] ?? [];
        foreach ($meds as $i => $mid) {
            if ($mid) {
                $pdo->prepare("INSERT INTO consulta_medicamentos (consulta_id,medicamento_id,dosis,frecuencia,duracion) VALUES (?,?,?,?,?)")
                    ->execute([$consultaId, $mid, $_POST['med_dosis'][$i]??'', $_POST['med_frecuencia'][$i]??'', $_POST['med_duracion'][$i]??'']);
            }
        }

        // Buscar status "Completada" y marcar la cita
        if ($cid) {
            $sc = $pdo->query("SELECT id FROM status_cita WHERE nombre='Completada' LIMIT 1")->fetchColumn();
            $pdo->prepare("UPDATE citas SET status_id=?, estado='Completada' WHERE id=?")->execute([$sc ?: null, $cid]);
        }

        if ($emergenciaMsg) setFlash('info', $emergenciaMsg);
        setFlash('success', 'Consulta registrada correctamente.');
        redirect(BASE_URL.'/consultas/view.php?id='.$consultaId);
    }
}

// Antecedentes previos del paciente
$antecedentesConsulta = [];
$pidAnte = $cita['paciente_id'] ?? $preselPaciente ?? 0;
if ($pidAnte) {
    $ants = $pdo->prepare("SELECT a.*, t.nombre AS tipo_nombre, t.color AS tipo_color FROM antecedentes a LEFT JOIN tipo_antecedente t ON a.tipo_id=t.id WHERE a.paciente_id=? ORDER BY t.nombre");
    $ants->execute([$pidAnte]);
    $antecedentesConsulta = $ants->fetchAll();
}

$isEmergencia = !$citaId; // No viene de una cita → posible emergencia

$pageTitle = 'Nueva Consulta';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar.php';
?>
<div class="main-content">
  <div class="topbar">
    <div>
      <h4><i class="fa-solid fa-notes-medical me-2 text-primary"></i>Registrar Consulta</h4>
      <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/consultas/index.php">Consultas</a></li><li class="breadcrumb-item active">Nueva</li></ol></nav>
    </div>
    <?php if ($isEmergencia): ?><span class="badge bg-danger fs-6"><i class="fa-solid fa-triangle-exclamation me-1"></i>Consulta libre / Emergencia</span><?php endif; ?>
  </div>
  <div class="content-area">
    <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul></div><?php endif; ?>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><h5>Datos de la Consulta</h5></div>
          <div class="card-body">
            <form method="POST" id="formConsulta" onsubmit="return validarFormularioConsulta();">
              <input type="hidden" name="cita_id" value="<?= $citaId ?>">
              <div class="row g-3">

                <div class="col-md-6">
                  <label class="form-label">Paciente</label>
                  <?php if ($cita): ?>
                    <input type="hidden" name="paciente_id" value="<?= $cita['paciente_id'] ?>">
                    <input type="text" class="form-control" value="<?= e($cita['paciente_nombre']) ?>" readonly>
                  <?php else: ?>
                    <input type="text" 
                           id="selectPacienteInput" 
                           class="form-control text-start" 
                           list="listaPacientes" 
                           placeholder="Buscar paciente..." 
                           autocomplete="off"
                           oninput="syncPacienteId(this.value)">
                    <input type="hidden" name="paciente_id" id="selectPaciente" value="<?= $preselPaciente ?: '' ?>">
                  <?php endif; ?>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Especialista *</label>
                  <?php if ($cita): ?>
                    <input type="hidden" name="especialista_id" value="<?= $cita['especialista_id'] ?>">
                    <input type="text" class="form-control" value="<?= e($cita['especialista_nombre']) ?>" readonly>
                  <?php elseif ($medicoEspecialista): ?>
                    <input type="hidden" name="especialista_id" value="<?= $medicoEspecialista['id'] ?>">
                    <input type="text" class="form-control" value="<?= e($medicoEspecialista['nombre']) ?>" readonly>
                  <?php else: ?>
                    <!-- NVO: Reemplazado selector nativo por input autocompletable con datalist -->
                    <input type="text" 
                           id="selectEspecialistaInput" 
                           class="form-control text-start" 
                           list="listaEspecialistas" 
                           placeholder="Buscar especialista..." 
                           autocomplete="off"
                           oninput="syncEspecialistaId(this.value)">
                    <input type="hidden" name="especialista_id" id="selectEspecialista">
                  <?php endif; ?>
                </div>

                <?php if ($isEmergencia): ?>
                <div class="col-12">
                  <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                      <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="chkEmergencia" onchange="toggleEmergencia(this.checked)">
                        <label class="form-check-label fw-bold" for="chkEmergencia">
                          <i class="fa-solid fa-user-plus me-1"></i>Paciente no registrado — registrar en este momento
                        </label>
                      </div>
                    </div>
                    <div class="card-body d-none" id="panelEmergencia">
                      <div class="row g-2">
                        <div class="col-md-4"><label class="form-label">Nombre *</label><input type="text" name="em_nombre" id="em_nombre" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label">Apellido *</label><input type="text" name="em_apellido" id="em_apellido" class="form-control form-control-sm"></div>
                        <div class="col-md-4"><label class="form-label">Cédula</label><input type="text" name="em_cedula" class="form-control form-control-sm"></div>
                      </div>
                      <small class="text-muted">Se creará el paciente automáticamente al guardar la consulta.</small>
                    </div>
                  </div>
                </div>
                <?php endif; ?>

                <div class="col-12">
                  <label class="form-label">Motivo de consulta</label>
                  <textarea name="motivo_consulta" class="form-control" rows="2"><?= e($cita['motivo'] ?? $_POST['motivo_consulta'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Diagnóstico</label>
                  <textarea name="diagnostico" class="form-control" rows="3"><?= e($_POST['diagnostico'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Tratamiento</label>
                  <textarea name="tratamiento" class="form-control" rows="2"><?= e($_POST['tratamiento'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Observaciones</label>
                  <textarea name="observaciones" class="form-control" rows="2"><?= e($_POST['observaciones'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                  <label class="form-label fw-bold">Medicamentos recetados</label>
                  <div id="meds-container">
                    <div class="row g-2 mb-2 med-row align-items-center">
                      <div class="col-md-4">
                        <input type="text" 
                               class="form-control form-control-sm med-input" 
                               list="listaMedicamentos" 
                               placeholder="Buscar medicamento..." 
                               autocomplete="off"
                               oninput="syncMedId(this)">
                        <input type="hidden" name="med_id[]" class="med-hidden">
                      </div>
                      <div class="col-md-3"><input type="text" name="med_dosis[]" class="form-control form-control-sm" placeholder="Dosis"></div>
                      <div class="col-md-2"><input type="text" name="med_frecuencia[]" class="form-control form-control-sm" placeholder="Freq."></div>
                      <div class="col-md-2"><input type="text" name="med_duracion[]" class="form-control form-control-sm" placeholder="Dur."></div>
                      <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-med"><i class="fa-solid fa-times"></i></button></div>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addMed()"><i class="fa-solid fa-plus me-1"></i>Agregar otro medicamento</button>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                  <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-2"></i>Guardar Consulta</button>
                  <a href="<?= BASE_URL ?>/consultas/index.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card mb-3">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa-solid fa-clipboard-list me-2 text-warning"></i>Antecedentes</h5>
            <?php $pidPanel = $cita['paciente_id'] ?? $preselPaciente ?? 0; ?>
            <?php if ($pidPanel): ?>
              <a href="<?= BASE_URL ?>/pacientes/antecedente_add.php?paciente_id=<?= $pidPanel ?>" class="btn btn-sm btn-outline-warning" title="Agregar antecedente"><i class="fa-solid fa-plus"></i></a>
            <?php endif; ?>
          </div>
          <div class="card-body" id="antecedentes-panel">
            <?php if ($antecedentesConsulta): ?>
              <?php foreach ($antecedentesConsulta as $ant): ?>
                <div class="antecedente-card border-<?= e($ant['tipo_color']??'secondary') ?> mb-2">
                  <span class="badge bg-<?= e($ant['tipo_color']??'secondary') ?> mb-1"><?= e($ant['tipo_nombre']??'-') ?></span>
                  <div class="small"><?= e($ant['descripcion']) ?></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-muted small text-center py-2">Selecciona un paciente para ver sus antecedentes</p>
            <?php endif; ?>
          </div>
        </div>

        <?php $tiposAnte = $pdo->query("SELECT * FROM tipo_antecedente WHERE activo=1 ORDER BY nombre")->fetchAll(); ?>
        <?php if ($isEmergencia && !$pidPanel): ?>
        <div class="card border-warning">
          <div class="card-body text-center py-3">
            <i class="fa-solid fa-triangle-exclamation fa-2x text-warning mb-2"></i>
            <p class="small text-muted mb-0">Guarda la consulta primero.<br>Los antecedentes pueden agregarse al historial del paciente luego.</p>
          </div>
        </div>
        <?php else: ?>
        <div class="card">
          <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-plus me-1 text-warning"></i>Nuevo antecedente</h6></div>
          <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>/pacientes/antecedente_add.php" id="formAnte" onsubmit="return validarAntecedenteAntesDeEnviar();">            
              <input type="hidden" name="paciente_id" id="antePackId" value="<?= $pidPanel ?>">
              <input type="hidden" name="redirect_url" value="<?= BASE_URL ?>/consultas/add.php<?= $citaId ? '?cita_id='.$citaId : '' ?>">
              <div class="mb-2">
               <input type="text" 
                       id="tipoAntecedenteInput" 
                       class="form-control form-control-sm" 
                       list="listaTiposAntecedentes" 
                       placeholder="Buscar tipo de antecedente..." 
                       autocomplete="off"
                       oninput="syncTipoAntecedenteId()"
                       required>
                <input type="hidden" name="tipo_id" id="tipoAntecedenteId">
              </div>
              <div class="mb-2">
                <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Descripción *" required></textarea>
              </div>
              <div class="mb-2">
                <input type="date" name="fecha" class="form-control form-control-sm">
              </div>
              <button type="submit" class="btn btn-warning btn-sm w-100 text-white">
                <i class="fa-solid fa-save me-1"></i>Guardar antecedente
              </button>
            </form>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<datalist id="listaPacientes">
  <?php foreach($pacientes as $pac): ?>
    <option class="opcion-paciente" data-id="<?= $pac['id'] ?>" value="<?= e($pac['nombre']) ?>"></option>
  <?php endforeach; ?>
</datalist>

<datalist id="listaEspecialistas">
  <?php foreach($especialistas as $esp): ?>
    <option class="opcion-especialista" data-id="<?= $esp['id'] ?>" value="<?= e($esp['nombre']) ?>"></option>
  <?php endforeach; ?>
</datalist>
<datalist id="listaMedicamentos">
  <?php foreach($medicamentos as $m): ?>
    <option class="opcion-medicamento" data-id="<?= $m['id'] ?>" value="<?= e($m['nombre']) ?>"></option>
  <?php endforeach; ?>
</datalist>

<datalist id="listaTiposAntecedentes">
  <?php foreach ($tiposAnte as $t): ?>
    <option class="opcion-antecedente" data-id="<?= $t['id'] ?>" value="<?= e($t['nombre']) ?>"></option>
  <?php endforeach; ?>
</datalist>

<script>
function toggleEmergencia(checked) {
  const panel = document.getElementById('panelEmergencia');
  const selPacInput = document.getElementById('selectPacienteInput');
  const selPacHidden = document.getElementById('selectPaciente');
  const emNombre = document.getElementById('em_nombre');
  const emApellido = document.getElementById('em_apellido');

  if(panel) {
    panel.classList.toggle('d-none', !checked);
    if(selPacInput) {
        selPacInput.disabled = checked;
        if(checked) {
            selPacInput.value = '';
            selPacHidden.value = '';
            emNombre.required = true;
            emApellido.required = true;
            loadAntecedentes('');
        } else {
            emNombre.required = false;
            emApellido.required = false;
        }
    }
  }
}

function syncPacienteId(valorInput) {
    const hiddenInput = document.getElementById('selectPaciente');
    const options = document.querySelectorAll('.opcion-paciente');
    if(!hiddenInput) return;

    hiddenInput.value = "";
    let mostrados = 0;
    const maxVisibles = 10;
    const filtro = valorInput.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {
            if (valorOpcion === filtro) {
                hiddenInput.value = option.getAttribute('data-id');
                loadAntecedentes(hiddenInput.value); // Dispara la carga de antecedentes
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
    if(filtro === "") loadAntecedentes('');
}
function syncEspecialistaId(valorInput) {
    const hiddenInput = document.getElementById('selectEspecialista');
    const options = document.querySelectorAll('.opcion-especialista');
    if(!hiddenInput) return;

    hiddenInput.value = "";
    let mostrados = 0;
    const maxVisibles = 10;
    const filtro = valorInput.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {
            if (valorOpcion === filtro) {
                hiddenInput.value = option.getAttribute('data-id');
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

function syncMedId(inputElement) {
    const parent = inputElement.closest('.med-row');
    const hiddenInput = parent.querySelector('.med-hidden');
    const options = document.querySelectorAll('.opcion-medicamento');
    
    hiddenInput.value = ""; 
    let mostrados = 0;
    const maxVisibles = 10;
    const filtro = inputElement.value.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {
            if (valorOpcion === filtro) {
                hiddenInput.value = option.getAttribute('data-id');
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
const medRowHtml = `<div class="row g-2 mb-2 med-row align-items-center">
  <div class="col-md-4">
    <input type="text" 
           class="form-control form-control-sm med-input" 
           list="listaMedicamentos" 
           placeholder="Buscar medicamento..." 
           autocomplete="off"
           oninput="syncMedId(this)">
    <input type="hidden" name="med_id[]" class="med-hidden">
  </div>
  <div class="col-md-3"><input type="text" name="med_dosis[]" class="form-control form-control-sm" placeholder="Dosis"></div>
  <div class="col-md-2"><input type="text" name="med_frecuencia[]" class="form-control form-control-sm" placeholder="Freq."></div>
  <div class="col-md-2"><input type="text" name="med_duracion[]" class="form-control form-control-sm" placeholder="Dur."></div>
  <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger remove-med"><i class="fa-solid fa-times"></i></button></div>
</div>`;

function addMed() { 
    document.getElementById('meds-container').insertAdjacentHTML('beforeend', medRowHtml); 
    const inputs = document.querySelectorAll('.med-input');
    if(inputs.length > 0) syncMedId(inputs[inputs.length - 1]);
}

document.getElementById('meds-container').addEventListener('click', e => {
  if (e.target.closest('.remove-med')) e.target.closest('.med-row').remove();
});

function validarFormularioConsulta() {
    const chkEmergencia = document.getElementById('chkEmergencia');
    const pacienteInput = document.getElementById('selectPacienteInput');
    if (pacienteInput && (!chkEmergencia || !chkEmergencia.checked)) {
        const txtPac = pacienteInput.value.trim();
        const idPac = document.getElementById('selectPaciente').value;
        if (txtPac !== "" && idPac === "") {
            alert(`El paciente "${txtPac}" no es válido. Por favor, selecciónalo de la lista.`);
            pacienteInput.focus();
            return false;
        }
    }
    const espInput = document.getElementById('selectEspecialistaInput');
    if (espInput) {
        const txtEsp = espInput.value.trim();
        const idEsp = document.getElementById('selectEspecialista').value;
        if (txtEsp !== "" && idEsp === "") {
            alert(`El especialista "${txtEsp}" no es válido. Por favor, selecciónalo de la lista.`);
            espInput.focus();
            return false;
        }
    }
    const rows = document.querySelectorAll('.med-row');
    for (const row of rows) {
        const textVal = row.querySelector('.med-input').value.trim();
        const hiddenVal = row.querySelector('.med-hidden').value;
        if (textVal !== "" && hiddenVal === "") {
            alert(`El medicamento "${textVal}" no es válido. Por favor, selecciónalo de la lista.`);
            row.querySelector('.med-input').focus();
            return false;
        }
    }
    return true;
}

function loadAntecedentes(pid) {
  const panel = document.getElementById('antecedentes-panel');
  if (!pid) { panel.innerHTML='<p class="text-muted small text-center py-2">Selecciona un paciente</p>'; return; }
  fetch('<?= BASE_URL ?>/consultas/get_antecedentes.php?paciente_id=' + pid)
    .then(r=>r.text()).then(html=>{ panel.innerHTML=html; });
}

function syncTipoAntecedenteId() {
    const input = document.getElementById('tipoAntecedenteInput');
    const hidden = document.getElementById('tipoAntecedenteId');
    if(!input) return;
    
    const options = document.querySelectorAll('.opcion-antecedente');
    hidden.value = ""; 
    let mostrados = 0;
    const maxVisibles = 10;
    const filtro = input.value.toLowerCase();

    for (const option of options) {
        const valorOpcion = option.value.toLowerCase();
        if (valorOpcion.includes(filtro)) {
            if (option.value.toLowerCase() === filtro) {
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

function validarAntecedenteAntesDeEnviar() {
    const textVal = document.getElementById('tipoAntecedenteInput').value.trim();
    const hiddenVal = document.getElementById('tipoAntecedenteId').value;
    
    if (textVal !== "" && hiddenVal === "") {
        alert(`El tipo de antecedente "${textVal}" no es válido. Por favor, selecciónalo de la lista autocompletable.`);
        document.getElementById('tipoAntecedenteInput').focus();
        return false; // Bloquea el envío
    }
    return true; // Continúa con el envío de formulario
}

document.addEventListener("DOMContentLoaded", () => {
    const pInput = document.getElementById('selectPacienteInput');
    if(pInput) syncPacienteId(pInput.value);

    const eInput = document.getElementById('selectEspecialistaInput');
    if(eInput) syncEspecialistaId(eInput.value);

    const primerMedInput = document.querySelector('.med-input');
    if(primerMedInput) syncMedId(primerMedInput);

    syncTipoAntecedenteId();
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>