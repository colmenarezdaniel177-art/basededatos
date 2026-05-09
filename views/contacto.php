<?php
session_start();
$usuario = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="contacto.css">
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

  <!-- Hero -->
  <section id="hero">
    <h1>Contacta con <span class="color-acento">Ozono Vital</span></h1>
    <p>Estamos aquí para ayudarte con cualquier consulta</p>
  </section>

  <!-- Sección de contacto -->
  <section id="contacto">
    <h2 class="section-title">Información de contacto</h2>
    <article class="contact-info">
      <p><strong>📍 Dirección:</strong> Centro Centro Odontologico Moreno, Calle Juárez entre San Rafael y, C. Vicente Amengual, Cabudare 3001, Lara</p>
      <p><strong>📞 Teléfono:</strong> +58 414-5441720</p>
      <p><strong>📧 Email:</strong> PsicoinOzonoVitals@gmail.com</p>
    </article>
  </section>

  <!-- Footer -->
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