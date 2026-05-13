<?php
session_start();
$usuario = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ozono Vital</title>
  <link rel="stylesheet" href="juegos.css">
</head>

<body>

  <header>
    <article class="container">
      <p class="logo">Ozono Vital</p>

      <!-- Botón hamburguesa SIEMPRE visible -->
      <button class="menu-toggle" aria-label="Abrir menú">&#9776;</button>

      <nav id="menu">
        <button class="close-menu" aria-label="Cerrar menú">&#x2192;</button> <!-- Flecha hacia la derecha -->
        <a href="index.php" class="btn">Inicio</a>
 
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

  <article class="quiz-contenedor">
    <h2>PsicoQuiz para ejercitar la mente 🧠</h2>
    <p>Selecciona la opción correcta para cada pregunta</p>

    <!-- Pregunta 1 -->
    <article class="pregunta">
      <h3>1. ¿Cuál neurotransmisor se asocia principalmente con los sistemas de recompensa, placer y motivación en el
        cerebro, y juega un papel clave en la adicción?</h3>
      <label><input type="radio" name="pregunta1" class="correcta"> Dopamina</label>
      <label><input type="radio" name="pregunta1" class="incorrecta"> Acetilcolina</label>
      <label><input type="radio" name="pregunta1" class="incorrecta"> Serotonina</label>
      <label><input type="radio" name="pregunta1" class="incorrecta"> GABA (Ácido Gamma-aminobutírico)</label>
    </article>

    <!-- Pregunta 2 -->
    <article class="pregunta">
      <h3>2. La memoria de procedimientos (o procedimental), que permite realizar tareas motoras sin esfuerzo
        consciente, es una forma de memoria no declarativa (implícita). ¿Qué otro fenómeno psicológico también se
        considera una forma de memoria no declarativa?</h3>
      <label><input type="radio" name="pregunta2" class="incorrecta"> Memoria episódica</label>
      <label><input type="radio" name="pregunta2" class="incorrecta"> Memoria de trabajo</label>
      <label><input type="radio" name="pregunta2" class="correcta"> Priming (o Preparación)</label>
      <label><input type="radio" name="pregunta2" class="incorrecta"> Memoria semántica</label>
    </article>

    <!-- Pregunta 3 -->
    <article class="pregunta">
      <h3>3. ¿Quién es considerado el fundador del estructuralismo en la psicología, cuyo objetivo principal era
        identificar los elementos básicos de la experiencia y la conciencia a través del método de la introspección?
      </h3>
      <label><input type="radio" name="pregunta3" class="incorrecta"> Sigmund Freud</label>
      <label><input type="radio" name="pregunta3" class="correcta"> Wilhelm Wundt</label>
      <label><input type="radio" name="pregunta3" class="incorrecta"> B.F. SkinnerCarl Rogers</label>
      <label><input type="radio" name="pregunta3" class="incorrecta"> Carl Rogers</label>
    </article>

    <!-- Pregunta 4 -->
    <article class="pregunta">
      <h3>4. ¿Qué rama de la psicología se enfoca en el estudio de cómo las personas perciben, aprenden, recuerdan y
        resuelven problemas?</h3>
      <label><input type="radio" name="pregunta4" class="incorrecta"> Psicología del Desarrollo</label>
      <label><input type="radio" name="pregunta4" class="incorrecta"> Psicología Social</label>
      <label><input type="radio" name="pregunta4" class="incorrecta"> Psicología Clínica</label>
      <label><input type="radio" name="pregunta4" class="correcta"> Psicología Cognitiva</label>
    </article>
  </article>

  <script>
const toggle = document.querySelector('.menu-toggle');
const closeMenu = document.querySelector('.close-menu');
const menu = document.getElementById('menu');

toggle.addEventListener('click', () => {
  menu.classList.add('show'); // Mostrar el menú
  toggle.style.display = 'none'; // Ocultar el botón hamburguesa
  closeMenu.style.display = 'block'; // Mostrar la flecha para cerrar
});

closeMenu.addEventListener('click', () => {
  menu.classList.remove('show'); // Ocultar el menú
  toggle.style.display = 'block'; // Volver a mostrar el botón hamburguesa
  closeMenu.style.display = 'none'; // Ocultar la flecha
});
</script>

</body>
<footer>
  <article class="container">
    <p>&copy; Ozono Vital 2025</p>
  </article>
</footer>

</html>