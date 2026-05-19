<?php
// views/Estatus_Cita/citas_canceladas.php

// Cargamos el modelo de citas que ya tiene la conexión configurada
require_once dirname(dirname(__DIR__)) . "/models/cita.php";
$citaModel = new CitaModel($db); 

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

// Usamos el método que ya creamos en cita.php para traer las canceladas
$stmt = $citaModel->consultarCitasCanceladas($fecha_inicio, $fecha_fin);
?>


<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-times-circle"></i> Control de Citas Canceladas / Ausencias</h5>
            <a href="index.php?controller=estatus_cita&action=reporteCanceladas&download=pdf&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
                 target="_blank" class="btn btn-light text-secondary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>

        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="estatus_cita">
                <input type="hidden" name="action" value="reporteCanceladas">
                
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Desde:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Hasta:</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-dark w-100"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Especialista</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Nota / Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr class="table-warning">
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['paciente_nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['especialista_nombre']) ?></td>
                                    <td><?= $row['fecha'] ?></td>
                                    <td><span class="badge bg-danger"><?= htmlspecialchars($row['status']) ?></span></td>
                                    <td><?= htmlspecialchars($row['nota']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No se registran inasistencias o cancelaciones en este rango.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
