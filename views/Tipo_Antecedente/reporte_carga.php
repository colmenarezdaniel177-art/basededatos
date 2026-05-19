<?php
// views/Tipo_Antecedente/reporte_carga.php

// Ejecutamos la consulta de supervisión directamente usando la conexión de la base de datos ($db)
$query = "SELECT ta.id, ta.nombre AS antecedente_nombre, COUNT(DISTINCT am.paciente_id) AS total_pacientes
          FROM tipo_antecedente ta
          LEFT JOIN antecedentes_medicos am ON ta.id = am.tipo_antecedente_id
          GROUP BY ta.id, ta.nombre
          ORDER BY total_pacientes DESC";

$stmt = $db->prepare($query);
$stmt->execute();
?>


<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-microscope"></i> Supervisión: Carga Clínica por Tipo de Antecedente</h5>
            <a href="index.php?controller=tipo_antecedente&action=reportePorAntecedente&download=pdf" 
               target="_blank" class="btn btn-light text-secondary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> Este reporte consolida la cantidad de pacientes únicos asociados a cada patología o antecedente registrado.
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped" width="100%">
                    <thead class="table-dark">
                        <tr>
                            <th width="150">ID Tipo</th>
                            <th>Categoría de Antecedente Médico</th>
                            <th width="250" class="text-center">Pacientes Diagnosticados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['antecedente_nombre']) ?></td>
                                    <td class="font-weight-bold text-center text-primary"><?= $row['total_pacientes'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No se registran antecedentes médicos vinculados a pacientes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
