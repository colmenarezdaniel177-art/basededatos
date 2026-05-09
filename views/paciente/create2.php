<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   
    <link rel="stylesheet" href="../views/contacto.css">
    <title>Ozono Vital</title>
</head>

<body>
    <header>
        <article class="container">
            <p class="logo">Ozono Vital</p>

            <!-- Botón hamburguesa SIEMPRE visible -->
            <button class="menu-toggle" aria-label="Abrir menú">&#9776;</button>

            <nav id="menu">
                <a href="../views/index.php" class="btn">Inicio</a>
                <a href="../views/sobre_nosotros.php" class="btn">Sobre Nosotros</a>
                <a href="../views/juegos.php" class="btn">Juegos</a>
                <a href="../views/contacto.php" class="btn">Contacto</a>
                <a href="../views/servicios.php" class="btn">Servicios</a>
                <?php
                $userLoggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user'] != null;

                if ($userLoggedIn): ?>
                    <a href="index.php?controller=paciente&action=index2" class="btn">Mis Pacientes</a>
                    <a href="index.php?controller=cita&action=index2" class="btn">Mis Citas</a>
                    <a href="../Login/logout.php" class="btn">Cerrar sesión (<?php echo htmlspecialchars($_SESSION['user']['login']); ?>)</a>
                <?php else: ?>
                    <a href="../Login/login.php" class="btn">Iniciar sesión</a>
                <?php endif; ?>
            </nav>
        </article>
    </header>

    <?php
$title = "Crear Nuevo Paciente";
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fa-solid fa-hospital-user"></i>Crear Paciente
            </h4>
            <a href="index.php?controller=paciente&action=index2" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-hospital-user"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?controller=paciente&action=create2" id="pacienteForm">
                <div class="row">
                    <div class="col-md-9">
                        <label for="nombre;" class="form-label">Nombre de Paciente *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                            required maxlength="50" placeholder="Ingrese el nombre de paciente">

                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula *</label>
                            <input type="text" class="form-control" id="cedula" name="cedula"
                                required maxlength="20" placeholder="Ingrese el número de cédula"
                                pattern="[0-9]+" title="Solo se permiten números">
                            <div class="form-text" id="cedulaFeedback">
                                Ingrese solo números. Ejemplo: 12345678
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento *</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                required max="<?php echo date('Y-m-d'); ?>">
                            <div class="form-text" id="edadFeedback">
                                Seleccione la fecha de nacimiento
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php?controller=paciente&action=index2" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
    <footer>
        <article class="container">
            <p>&copy; Ozono Vital 2025</p>
        </article>
    </footer>

    <script>
        const toggle = document.querySelector('.menu-toggle');
        const menu = document.getElementById('menu');

        toggle.addEventListener('click', () => {
            menu.classList.toggle('show');
        });

        // opcional: cerrar menú al hacer clic en un enlace
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.remove('show');
            });
        });
    </script>
</body>

</html>