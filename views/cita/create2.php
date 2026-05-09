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
    $title = "Crear Nuevo Cita";
    ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i class="fa fa-calendar"></i>Crear Cita
                </h4>
                <a href="index.php?controller=cita&action=index2" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                    unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <form method="POST" action="index.php?controller=cita&action=create2" id="especialistaForm">
                    <div class="row">
                        <div class="col-md-9">
                            <label for="nombre;" class="form-label">Paciente *</label>
                            <select class="form-select" id="paciente_id" name="paciente_id" required>
                                <option value="">Seleccione un Paciente</option>
                                <?php if (!empty($pacientes)): ?>
                                    <?php foreach ($pacientes as $p): ?>
                                        <option value="<?php echo $p['id']; ?>">
                                            <?php echo htmlspecialchars($p['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay pacientes disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <label for="nombre;" class="form-label">Especialista *</label>
                            <select class="form-select" id="especialista_id" name="especialista_id" required>
                                <option value="">Seleccione un Especialista</option>
                                <?php if (!empty($especialistas)): ?>
                                    <?php foreach ($especialistas as $e): ?>
                                        <option value="<?php echo $e['id']; ?>">
                                            <?php echo htmlspecialchars($e['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay especialistas disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha *</label>
                                <input type="date" class="form-control" id="fecha" name="fecha"
                                    required min="<?php echo date('Y-m-d'); ?>">
                                <div class="form-text" id="fechaFeedback">
                                    Seleccione la fecha
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-9">
                            <label for="nota" class="form-label">Nota</label>
                            <input type="text" class="form-control" id="nota" name="nota"
                                maxlength="200" placeholder="Ingrese una nota">

                        </div>
                    </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?controller=cita&action=index2" class="btn btn-outline-secondary px-4">
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