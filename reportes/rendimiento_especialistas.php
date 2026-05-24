<?php
// views/cita/rendimiento_especialistas.php


$especialistaModel = new EspecialistaModel($db);

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

$stmt = $especialistaModel->consultarRendimientoEspecialistas($fecha_inicio, $fecha_fin);
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning text-dark d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-chart-bar"></i> Rendimiento de Especialistas (Supervisión)</h5>
            
                <a href="index.php?controller=especialista&action=reporteRendimiento&download=pdf&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
                target="_blank" class="btn btn-dark font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>

        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="especialista">
                <input type="hidden" name="action" value="reporteRendimiento">
                
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Desde:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Hasta:</label>
                    <input type="date" name="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sync"></i> Filtrar Grilla</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Médico</th>
                            <th>Nombre del Especialista</th>
                            <th>Total Citas del Período</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td class="font-weight-bold text-center"><?= $row['total_citas'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
