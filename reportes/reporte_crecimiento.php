<?php
// views/paciente/reporte_crecimiento.php
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

// views/paciente/reporte_crecimiento.php

$query = "SELECT MONTH(fecha_registro) AS mes_num, COUNT(*) AS total_nuevos 
          FROM paciente 
          GROUP BY MONTH(fecha_registro)
          ORDER BY mes_num ASC";
$stmt = $db->prepare($query);
$stmt->execute();



$meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-user-plus"></i> Dirección: Crecimiento Base de Datos Pacientes</h5>
            <a href="index.php?controller=paciente&action=reporteCrecimientoGerencial&download=pdf&anio=<?= $anio ?>" 
               target="_blank" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar PDF Gerencial
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-secondary" role="alert">
                <i class="fas fa-info-circle"></i> Análisis estratégico mensual de captación de usuarios en el sistema clínico.
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Mes de Registro</th>
                            <th>Nuevos Pacientes Incorporados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <?php 
                                    $mes_idx = ($row['mes_num'] >= 1 && $row['mes_num'] <= 12) ? $row['mes_num'] : 1;
                                    ?>
                                    <td class="font-weight-bold"><?= $meses[$mes_idx] ?></td>
                                    <td class="text-primary font-weight-bold fs-5"><?= $row['total_nuevos'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">No se registran pacientes incorporados en este año.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
