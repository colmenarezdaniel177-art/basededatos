<?php
// views/especialidad/reporte_ranking.php

// Ejecutamos la consulta gerencial cruzando datos mediante conexión directa PDO
$query = "SELECT esp.nombre AS especialidad_nombre, COUNT(c.id) AS total_solicitudes
          FROM especialidad esp
          INNER JOIN especialista e ON esp.id = e.especialidad_id
          INNER JOIN cita c ON e.id = c.especialista_id
          GROUP BY esp.id, esp.nombre
          ORDER BY total_solicitudes DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$ranking = 1;
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-crown"></i> Dirección: Especialidades Médicas Más Demandadas</h5>
            <a href="index.php?controller=especialidad&action=reporteEspecialidadesGerencial&download=pdf" 
               target="_blank" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar Ranking PDF
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-warning text-dark" role="alert">
                <i class="fas fa-chart-pie"></i> <strong>Indicador de Toma de Decisiones:</strong> Evalúe qué áreas de atención clínica tienen mayor volumen de ocupación para planificar futuras contrataciones de personal médico.
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th width="150">Lugar (Top)</th>
                            <th>Especialidad Médica</th>
                            <th width="250">Total de Solicitudes Recibidas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="font-weight-bold bg-light">
                                        <i class="fas fa-medal text-warning me-1"></i> <?= $ranking ?>°
                                    </td>
                                    <td class="text-start font-weight-bold"><?= htmlspecialchars($row['especialidad_nombre']) ?></td>
                                    <td class="text-danger font-weight-bold fs-5"><?= $row['total_solicitudes'] ?></td>
                                </tr>
                                <?php $ranking++; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No se registra volumen de citas médicas completadas en las especialidades.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
