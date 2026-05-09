<?php
$title = "Editar Medicamento";
require_once '../views/layouts/header.php';
require_once '../config/database.php';

?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h4 class="card-title mb-0">
                    <i class="fas fa-pills me-2 text-primary"></i>Editar Medicamento
                </h4>
                <a href="index.php?controller=medicamento&action=index" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <form method="POST" action="index.php?controller=medicamento&action=edit&id=<?php echo $medicamento['id']; ?>">
                    <div class="mb-4">
                        <label for="nombre" class="form-label fw-bold">Nombre del Medicamento *</label>
                        <input type="text" class="form-control form-control-lg" id="nombre" name="nombre"
                            required maxlength="50" placeholder="Ingrese el nombre del medicamento"
                            value="<?php echo htmlspecialchars($medicamento['nombre']); ?>">
                        <div class="form-text" id="usernameFeedback">
                            El nombre del medicamento debe ser único.
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="index.php?controller=medicamento&action=index" class="btn btn-light px-4">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-save me-1"></i> Guardar Medicamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>