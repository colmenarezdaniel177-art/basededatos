<?php
$title = "Editar Paciente";
require_once __DIR__ . '/../layouts/header.php';

/* FORMATEAR FECHA */
function formatearFechaParaInput($fecha)
{
    if (
        empty($fecha) ||
        $fecha == '0000-00-00' ||
        $fecha == '0000-00-00 00:00:00'
    ) {
        return '';
    }

    try {

        $fecha_obj = new DateTime($fecha);

        return $fecha_obj->format('Y-m-d');

    } catch (Exception $e) {

        return '';

    }
}

/* FECHA FORMATEADA */
$fecha_nacimiento_formateada = formatearFechaParaInput(
    $paciente['fecha_nacimiento']
);
?>

<div class="main-container">

    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="content">

        <!-- HEADER -->
        <div class="dashboard-header">

            <div>

                <h1 class="page-title">
                    Editar Paciente
                </h1>

                <p class="dashboard-subtitle">
                    Modifique la información del paciente
                </p>

            </div>

            <a href="index.php?controller=paciente&action=index"
                class="btn-modern btn-primary-modern">

                <i class="fas fa-arrow-left"></i>
                Volver

            </a>

        </div>

        <!-- FORM CARD -->
        <div class="card" style="max-width:900px;">

            <?php if (isset($_SESSION['error'])): ?>

                <div class="alert alert-danger mb-4">

                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>

                </div>

            <?php endif; ?>

            <!-- FORM -->
            <form method="POST"
                action="index.php?controller=paciente&action=edit&id=<?php echo $paciente['id']; ?>">

                <!-- NOMBRE -->
                <div class="mb-4">

                    <label for="nombre" class="form-label fw-semibold mb-2">
                        Nombre del Paciente *
                    </label>

                    <input type="text"
                        class="input-modern"
                        id="nombre"
                        name="nombre"
                        maxlength="50"
                        placeholder="Ingrese el nombre del paciente"
                        value="<?php echo htmlspecialchars($paciente['nombre']); ?>"
                        required>

                </div>

                <!-- CEDULA -->
                <div class="mb-4">

                    <label for="cedula" class="form-label fw-semibold mb-2">
                        Cédula *
                    </label>

                    <input type="text"
                        class="input-modern"
                        id="cedula"
                        name="cedula"
                        required
                        maxlength="20"
                        placeholder="Ingrese el número de cédula"
                        pattern="[0-9]+"
                        title="Solo se permiten números"
                        value="<?php echo htmlspecialchars($paciente['cedula']); ?>">

                    <small class="form-help">
                        Ingrese solo números. Ejemplo: 12345678
                    </small>

                </div>

                <!-- FECHA -->
                <div class="mb-4">

                    <label for="fecha_nacimiento"
                        class="form-label fw-semibold mb-2">

                        Fecha de Nacimiento *

                    </label>

                    <input type="date"
                        class="input-modern"
                        id="fecha_nacimiento"
                        name="fecha_nacimiento"
                        value="<?php echo htmlspecialchars($fecha_nacimiento_formateada); ?>"
                        required
                        max="<?php echo date('Y-m-d'); ?>">

                    <small class="form-help">
                        Seleccione la fecha de nacimiento del paciente
                    </small>

                </div>

                <!-- ACTIONS -->
                <div class="d-flex justify-content-end gap-3 mt-5">

                    <a href="index.php?controller=paciente&action=index"
                        class="btn-modern btn-danger-modern">

                        <i class="fas fa-times"></i>
                        Cancelar

                    </a>

                    <button type="submit"
                        class="btn-modern btn-success-modern">

                        <i class="fas fa-save"></i>
                        Actualizar Paciente

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>