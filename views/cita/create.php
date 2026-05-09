<?php
$title = "Crear Nuevo Cita";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fa fa-calendar"></i>Crear Cita
            </h4>
            <a href="index.php?controller=cita&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?controller=cita&action=create" id="especialistaForm">
                <div class="row">
                    <div class="col-md-9">
                        <label for="nombre;" class="form-label">Paciente *</label>
                        <select class="form-select" id="paciente_id" name="paciente_id" required>
                            <option value="">Seleccione un Paciente</option>
                            <?php if (!empty($pacientes)): ?>
                                <?php foreach ($pacientes as $p): ?>
                                    <option value="<?php echo $p['id']; ?>">
                                        <?php echo htmlspecialchars($p['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay pacientes disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <label for="nombre;" class="form-label">Especialista *</label>
                        <select class="form-select" id="especialista_id" name="especialista_id" required>
                            <option value="">Seleccione un Especialista</option>
                            <?php if (!empty($especialistas)): ?>
                                <?php foreach ($especialistas as $e): ?>
                                    <option value="<?php echo $e['id']; ?>">
                                        <?php echo htmlspecialchars($e['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay especialistas disponibles</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha *</label>
                            <input type="date" class="form-control" id="fecha" name="fecha"
                                 required min="<?php echo date('Y-m-d'); ?>">
                            <div class="form-text" id="fechaFeedback">
                                Seleccione la fecha
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-9">
                        <label for="nota" class="form-label">Nota</label>
                        <input type="text" class="form-control" id="nota" name="nota"
                             maxlength="200" placeholder="Ingrese una nota">
                        
                    </div>
                </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="index.php?controller=cita&action=index" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
        </form>
    </div>
</div>
</div>
</div>



<?php require_once '../views/layouts/footer.php'; ?>