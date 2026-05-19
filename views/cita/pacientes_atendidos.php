<?php
// views/cita/pacientes_atendidos.php

$citaModel = new CitaModel($db);


// Capturar fechas elegidas por el usuario. Por defecto usa el mes actual.
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

// Consultar para la tabla en pantalla
$stmt = $citaModel->consultarCitasPorRango($fecha_inicio, $fecha_fin);
?>


<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">Filtro de Pacientes Atendidos</h5>
           
            <a href="index.php?controller=cita&action=reportePacientes&download=pdf&fecha_inicio=<?= $fecha_inicio ?>&fecha_fin=<?= $fecha_fin ?>" 
            target="_blank" class="btn btn-light text-primary font-weight-bold shadow-sm">
                <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
            </a>


        </div>
        <div class="card-body">
            <!-- Formulario de Filtros rápidos (Diario, Mensual, Anual) -->
            <form method="GET" action="index.php" class="row mb-4 align-items-end">
                <input type="hidden" name="controller" value="cita">
                <input type="hidden" name="action" value="reportePacientes">
                
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Fecha Inicio:</label>

                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="<?= $fecha_inicio ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="<?= $fecha_fin ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="fas fa-filter"></i> Filtrar en Pantalla
                    </button>
                </div>
            </form>

            <!-- Botones de configuración rápida -->
            <div class="mb-3">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFiltro('diario')">Hoy (Diario)</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFiltro('mensual')">Mes Actual</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setFiltro('anual')">Año Actual</button>
            </div>

            <!-- Tabla de visualización previa -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID Cita</th>
                            <th>Paciente</th>
                            <th>Doctor Especialista</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($stmt->rowCount() > 0): ?>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['paciente_nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['especialista_nombre']) ?></td>
                                    <td><?= $row['fecha'] ?></td>
                                </tr>
                            <?php endwhile; ?> <!-- CORRIGE AQUÍ: Debe decir endwhile; y no endbox; -->
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No se encontraron registros para este rango de fechas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Script para los botones rápidos Diario, Mensual y Anual -->
<script>
function setFiltro(tipo) {
    const hoy = new Date().toISOString().split('T')[0];
    const anio = new Date().getFullYear();
    const mes = String(new Date().getMonth() + 1).padStart(2, '0');

    if (tipo === 'diario') {
        document.getElementById('fecha_inicio').value = hoy;
        document.getElementById('fecha_fin').value = hoy;
    } else if (tipo === 'mensual') {
        document.getElementById('fecha_inicio').value = `${anio}-${mes}-01`;
        // Obtener último día del mes
        const ultimoDia = new Date(anio, mes, 0).getDate();
        document.getElementById('fecha_fin').value = `${anio}-${mes}-${ultimoDia}`;
    } else if (tipo === 'anual') {
        document.getElementById('fecha_inicio').value = `${anio}-01-01`;
        document.getElementById('fecha_fin').value = `${anio}-12-31`;
    }
}
</script>
