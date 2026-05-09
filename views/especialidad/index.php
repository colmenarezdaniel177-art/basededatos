<?php
$title = "Gestión de Especialidades"; 
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-stethoscope"></i> Gestión de Especialidades
        </h2>
        <p class="text-muted mb-0">Administre las categorías médicas del sistema</p>
    </div>
    <a href="index.php?controller=especialidad&action=create" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Nueva Especialidad
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0 text-primary">
            <i class="fas fa-list me-2"></i>Listado de Especialidades
        </h5>
    </div>
    <div class="card-body">
        <?php if ($especialidades && $especialidades->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="100">ID</th>
                            <th>Nombre de la Especialidad</th> 
                            <th width="200" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $especialidades->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $item['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="index.php?controller=especialidad&action=edit&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=especialidad&action=delete&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('¿Está seguro de eliminar esta especialidad? Esto podría afectar a los especialistas asociados.')">
                                            <i class="fas fa-trash"></i>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x text-light mb-3"></i>
                <h4 class="text-muted">No hay especialidades registradas</h4>
                <p class="mb-4">Para comenzar, debe definir al menos una especialidad médica.</p>
                <a href="index.php?controller=especialidad&action=create" class="btn btn-primary">
                    Crear mi primera especialidad
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>