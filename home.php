<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$isLogged = isLoggedIn();
$rol      = $_SESSION['rol'] ?? '';

// Redirigir admin y médico al dashboard
if ($isLogged && in_array($rol, ['admin','medico'])) {
    redirect(BASE_URL . '/dashboard.php');
}

// Datos del usuario tipo "usuario"
$misCitas    = [];
$misPacientes = [];
if ($isLogged && $rol === 'usuario') {
    $uid = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT c.*, CONCAT(p.nombre,' ',p.apellido) AS paciente, CONCAT(e.nombre,' ',e.apellido) AS especialista, sc.nombre AS estado_nombre, sc.color AS estado_color FROM citas c JOIN pacientes p ON c.paciente_id=p.id JOIN especialistas e ON c.especialista_id=e.id LEFT JOIN status_cita sc ON c.status_id=sc.id WHERE c.usuario_id=? AND c.fecha >= CURDATE() ORDER BY c.fecha LIMIT 5");
    $stmt->execute([$uid]);
    $misCitas = $stmt->fetchAll();

    $stmt2 = $pdo->prepare("SELECT * FROM pacientes WHERE usuario_id=? ORDER BY nombre LIMIT 8");
    $stmt2->execute([$uid]);
    $misPacientes = $stmt2->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>OzonoVital — Centro Médico</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/home.css">
</head>
<body>

<!-- ── HEADER ── -->
<header>
  <div class="navbar">
    <div class="logo">OzonoVital</div>

    <button class="hamburger" id="toggle">☰</button>

    <nav class="menu" id="menu">
      <a href="#acerca">Acerca de nosotros</a>
      <a href="#directorio">Directorio médico</a>
      <a href="#servicios">Servicios</a>
      <a href="#ubicacion">Ubicación</a>
      <a href="#contacto">Contacto</a>
      <a href="#faq">Preguntas</a>

      <a href="<?= BASE_URL ?>/index.php" class="btn-login mobile">Iniciar sesión</a>
      <a href="<?= BASE_URL ?>/citas/add.php" class="btn-cita mobile">Pedir cita</a>
    </nav>

    <?php if (!$isLogged): ?>
      <div>
        <a href="<?= BASE_URL ?>/index.php" class="btn-login desktop">Iniciar sesión</a>
        <a href="<?= BASE_URL ?>/index.php" class="btn-cita desktop">Pedir cita</a>
      </div>
    <?php else: ?>
      <div style="display:flex;align-items:center;gap:.75rem">
        <span class="user-name">Hola, <?= e($_SESSION['user_name']) ?></span>
        <a href="<?= BASE_URL ?>/citas/add.php" class="btn-cita desktop">Pedir cita</a>
        <a href="<?= BASE_URL ?>/logout.php" class="btn-login desktop">Logout</a>
      </div>
    <?php endif; ?>
  </div>
</header>

<!-- ── HERO ── -->
<section class="hero">
  <div class="carousel">
    <div class="slide active">
      <img src="<?= BASE_URL ?>/assets/img/ginecologia-fertilidad-2-1024x724.jpg" alt="Ginecología">
      <div class="overlay"></div>
      <div class="content">
        <h1>Confianza en ginecología</h1>
        <p>Cuidado especializado para la mujer</p>
        <a href="<?= $isLogged ? BASE_URL.'/citas/add.php' : BASE_URL.'/index.php' ?>" class="btn-hero">Pide una cita</a>
      </div>
    </div>
    <div class="slide">
      <img src="<?= BASE_URL ?>/assets/img/Traumatologia.jpg" alt="Traumatología">
      <div class="overlay"></div>
      <div class="content">
        <h1>Transformar el cuidado de tu salud</h1>
        <p>Atención médica profesional</p>
        <a href="#servicios" class="btn-hero">Ver servicios</a>
      </div>
    </div>
    <div class="slide">
      <img src="<?= BASE_URL ?>/assets/img/ozonoterapia.jpg" alt="Ozonoterapia">
      <div class="overlay"></div>
      <div class="content">
        <h1>Equipo médico profesional</h1>
        <p>Especialistas certificados</p>
        <a href="#directorio" class="btn-hero">Conócenos</a>
      </div>
    </div>
  </div>
  <button class="prev">❮</button>
  <button class="next">❯</button>
</section>

<!-- ── PANEL USUARIO LOGUEADO ── -->
<?php if ($isLogged && $rol === 'usuario'): ?>
<section class="user-section">
  <div class="user-panel">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
      <h2 style="margin:0">Mi panel</h2>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <a href="<?= BASE_URL ?>/pacientes/add.php" style="background:#0b7754;color:#fff;padding:.5rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600">+ Paciente</a>
        <a href="<?= BASE_URL ?>/citas/add.php" style="background:#0b7754;color:#fff;padding:.5rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600">+ Cita</a>
        <a href="<?= BASE_URL ?>/pacientes/index.php" style="border:2px solid #0b7754;color:#0b7754;padding:.5rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600">Ver pacientes</a>
        <a href="<?= BASE_URL ?>/citas/index.php" style="border:2px solid #0b7754;color:#0b7754;padding:.5rem 1rem;border-radius:8px;font-size:.85rem;font-weight:600">Ver citas</a>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;flex-wrap:wrap">
      <!-- Próximas citas -->
      <div>
        <h3 style="color:#0b7754;margin-bottom:1rem;font-size:1.1rem">Próximas citas</h3>
        <?php if (!$misCitas): ?>
          <p style="color:#888">No tienes citas próximas.</p>
        <?php else: ?>
          <?php foreach ($misCitas as $c): ?>
            <div class="cita-item">
              <div>
                <strong><?= e($c['paciente']) ?></strong>
                <div style="font-size:.82rem;color:#666"><?= formatDate($c['fecha']) ?> — <?= e($c['especialista']) ?></div>
              </div>
              <span style="background:#<?= $c['estado_color'] === 'warning' ? 'ffc107' : ($c['estado_color'] === 'success' ? '198754' : ($c['estado_color'] === 'danger' ? 'dc3545' : '0dcaf0')) ?>;color:#fff;padding:.25rem .65rem;border-radius:6px;font-size:.75rem;font-weight:600">
                <?= e($c['estado_nombre'] ?? 'Pendiente') ?>
              </span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Mis pacientes -->
      <div>
        <h3 style="color:#0b7754;margin-bottom:1rem;font-size:1.1rem">Mis pacientes</h3>
        <?php if (!$misPacientes): ?>
          <p style="color:#888">No tienes pacientes registrados.</p>
        <?php else: ?>
          <?php foreach ($misPacientes as $pac): ?>
            <div class="cita-item">
              <div>
                <strong><?= e($pac['nombre'].' '.$pac['apellido']) ?></strong>
                <div style="font-size:.82rem;color:#666"><?= calcularEdad($pac['fecha_nacimiento']) ?></div>
              </div>
              <a href="<?= BASE_URL ?>/pacientes/view.php?id=<?= $pac['id'] ?>" style="color:#0b7754;font-size:.82rem;font-weight:600">Ver</a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ── ACERCA ── -->
<section id="acerca" class="section about-section">
  <h2>Acerca de nosotros</h2>
  <div class="about-intro">
    <p><strong>Ozono Vital</strong> es una institución orientada al cuidado integral de la salud en Barquisimeto.
    Nos enfocamos en la prevención, recuperación y bienestar de nuestros pacientes, integrando la medicina
    convencional con terapias regenerativas innovadoras.</p>
  </div>
  <div class="about-cards">
    <div class="about-card">
      <h3>Misión</h3>
      <p>Brindar atención médica integral de alta calidad, promoviendo la prevención y el bienestar general mediante tratamientos regenerativos seguros y efectivos.</p>
    </div>
    <div class="about-card">
      <h3>Visión</h3>
      <p>Ser un centro médico de referencia en Venezuela en medicina integrativa, reconocido por la excelencia profesional, innovación terapéutica y compromiso con el paciente.</p>
    </div>
  </div>
</section>

<!-- ── DIRECTORIO MÉDICO ── -->
<section id="directorio" class="section directory-section alt">
  <h2>Directorio Médico</h2>
  <p class="directory-intro">Conoce a nuestro equipo de especialistas altamente capacitados, comprometidos con brindarte una atención médica integral, humana y profesional.</p>
  <div class="cards">
    <div class="card"><h3>Dr. Daniel Lucena</h3><p><strong>Ginecología y Obstetricia</strong></p><p>Especialista en medicina regenerativa y control prenatal.</p></div>
    <div class="card"><h3>Dra. Maria Duin</h3><p><strong>Traumatología y Ortopedia</strong></p><p>Especialista en cirugía articular y manejo integral del dolor.</p></div>
    <div class="card"><h3>Dr. Ignacio Escalona</h3><p><strong>Fisioterapia y Rehabilitación</strong></p><p>Experto en recuperación funcional y terapias complementarias.</p></div>
    <div class="card"><h3>Dr. Rafael Garmendia</h3><p><strong>Ozonoterapia</strong></p><p>Especialista en terapias de bienestar y medicina integrativa.</p></div>
  </div>
</section>

<!-- ── SERVICIOS ── -->
<section id="servicios" class="section services-section">
  <h2>Nuestros Servicios</h2>
  <p class="services-intro">En Ozono Vital combinamos la medicina convencional con terapias regenerativas para ofrecer una atención integral, segura y personalizada.</p>
  <div class="services-grid">
    <div class="service-card"><div class="service-icon">🩺</div><h3>Ginecología y Obstetricia</h3><p>Atención especializada, control prenatal y procedimientos de ginecología regenerativa.</p></div>
    <div class="service-card"><div class="service-icon">🦴</div><h3>Traumatología y Ortopedia</h3><p>Diagnóstico y tratamiento de lesiones del sistema músculo-esquelético.</p></div>
    <div class="service-card"><div class="service-icon">⚡</div><h3>Terapias Regenerativas</h3><p>Ozonoterapia y sueroterapia con oligoelementos para recuperación celular.</p></div>
    <div class="service-card"><div class="service-icon">🏃‍♀️</div><h3>Rehabilitación</h3><p>Fisioterapia y manejo del dolor con protocolos personalizados.</p></div>
  </div>
</section>

<!-- ── UBICACIÓN ── -->
<section id="ubicacion" class="section location-section alt">
  <h2>Ubicación</h2>
  <div class="location-wrapper">
    <div class="location-image">
      <img src="<?= BASE_URL ?>/assets/img/WhatsApp Image 2026-04-21 at 10.16.40 AM.jpeg" alt="Sede OzonoVital">
    </div>
    <div class="location-info">
      <h3>OzonoVital</h3>
      <p>Carrera 19 esquina calle 13, 5to piso.<br>Edificio Centro Parragon, Barquisimeto, Estado Lara.</p>
      <a href="https://www.google.com/maps/place/Edificio+Parragon,+Barquisimeto" target="_blank" class="map-btn">Ver en Google Maps</a>
    </div>
  </div>
</section>

<!-- ── CONTACTO ── -->
<section id="contacto" class="section contact-section">
  <h2>Contacto</h2>
  <p class="contact-intro">Estamos disponibles para atenderte y resolver tus dudas.</p>
  <div class="contact-grid">
    <div class="contact-card"><div class="contact-icon">📍</div><h3>Ubicación</h3><p>Carrera 19 con Calle 13, Edificio Centro Parragon, 5to piso. Barquisimeto, Estado Lara.</p></div>
    <div class="contact-card"><div class="contact-icon">🕒</div><h3>Horario</h3><p>Lunes a Viernes<br>8:00 a.m. – 5:00 p.m.</p></div>
    <div class="contact-card"><div class="contact-icon">📞</div><h3>Teléfono</h3><p>+58 (251) XXX-XXXX</p></div>
    <div class="contact-card"><div class="contact-icon">✉️</div><h3>Email</h3><p>contacto@ozonovital.com</p></div>
  </div>
</section>

<!-- ── FAQ ── -->
<section id="faq" class="section alt">
  <h2>Preguntas frecuentes</h2>
  <div class="faq-container">
    <?php
    $faqs = [
      ['¿Cada cuánto debo acudir al ginecólogo?','Se recomienda al menos una vez al año para chequeos preventivos.'],
      ['¿Qué incluye una consulta ginecológica?','Incluye evaluación médica, revisión física y orientación profesional.'],
      ['¿Puedo asistir durante mi período menstrual?','Depende del tipo de consulta, pero suele recomendarse reprogramar.'],
      ['¿Ofrecen control prenatal?','Sí, contamos con seguimiento completo durante el embarazo.'],
      ['¿Realizan estudios de fertilidad?','Sí, ofrecemos evaluación y tratamientos personalizados.'],
      ['¿Cómo puedo agendar una cita?','Puedes hacerlo desde la web o llamando directamente.'],
    ];
    foreach ($faqs as [$q,$a]): ?>
    <div class="faq-item">
      <div class="faq-header"><span><?= $q ?></span><span class="arrow">▼</span></div>
      <div class="faq-body"><p><?= $a ?></p></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<footer><p>&copy; <?= date('Y') ?> OzonoVital</p></footer>

<script>
/* Hamburger */
document.getElementById('toggle').addEventListener('click', () => {
  document.getElementById('menu').classList.toggle('show');
});

/* Carrusel */
let idx = 0;
const slides = document.querySelectorAll('.slide');
function showSlide(i){ slides.forEach(s=>s.classList.remove('active')); slides[i].classList.add('active'); }
document.querySelector('.next').onclick = () => { idx=(idx+1)%slides.length; showSlide(idx); };
document.querySelector('.prev').onclick = () => { idx=(idx-1+slides.length)%slides.length; showSlide(idx); };
setInterval(()=>{ idx=(idx+1)%slides.length; showSlide(idx); }, 7000);

/* FAQ */
document.querySelectorAll('.faq-item').forEach(item=>{
  item.querySelector('.faq-header').addEventListener('click', ()=>{
    const body = item.querySelector('.faq-body');
    document.querySelectorAll('.faq-item').forEach(i=>{
      if(i!==item){ i.classList.remove('active'); i.querySelector('.faq-body').style.maxHeight=null; }
    });
    item.classList.toggle('active');
    body.style.maxHeight = item.classList.contains('active') ? body.scrollHeight+'px' : null;
  });
});
</script>
</body>
</html>
