<?php
$title = "Crear Rol";
require_once '../views/layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fa fa-id-card"></i>Crear Rol
            </h4>
            <a href="index.php?controller=especialista&action=index" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?controller=rol&action=create">
                <div class="row">
                    <div class="col-md-9">
                        <label for="nombre;" class="form-label">Nombre del Rol *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            required maxlength="50">
                        <div class="form-text" id="usernameFeedback">
                            El nombre de especialista debe ser �nico.
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?controller=rol&action=index" class="btn btn-outline-secondary px-4">
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