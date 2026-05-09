<?php
$title = "Gestión de Roles";
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fa fa-id-card"></i>Gestión de Roles
        </h2>
        <p class="text-muted mb-0">Administre los Roles del sistema</p>
    </div>
    <a href="index.php?controller=rol&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Rol
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Roles
        </h5>
    </div>
    <div class="card-body">
        <?php if ($roles->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rol = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?php echo $rol['id']; ?></td>
                            <td><?php echo htmlspecialchars($rol['nombre']); ?></td>
                            <td>
                                <a href="index.php?controller=rol&action=edit&id=<?php echo $rol['id']; ?>"
                                    class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="index.php?controller=rol&action=delete&id=<?php echo $rol['id']; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Está seguro de eliminar este rol?')">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4>No hay roles registrados</h4>
                <p class="text-muted">Comience creando el primer rol del sistema.</p>
                <a href="index.php?controller=rol&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Rol
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>