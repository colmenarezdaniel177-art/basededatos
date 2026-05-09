<?php
$title = "Gestión de Especialias";
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fa-solid fa-hospital-user"></i>Gestión de Pacientes
        </h2>
        <p class="text-muted mb-0">Administre los Pacientes del sistema</p>
    </div>
    <a href="index.php?controller=paciente&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo Paciente
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fa-solid fa-hospital-user"></i>Lista de Pacientes
        </h5>
    </div>
    <div class="card-body">
        <?php if ($pacientes->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cedula</th>
                            <th>Fecha Nacimiento</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($paciente = $pacientes->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong>#<?php echo $paciente['id']; ?></strong></td>

                                <td><?php echo htmlspecialchars($paciente['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['cedula']); ?></td>
                                <td>
                                    <?php
                                    if (!empty($paciente['fecha_nacimiento']) && $paciente['fecha_nacimiento'] != '0000-00-00') {
                                        $fecha = new DateTime($paciente['fecha_nacimiento']);
                                        echo htmlspecialchars($fecha->format('d/m/Y'));
                                    } else {
                                        echo '<span class="text-muted">No registrada</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="index.php?controller=paciente&action=edit&id=<?php echo $paciente['id']; ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="index.php?controller=paciente&action=delete&id=<?php echo $paciente['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Está seguro de eliminar este paciente?')">
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
                <h4>No hay paciente registrados</h4>
                <p class="text-muted">Comience creando el primer paciente del sistema.</p>
                <a href="index.php?controller=paciente&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer paciente
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>