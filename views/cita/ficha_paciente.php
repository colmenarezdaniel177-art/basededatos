<?php
// views/cita/ficha_paciente.php

// Consultar los pacientes activos para el menú desplegable
$qPac = "SELECT id, nombre, cedula FROM paciente ORDER BY nombre ASC";
$sPac = $db->query($qPac);

$paciente_id = isset($_GET['paciente_id']) ? intval($_GET['paciente_id']) : 0;

$antecedentes = [];
$nombre = "";
$cedula = "";

if ($paciente_id > 0) {
    // Usamos el modelo cargándolo de forma nativa
    require_once "../models/paciente.php";
    $pacienteModel = new PacienteModel($db);
    $stmt = $pacienteModel->consultarAntecedentesPaciente($paciente_id);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nombre = $row['nombre'];
        $cedula = $row['cedula'];
        if (!empty($row['antecedente'])) {
            $antecedentes[] = $row;
        }
    }
}
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-id-card"></i> Ficha de Antecedentes por Paciente</h5>
            <?php if ($paciente_id > 0): ?>
               
            <a href="index.php?controller=cita&action=reporteFichaPaciente&download=pdf&paciente_id=<?= $paciente_id ?>" 
            target="_blank" class="btn btn-light text-info font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar Ficha PDF
            </a>

            <?php endif; ?>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="cita">
                <input type="hidden" name="action" value="reporteFichaPaciente">
                
                <div class="col-md-9">
                    <label class="form-label font-weight-bold">Seleccione Paciente:</label>
                    <select name="paciente_id" class="form-select" required>
                        <option value="">-- Seleccionar de la Lista --</option>
                        <?php while($pac = $sPac->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $pac['id'] ?>" <?= $paciente_id == $pac['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pac['nombre']) ?> (C.I: <?= $pac['cedula'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </form>

            <?php if ($paciente_id > 0): ?>
                <div class="border rounded p-3 bg-light mb-4">
                    <h6 class="font-weight-bold text-secondary mb-3">Resumen de Datos Personales</h6>
                    <p class="mb-1"><strong>Paciente:</strong> <?= htmlspecialchars($nombre) ?></p>
                    <p class="mb-1"><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>Descripción del Antecedente Médico</th>
                                </tr>
                            </thead>
                        <tbody>
                            <?php if (count($antecedentes) > 0): ?>
                                <?php foreach ($antecedentes as $ant): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ant['antecedente']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center text-muted">El paciente seleccionado no posee registros de antecedentes patológicos.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
