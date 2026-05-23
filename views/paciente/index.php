<?php
$title = "Gestión de Pacientes";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="main-container">

    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="content">

        <!-- HEADER -->
        <div class="dashboard-header">

            <div>
                <h1 class="page-title">
                    Gestión de Pacientes
                </h1>

                <p class="dashboard-subtitle">
                    Administre los pacientes registrados en el sistema
                </p>
            </div>

            <a href="index.php?controller=paciente&action=create"
                class="btn-modern btn-primary-modern">

                <i class="fas fa-plus"></i>
                Nuevo Paciente

            </a>

        </div>

        <!-- TABLE CARD -->
        <div class="card table-card">

            <div class="card-header-modern">

                <h4>
                    Lista de Pacientes
                </h4>

                <input type="text"
                    class="input-modern"
                    placeholder="Buscar paciente..."
                    style="max-width: 280px;">

            </div>

            <?php if ($pacientes->rowCount() > 0): ?>

                <table class="table-modern">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Fecha Nacimiento</th>
                            <th>Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php while ($paciente = $pacientes->fetch(PDO::FETCH_ASSOC)): ?>

                            <tr>

                                <td>
                                    <strong>
                                        #<?php echo $paciente['id']; ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($paciente['nombre']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($paciente['cedula']); ?>
                                </td>

                                <td>

                                    <?php
                                    if (
                                        !empty($paciente['fecha_nacimiento']) &&
                                        $paciente['fecha_nacimiento'] != '0000-00-00'
                                    ) {

                                        $fecha = new DateTime($paciente['fecha_nacimiento']);

                                        echo $fecha->format('d/m/Y');

                                    } else {

                                        echo '<span class="text-muted">No registrada</span>';

                                    }
                                    ?>

                                </td>

                              <td>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">

        <!-- EDITAR -->
        <a href="index.php?controller=paciente&action=edit&id=<?php echo $paciente['id']; ?>"
            class="btn-modern btn-primary-modern"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Editar Paciente">

            <i class="fas fa-edit"></i>

        </a>

        <!-- ELIMINAR -->
        <a href="index.php?controller=paciente&action=delete&id=<?php echo $paciente['id']; ?>"
            class="btn-modern btn-danger-modern"
            onclick="return confirm('¿Está seguro de eliminar este paciente?')"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Eliminar Paciente">

            <i class="fas fa-trash"></i>

        </a>

        <!-- ANTECEDENTES -->
        <a href="index.php?controller=paciente_antecedente&action=ver&id=<?php echo $paciente['id']; ?>"
            class="btn-modern btn-success-modern"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Ver Antecedentes">

            <i class="fas fa-file-medical"></i>

        </a>

    </div>

</td>

                            </tr>

                        <?php endwhile; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="empty-state">

                    <i class="fas fa-user-injured"></i>

                    <h3>
                        No hay pacientes registrados
                    </h3>

                    <p>
                        Comience agregando el primer paciente al sistema.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>