<?php
$title = "Gestión de Especialias"; 
require_once '../views/layouts/header.php';

$listNombres = [];
if (isset($especialistas) && $especialistas->rowCount() > 0) {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    $tmpStmt = $db->query("SELECT id, nombre FROM especialidad");
    $tmp = $tmpStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tmp as $record) {
        $listNombres[$record['id']] = $record['nombre'];
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fa fa-user-md"></i>Gestión de Especialistas
        </h2>
        <p class="text-muted mb-0">Administre los Especialistas del sistema</p>
    </div>
    <a href="index.php?controller=especialista&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Especialista
    </a>
</div>

<div class="card">   
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Especialista
        </h5>
    </div>
    <div class="card-body">
        <?php if ($especialistas->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Especialidad</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($especialista = $especialistas->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong>#<?php echo $especialista['id']; ?></strong></td>

                                <td><?php echo htmlspecialchars($especialista['nombre']); ?></td>
                                <td>
                                    <?php 
                                    
                                    $idEsp = $especialista['especialidad_id'];
                                    $nombreEspecialidad = isset($listNombres[$idEsp]) ? $listNombres[$idEsp] : "No asignada";
                                    ?>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($nombreEspecialidad); ?>
                                    </span>
                                </td>                                
                                <td>
                                    <a href="index.php?controller=especialista&action=edit&id=<?php echo $especialista['id']; ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="index.php?controller=especialista&action=delete&id=<?php echo $especialista['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Está seguro de eliminar este especialista?')">
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
                <h4>No hay especialistas registrados</h4>
                <p class="text-muted">Comience creando el primer especialista del sistema.</p>
                <a href="index.php?controller=especialista&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer Especialistas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>