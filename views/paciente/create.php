<?php
$title = "Crear Nuevo Paciente";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fa-solid fa-hospital-user"></i>Crear Paciente
            </h4>
            <a href="index.php?controller=paciente&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-hospital-user"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?controller=paciente&action=create" id="pacienteForm">
                <div class="row">
                    <div class="col-md-9">
                        <label for="nombre;" class="form-label">Nombre de Paciente *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            required maxlength="50" placeholder="Ingrese el nombre de paciente">

                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula *</label>
                            <input type="text" class="form-control" id="cedula" name="cedula"
                                required maxlength="20" placeholder="Ingrese el número de cédula"
                                pattern="[0-9]+" title="Solo se permiten números">
                            <div class="form-text" id="cedulaFeedback">
                                Ingrese solo números. Ejemplo: 12345678
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                required max="<?php echo date('Y-m-d'); ?>">
                            <div class="form-text" id="edadFeedback">
                                Seleccione la fecha de nacimiento
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?controller=paciente&action=index" class="btn btn-outline-secondary px-4">
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