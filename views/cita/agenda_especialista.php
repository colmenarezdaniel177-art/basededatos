<?php
// views/cita/agenda_especialista.php
$citaModel = new CitaModel($db);

// Consultar los especialistas para el SELECT
$qEsp = "SELECT id, nombre FROM especialista ORDER BY nombre ASC";
$sEsp = $db->query($qEsp);

$especialista_id = isset($_GET['especialista_id']) ? intval($_GET['especialista_id']) : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

$stmt = $citaModel->consultarAgendaPorEspecialista($especialista_id, $fecha_inicio, $fecha_fin);
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt"></i> Agenda de Citas por Especialista</h5>
            
            <a href="index.php?controller=cita&action=reporteAgenda&download=pdf&especialista_id=<?= $especialista_id ?>&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
            target="_blank" class="btn btn-light text-success font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar Agenda PDF
            </a>

        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="cita">
                <input type="hidden" name="action" value="reporteAgenda">
                
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Seleccione Especialista:</label>
                    <select name="especialista_id" class="form-select" required>
                        <option value="">-- Seleccionar Médico --</option>
                        <?php while($esp = $sEsp->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?= $esp['id'] ?>" <?= $especialista_id == $esp['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($esp['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Desde:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Hasta:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Cita</th>
                            <th>Paciente</th>
                            <th>Fecha Asignada</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['paciente_nombre']) ?></td>
                                    <td><?= $row['fecha'] ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['status']) ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No existen citas registradas para este especialista en las fechas seleccionadas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
