<?php
$title = "Gestión de Usuarios";
require_once '../views/layouts/header.php';

// Obtener nombres reales de roles (esto debería venir del controlador en una implementación ideal)
$rolNombres = [];
if (isset($usuarios) && $usuarios->rowCount() > 0) {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $rolStmt = $db->query("SELECT id, nombre FROM rol");
    $roles = $rolStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roles as $rol) {
        $rolNombres[$rol['id']] = $rol['nombre'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fas fa-users me-2"></i>Gestión de Usuarios
        </h2>
        <p class="text-muted mb-0">Administre los usuarios del sistema</p>
    </div>
    <a href="index.php?controller=usuario&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Usuarios
        </h5>
    </div>
    <div class="card-body">
        <?php if ($usuarios->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="80">ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong>#<?php echo $usuario['id']; ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($usuario['login']); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    // Asignar colores según el rol
                                    $rolColors = [
                                        'Administrador' => 'danger',
                                        'Usuario' => 'success',
                                        'Invitado' => 'secondary'
                                    ];
                                    $rolNombre = $rolNombres[$usuario['rol_id']] ?? 'Rol ' . $usuario['rol_id'];
                                    $color = $rolColors[$rolNombre] ?? 'primary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $rolNombre; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="index.php?controller=usuario&action=edit&id=<?php echo $usuario['id']; ?>"
                                            class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar usuario">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <a href="index.php?controller=usuario&action=delete&id=<?php echo $usuario['id']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Está seguro de eliminar el usuario \" <?php echo htmlspecialchars($usuario['login']); ?>\"?')"
                                            data-bs-toggle="tooltip" title="Eliminar usuario">
                                            <i class="fas fa-trash"></i> Eliminar
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
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4>No hay usuarios registrados</h4>
                <p class="text-muted">Comience creando el primer usuario del sistema.</p>
                <a href="index.php?controller=usuario&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Usuario
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>