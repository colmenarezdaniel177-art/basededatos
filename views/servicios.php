<?php
session_start();
$usuario = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="servicios.css">
    <title>Ozono Vital</title>
</head>

<body>
    <header>
        <article class="container">
            <p class="logo">Ozono Vital</p>

            <!-- Botón hamburguesa SIEMPRE visible -->
            <button class="menu-toggle" aria-label="Abrir menú">&#9776;</button>

            <nav id="menu">
                <a href="index.php" class="btn">Inicio</a>
                <a href="sobre_nosotros.php" class="btn">Sobre Nosotros</a>
                <a href="juegos.php" class="btn">Juegos</a>
                <a href="contacto.php" class="btn">Contacto</a>
                <a href="servicios.php" class="btn">Servicios</a>
                <?php
                $userLoggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']) && $_SESSION['user'] != null;

                if ($userLoggedIn): ?>
                    <a href="../public/index.php?controller=paciente&action=index2" class="btn">Mis Pacientes</a>
                    <a href="../public/index.php?controller=cita&action=index2" class="btn">Mis Citas</a>
                    <a href="../Login/logout.php" class="btn">Cerrar sesión (<?php echo htmlspecialchars($_SESSION['user']['login']); ?>)</a>
                <?php else: ?>
                    <a href="../Login/login.php" class="btn">Iniciar sesión</a>
                <?php endif; ?>
            </nav>
        </article>
    </header>

    <section id="hero">
        <h1>En <span class="color-acento">Ozono Vital</span> <br>
            Brindamos el apoyo que necesitas💜
        </h1>

    </section>
    <!-- Sección de Productos -->
    <section id="Personal">
        <article class="container">
            <h2 class="section-title">Especialistas</h2>
            <article class="products-grid">

                <section class="product-card">

                    <article class="product-image">
                        <img src="img/Dalesky.jpeg" alt="Dalesky">
                    </article>

                    <article class="product-info">

                        <h3 class="product-title">Dalesky Medina</h3>
                        <p>Psicóloga</p>
                        <p class="product-price">$40.00</p>
                        <button class="add-to-cart">Solicitar atención especializada</button>

                    </article>

                </section>

                <section class="product-card">

                    <article class="product-image">
                        <img src="img/Edith.jpeg" alt="Edith">
                    </article>

                    <article class="product-info">
                        <h3 class="product-title">Edith Rodríguez</h3>
                        <p>Psicopedagóga</p>
                        <p class="product-price">$30.00</p>
                        <button class="add-to-cart">Solicitar atención especializada</button>
                    </article>

                </section>

                <article class="product-card">

                    <article class="product-image">
                        <img src="img/Emma.jpeg" alt="Emma">
                    </article>

                    <article class="product-info">

                        <h3 class="product-title">Emma Zurita</h3>
                        <p>Terapista Ocupacional</p>
                        <p class="product-price">$30.00</p>
                        <button class="add-to-cart">Solicitar atención especializada</button>

                    </article>

                </article>

                <article class="product-card">

                    <article class="product-image">
                        <img src="img/Griselda.jpeg" alt="Griselda">
                    </article>

                    <article class="product-info">

                        <h3 class="product-title">Griselda Martínez</h3>
                        <p>Terapista de Lenguaje</p>
                        <p class="product-price">$30.00</p>
                        <button class="add-to-cart">Solicitar atención especializada</button>

                    </article>

                </article>
            </article>
    </section>

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
<footer>
    <article class="container">
        <p>&copy; Ozono Vital 2025</p>
    </article>
</footer>

</html>