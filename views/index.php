<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OzonoVital</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
  <div class="navbar">
    <div class="logo">OzonoVital</div>

    <nav class="menu" id="menu">
      <a href="#acerca">Acerca de nosotros</a>
      <a href="#directorio">Directorio médico</a>
      <a href="#servicios">Servicios</a>
      <a href="#ubicacion">Ubicacion</a>
      <a href="#contacto">Contacto</a>
      <a href="#faq">Preguntas</a>

      <a href="../Login/index.php" class="btn-login mobile">Iniciar sesión</a>
      <a href="pedir_cita.php" class="btn-cita mobile">Pedir cita</a>
    </nav>

    <div class="menu-right">
      <a href="../Login/index.php" class="btn-login desktop">Iniciar sesión</a>
      <a href="pedir_cita.php" class="btn-cita desktop">Pedir cita</a>
      <span class="menu-toggle" id="toggle">☰</span>
    </div>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div class="carousel">

    <div class="slide active">
      <img src="img/ginecologia-fertilidad-2-1024x724.jpg">
      <div class="overlay"></div>
      <div class="content">
        <h1>Confianza en ginecología </h1>
        <p>Cuidado especializado para la mujer</p>
        <a href="pedir_cita.php" class="btn-hero">Pide una cita</a>
      </div>
    </div>

    <div class="slide">
      <img src="img/Traumatologia.jpg">
      <div class="overlay"></div>
      <div class="content">
        <h1>Transformar el cuidado de tu salud</h1>
        <p>Atención médica profesional </p>
        <a href="#servicios" class="btn-hero">Ver servicios</a>
      </div>
    </div>

    <div class="slide">
      <img src="img/ozonoterapia.jpg">
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

<!-- ACERCA MEJORADO -->
<section id="acerca" class="section about-section">
  <h2>Acerca de nosotros</h2>

  <div class="about-intro">
    <p>
      <strong>Ozono Vital</strong> es una institución orientada al cuidado integral de la salud en Barquisimeto. 
      Nos enfocamos en la prevención, recuperación y bienestar de nuestros pacientes, integrando la medicina 
      convencional con terapias regenerativas innovadoras.
    </p>
  </div>

  <div class="about-cards">

    <div class="about-card">
      <h3>Misión</h3>
      <p>
        Brindar atención médica integral de alta calidad, promoviendo la prevención y el bienestar general 
        mediante tratamientos regenerativos seguros y efectivos.
      </p>
    </div>

    <div class="about-card">
      <h3>Visión</h3>
      <p>
        Ser un centro médico de referencia en Venezuela en medicina integrativa, reconocido por la excelencia 
        profesional, innovación terapéutica y compromiso con el paciente.
      </p>
    </div>

  </div>
</section>

<!-- DIRECTORIO MÉDICO MEJORADO -->
<section id="directorio" class="section directory-section">

  <h2>Directorio Médico</h2>

  <p class="directory-intro">
    Conoce a nuestro equipo de especialistas altamente capacitados, comprometidos con brindarte una atención médica integral, humana y profesional.
  </p>

  <div class="cards">

    <div class="card">
      <h3>Dr. Daniel Lucena</h3>
      <p><strong>Ginecología y Obstetricia</strong></p>
      <p>Especialista en medicina regenerativa y control prenatal.</p>
    </div>

    <div class="card">
      <h3>Dra. Maria Duin</h3>
      <p><strong>Traumatología y Ortopedia</strong></p>
      <p>Especialista en cirugía articular y manejo integral del dolor.</p>
    </div>

    <div class="card">
      <h3>Dr. Ignacio Escalona</h3>
      <p><strong>Fisioterapia y Rehabilitación</strong></p>
      <p>Experto en recuperación funcional y terapias complementarias.</p>
    </div>

    <div class="card">
      <h3>Dr. Rafael Garmendia</h3>
      <p><strong>Ozonoterapia</strong></p>
      <p>Especialista en terapias de bienestar y medicina integrativa.</p>
    </div>

  </div>

</section>

<!-- SERVICIOS MEJORADO -->
<section id="servicios" class="section services-section">

  <h2>Nuestros Servicios</h2>

  <p class="services-intro">
    En Ozono Vital combinamos la medicina convencional con terapias regenerativas para ofrecer una atención integral,
    segura y personalizada.
  </p>

  <div class="services-grid">

    <div class="service-card">
      <div class="service-icon">🩺</div>
      <h3>Ginecología y Obstetricia</h3>
      <p>Atención especializada, control prenatal y procedimientos de ginecología regenerativa.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">🦴</div>
      <h3>Traumatología y Ortopedia</h3>
      <p>Diagnóstico y tratamiento de lesiones del sistema músculo-esquelético.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">⚡</div>
      <h3>Terapias Regenerativas</h3>
      <p>Ozonoterapia y sueroterapia con oligoelementos para recuperación celular.</p>
    </div>

    <div class="service-card">
      <div class="service-icon">🏃‍♀️</div>
      <h3>Rehabilitación</h3>
      <p>Fisioterapia y manejo del dolor con protocolos personalizados.</p>
    </div>

  </div>

</section>

<!-- UBICACION -->
<section id="ubicacion" class="section location-section">
  <h2>Ubicación</h2>

  <div class="location-wrapper">

    <div class="location-image">
      <img src="img/WhatsApp Image 2026-04-21 at 10.16.40 AM.jpeg">
    </div>

    <div class="location-info">
      <h3>OzonoVital</h3>
      <p>Carrera 19 esquina calle 13, 5to piso.</p>

      <a href="https://www.google.com/maps/place/Edificio+Parragon,+Barquisimeto"
         target="_blank"
         class="map-btn">
         Ver en Google Maps
      </a>
    </div>

  </div>
</section>

<!-- CONTACTO -->
<section id="contacto" class="section contact-section">
  <h2>Contacto</h2>

  <p class="contact-intro">
    Estamos disponibles para atenderte y resolver tus dudas. Puedes contactarnos por los siguientes medios o visitarnos en nuestra sede.
  </p>

  <div class="contact-grid">

    <div class="contact-card">
      <div class="contact-icon">📍</div>
      <h3>Ubicación</h3>
      <p>Carrera 19 con Calle 13, Edificio Centro Parragon, 5to piso. Barquisimeto, Estado Lara.</p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">🕒</div>
      <h3>Horario</h3>
      <p>Lunes a Viernes<br>8:00 a.m. – 5:00 p.m.</p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">📞</div>
      <h3>Teléfono</h3>
      <p>+58 (251) XXX-XXXX</p>
    </div>

    <div class="contact-card">
      <div class="contact-icon">✉️</div>
      <h3>Email</h3>
      <p>contacto@ozonovital.com</p>
    </div>

  </div>
</section>

<!-- FAQ -->
<section id="faq" class="section alt">
  <h2>Preguntas frecuentes</h2>

  <div class="faq-container">

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Cada cuánto debo acudir al ginecólogo?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Se recomienda al menos una vez al año para chequeos preventivos.</p>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Qué incluye una consulta ginecológica?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Incluye evaluación médica, revisión física y orientación profesional.</p>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Puedo asistir durante mi período menstrual?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Depende del tipo de consulta, pero suele recomendarse reprogramar.</p>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Ofrecen control prenatal?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Sí, contamos con seguimiento completo durante el embarazo.</p>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Realizan estudios de fertilidad?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Sí, ofrecemos evaluación y tratamientos personalizados.</p>
      </div>
    </div>

    <div class="faq-item">
      <div class="faq-header">
        <span>¿Cómo puedo agendar una cita?</span>
        <span class="arrow">▼</span>
      </div>
      <div class="faq-body">
        <p>Puedes hacerlo desde la web o llamando directamente.</p>
      </div>
    </div>

  </div>
</section>

<footer>
  <p>&copy; 2025 OzonoVital</p>
</footer>

<!-- SCRIPT -->
<script>
const toggle = document.getElementById("toggle");
const menu = document.getElementById("menu");

toggle.addEventListener("click", () => {
  menu.classList.toggle("show");
});

/* CARRUSEL */
let index = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(i){
  slides.forEach(s => s.classList.remove("active"));
  slides[i].classList.add("active");
}

document.querySelector(".next").onclick = () => {
  index = (index + 1) % slides.length;
  showSlide(index);
};

document.querySelector(".prev").onclick = () => {
  index = (index - 1 + slides.length) % slides.length;
  showSlide(index);
};

setInterval(()=>{
  index = (index + 1) % slides.length;
  showSlide(index);
}, 7000);

/* FAQ CORREGIDO */
const items = document.querySelectorAll(".faq-item");

items.forEach(item => {
  const header = item.querySelector(".faq-header");
  const body = item.querySelector(".faq-body");

  header.addEventListener("click", () => {

    items.forEach(i => {
      if(i !== item){
        i.classList.remove("active");
        i.querySelector(".faq-body").style.maxHeight = null;
      }
    });

    item.classList.toggle("active");

    if(item.classList.contains("active")){
      body.style.maxHeight = body.scrollHeight + "px";
    } else {
      body.style.maxHeight = null;
    }

  });
});
</script>

</body>
</html>