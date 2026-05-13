<?php
$title = "Gestión de Citas";
require_once '../views/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fa fa-calendar"></i>Gestión de Citas
        </h2>
        <p class="text-muted mb-0">Administre las citas del sistema</p>
    </div>
    <a href="index.php?controller=cita&action=create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nueva Cita
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Citas
        </h5>
    </div>
    <div class="card-body">
        <?php if ($citas->rowCount() > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Especialista</th>
                            <th>Fecha</th>
                            <th>Status</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($cita = $citas->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong>#<?php echo $cita['id']; ?></strong></td>

                                <td><?php echo htmlspecialchars($cita['paciente_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($cita['especialista_nombre']); ?></td>
                                 <td>
                                    <?php
                                    if (!empty($cita['fecha']) && $cita['fecha'] != '0000-00-00') {
                                        $fecha = new DateTime($cita['fecha']);
                                        echo htmlspecialchars($fecha->format('d/m/Y'));
                                    } else {
                                        echo '<span class="text-muted">No registrada</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Asignar colores según el rol
                                    $statusColors = [
                                        'Cancelada' => 'danger',
                                        'Realizada' => 'success',
                                        'Vencida' => 'secondary',
                                        'Vigente' => 'info',
                                    ];                                    
                                    $color = $statusColors[$cita['status']] ?? 'primary';
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo htmlspecialchars($cita['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?controller=cita&action=edit&id=<?php echo $cita['id']; ?>"
                                        class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="index.php?controller=cita&action=delete&id=<?php echo $cita['id']; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Está seguro de eliminar este Cita?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                    <?php if ($cita['status'] !== 'Completada'): ?>
                                        <a href="index.php?controller=cita&action=atender&id=<?php echo $cita['id']; ?>"
                                        class="btn btn-sm btn-success">
                                        <i class="fas fa-stethoscope"></i> Atender
                                        </a>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled title="Esta cita ya ha sido finalizada">
                                        <i class="fas fa-check-double"></i> Atendida
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa fa-calendar fa-4x text-muted mb-3"></i>
                <h4>No hay citas registradas</h4>
                <p class="text-muted">Comience creando la primera cita del sistema.</p>
                <a href="index.php?controller=cita&action=create" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primera Cita
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../views/layouts/footer.php'; ?>