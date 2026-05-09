<?php
$title = "Atención de Consulta";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-user-nurse me-2"></i>Detalles de la Consulta</h4>
                <span class="badge bg-light text-primary">Cita #<?php echo $cita['id']; ?></span>
            </div>
            <div class="card-body">
                <div class="row mb-4 p-3 bg-light rounded">
                    <div class="col-md-4">
                        <label class="text-muted d-block">Paciente</label>
                        <strong><?php echo htmlspecialchars($cita['paciente_nombre']); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted d-block">Especialista</label>
                        <strong><?php echo htmlspecialchars($cita['especialista_nombre']); ?></strong>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted d-block">Fecha</label>
                        <strong><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></strong>
                    </div>
                </div>

                <form method="POST" action="index.php?controller=cita&action=guardarConsulta">
                    <input type="hidden" name="cita_id" value="<?php echo $cita['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Motivo de la Consulta</label>
                            <textarea class="form-control" name="motivo_consulta" rows="2" placeholder="Describa el motivo..."></textarea>
                        </div>                        
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Diagnóstico / Tratamiento</label>
                            <textarea class="form-control" name="tratamiento" rows="4" required placeholder="Medicamentos o indicaciones"></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Observaciones adicionales</label>
                            <textarea class="form-control" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3 border-top pt-3">
                        <a href="index.php?controller=cita&action=index" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Finalizar Consulta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>