<?php
$title = "Gestionar Horario Semanal";
require_once '../views/layouts/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white d-flex justify-content-between">
        <h4><i class="fas fa-clock text-primary"></i> Configuración de Horario Semanal</h4>
        <a href="index.php?controller=especialista&action=index" class="btn btn-outline-secondary btn-sm">Volver</a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?controller=horario&action=gestionar">
            <input type="hidden" name="especialistaId" value="<?php echo $especialistaId; ?>">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Día</th>
                            <th>Estado</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dias_nombres as $num => $nombre): 
                            $h = $horarios[$num] ?? null;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo $nombre; ?></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           name="dias[<?php echo $num; ?>][activo]" 
                                           <?php echo $h ? 'checked' : ''; ?>>
                                    <label class="form-check-label">Laborable</label>
                                </div>
                            </td>
                            <td>
                                <input type="time" class="form-control" 
                                       name="dias[<?php echo $num; ?>][inicio]" 
                                       value="<?php echo $h['hora_inicio'] ?? '08:00'; ?>">
                            </td>
                            <td>
                                <input type="time" class="form-control" 
                                       name="dias[<?php echo $num; ?>][fin]" 
                                       value="<?php echo $h['hora_fin'] ?? '16:00'; ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="fas fa-save me-2"></i>Guardar Todo el Horario
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>