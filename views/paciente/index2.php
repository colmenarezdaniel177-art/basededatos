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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">
            <i class="fa-solid fa-hospital-user"></i>Gestión de Pacientes
        </h2>
        <p class="text-muted mb-0">Administre los Pacientes del sistema</p>
    </div>
    <a href="index.php?controller=paciente&action=create2" class="btn btn-primary">
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
                <a href="index.php?controller=paciente&action=create2" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Crear Primer paciente
                </a>
            </div>
        <?php endif; ?>
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