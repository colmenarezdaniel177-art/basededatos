<?php
$title = "Gestión de Estatus de Cita"; 
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-calendar-check"></i> Gestión de Estatus de Cita
        </h2>
        <p class="text-muted mb-0">Administre los estados de cita del sistema</p>
    </div>
    <a href="index.php?controller=estatus_cita&action=create" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Nuevo Estatus de Cita
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
            <i class="fas fa-list me-2"></i>Listado de Estatus de Cita
        </h5>
    </div>
    <div class="card-body">
        <?php if ($estatus_citas && $estatus_citas->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="100">ID</th>
                            <th>Nombre del Estatus de Cita</th>
                            <th width="200" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $estatus_citas->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><span class="text-muted">#<?php echo $item['id']; ?></span></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['nombre']); ?></strong>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="index.php?controller=estatus_cita&action=edit&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=estatus_cita&action=delete&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('¿Está seguro de eliminar este estatus de cita? Esto podría afectar registros relacionados.')">
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
                <h4 class="text-muted">No hay estatus de cita registradas</h4>
                <p class="mb-4">Para comenzar, debe definir al menos un estatus de cita.</p>
                <a href="index.php?controller=estatus_cita&action=create" class="btn btn-primary">
                    Crear mi primer estatus de cita
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>