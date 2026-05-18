<?php
// views/cita/reporte_gerencial_mensual.php
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

$query = "SELECT MONTH(fecha) AS mes_num, COUNT(*) AS total_citas 
          FROM cita 
          WHERE YEAR(fecha) = :anio
          GROUP BY MONTH(fecha)
          ORDER BY mes_num ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
$stmt->execute();

$meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
?>

<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-chart-line"></i> Dirección: Volumen Mensual de Consultas</h5>
            <a href="index.php?controller=cita&action=reporteGerencialMensual&download=pdf&anio=<?= $anio ?>" 
               target="_blank" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar PDF Gerencial
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="cita">
                <input type="hidden" name="action" value="reporteGerencialMensual">
                
                <div class="col-md-8">
                    <label class="form-label font-weight-bold">Seleccionar Año de Análisis:</label>
                    <select name="anio" class="form-select">
                        <?php for($i = date('Y'); $i >= 2024; $i--): ?>
                            <option value="<?= $i ?>" <?= $anio == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-chart-bar"></i> Analizar Año</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Mes de Análisis</th>
                            <th>Cantidad Total de Consultas Procesadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td class="font-weight-bold"><?= $meses[$row['mes_num']] ?></td>
                                    <td class="text-success font-weight-bold fs-5"><?= $row['total_citas'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">No se registran actividades clínicas para el año seleccionado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
