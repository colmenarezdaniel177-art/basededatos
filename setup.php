<?php
require_once __DIR__ . '/config.php';

echo '<style>
body{font-family:sans-serif;padding:2rem;max-width:800px;line-height:1.7}
h2{color:#0d6efd} h3{color:#444;margin-top:1.5rem}
.ok{color:green} .err{color:red} .warn{color:orange}
code{background:#f4f4f4;padding:.1rem .4rem;border-radius:4px}
</style>';
echo '<h2>🏥 MediCitas — Setup completo desde cero</h2>';

try {
    // Connect without DB to create it first
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (PDOException $e) {
    die('<p class="err">Error de conexión: ' . htmlspecialchars($e->getMessage()) . '</p>');
}

function run(PDO $pdo, string $sql, string $label): void {
    try {
        $pdo->exec($sql);
        echo "<p class='ok'>✔ $label</p>";
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (str_contains($msg,'Duplicate') || str_contains($msg,'already exists') || str_contains($msg,'Duplicate column')) {
            echo "<p class='warn'>⚠ $label (ya existía)</p>";
        } else {
            echo "<p class='err'>✘ $label — " . htmlspecialchars($msg) . "</p>";
        }
    }
}

/* ── BASE DE DATOS ───────────────────────────────────────────── */
echo '<h3>Base de datos</h3>';
run($pdo, "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci", "Crear base de datos " . DB_NAME);
run($pdo, "USE `" . DB_NAME . "`", "Seleccionar base de datos");
$pdo->exec("USE `" . DB_NAME . "`");

/* ── TABLAS ──────────────────────────────────────────────────── */
echo '<h3>Tablas</h3>';

run($pdo, "CREATE TABLE IF NOT EXISTS roles (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50) NOT NULL UNIQUE,
  es_sistema  TINYINT(1) DEFAULT 0
)", "Tabla roles");

run($pdo, "CREATE TABLE IF NOT EXISTS especialidades (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(100) NOT NULL,
  descripcion TEXT,
  activo      TINYINT(1) DEFAULT 1
)", "Tabla especialidades");

run($pdo, "CREATE TABLE IF NOT EXISTS especialistas (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(100) NOT NULL,
  apellido        VARCHAR(100) NOT NULL,
  especialidad_id INT,
  telefono        VARCHAR(20),
  email           VARCHAR(150),
  citas_max_por_dia int(11) NOT NULL DEFAULT 1,
  activo          TINYINT(1) DEFAULT 1,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (especialidad_id) REFERENCES especialidades(id)
)", "Tabla especialistas");

run($pdo, "CREATE TABLE IF NOT EXISTS usuarios (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(100) NOT NULL,
  email           VARCHAR(150) NOT NULL UNIQUE,
  password        VARCHAR(255) NOT NULL,
  rol_id          INT NOT NULL DEFAULT 2,
  especialista_id INT NULL,
  activo          TINYINT(1) DEFAULT 1,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rol_id) REFERENCES roles(id),
  FOREIGN KEY (especialista_id) REFERENCES especialistas(id) ON DELETE SET NULL
)", "Tabla usuarios");

run($pdo, "CREATE TABLE IF NOT EXISTS pacientes (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id       INT,
  nombre           VARCHAR(100) NOT NULL,
  apellido         VARCHAR(100) NOT NULL,
  cedula           VARCHAR(20),
  fecha_nacimiento DATE,
  genero           ENUM('M','F') DEFAULT 'M',
  telefono         VARCHAR(20),
  email            VARCHAR(150),
  direccion        TEXT,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
)", "Tabla pacientes");

run($pdo, "CREATE TABLE IF NOT EXISTS status_cita (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL,
  color  VARCHAR(30) DEFAULT 'secondary',
  activo TINYINT(1) DEFAULT 1
)", "Tabla status_cita");

run($pdo, "CREATE TABLE IF NOT EXISTS tipo_antecedente (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  color  VARCHAR(30) DEFAULT 'secondary',
  activo TINYINT(1) DEFAULT 1
)", "Tabla tipo_antecedente");

run($pdo, "CREATE TABLE IF NOT EXISTS antecedentes (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id INT NOT NULL,
  tipo_id     INT,
  descripcion TEXT NOT NULL,
  fecha       DATE,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
  FOREIGN KEY (tipo_id)     REFERENCES tipo_antecedente(id)
)", "Tabla antecedentes");

run($pdo, "CREATE TABLE IF NOT EXISTS medicamentos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(150) NOT NULL,
  descripcion    TEXT,
  dosis_sugerida VARCHAR(100),
  activo         TINYINT(1) DEFAULT 1,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)", "Tabla medicamentos");

run($pdo, "CREATE TABLE IF NOT EXISTS citas (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id     INT NOT NULL,
  especialista_id INT NOT NULL,
  usuario_id      INT,
  fecha           DATE NOT NULL,
  motivo          TEXT,
  status_id       INT,
  estado          VARCHAR(50) DEFAULT 'Pendiente',
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (paciente_id)     REFERENCES pacientes(id),
  FOREIGN KEY (especialista_id) REFERENCES especialistas(id),
  FOREIGN KEY (usuario_id)      REFERENCES usuarios(id) ON DELETE SET NULL,
  FOREIGN KEY (status_id)       REFERENCES status_cita(id)
)", "Tabla citas");

run($pdo, "CREATE TABLE IF NOT EXISTS consultas (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  cita_id         INT,
  paciente_id     INT NOT NULL,
  especialista_id INT NOT NULL,
  fecha           DATETIME DEFAULT CURRENT_TIMESTAMP,
  motivo_consulta TEXT,
  diagnostico     TEXT,
  tratamiento     TEXT,
  observaciones   TEXT,
  FOREIGN KEY (cita_id)         REFERENCES citas(id) ON DELETE SET NULL,
  FOREIGN KEY (paciente_id)     REFERENCES pacientes(id),
  FOREIGN KEY (especialista_id) REFERENCES especialistas(id)
)", "Tabla consultas");

run($pdo, "CREATE TABLE IF NOT EXISTS consulta_medicamentos (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  consulta_id    INT NOT NULL,
  medicamento_id INT NOT NULL,
  dosis          VARCHAR(100),
  frecuencia     VARCHAR(100),
  duracion       VARCHAR(100),
  FOREIGN KEY (consulta_id)    REFERENCES consultas(id) ON DELETE CASCADE,
  FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id)
)", "Tabla consulta_medicamentos");

run($pdo, "CREATE TABLE IF NOT EXISTS horarios_especialista (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  especialistaId  INT NOT NULL,
  dia_semana      TINYINT(1) DEFAULT NULL,
  hora_inicio     TIME DEFAULT NULL,
  hora_fin        TIME DEFAULT NULL,
  FOREIGN KEY (especialistaId) REFERENCES especialistas(id) ON DELETE CASCADE
)", "Tabla horarios_especialista");

/* Columnas adicionales para instalaciones existentes */
run($pdo, "ALTER TABLE usuarios ADD COLUMN especialista_id INT NULL", "usuarios.especialista_id (si no existe)");
run($pdo, "ALTER TABLE usuarios ADD CONSTRAINT fk_usr_esp FOREIGN KEY (especialista_id) REFERENCES especialistas(id) ON DELETE SET NULL", "FK usuarios.especialista_id");
run($pdo, "ALTER TABLE pacientes ADD COLUMN cedula VARCHAR(20) NULL AFTER apellido", "pacientes.cedula (si no existe)");

/* ── DATOS POR DEFECTO ───────────────────────────────────────── */
echo '<h3>Datos por defecto</h3>';

// Roles de sistema
run($pdo, "INSERT IGNORE INTO roles (nombre, es_sistema) VALUES ('admin',1),('usuario',1),('medico',1)", "Roles del sistema");

// Status de cita
run($pdo, "INSERT IGNORE INTO status_cita (nombre,color) VALUES
  ('Pendiente','warning'),('Confirmada','info'),('Completada','success'),('Cancelada','danger')",
  "Status de cita predeterminados");

// Tipos de antecedente
run($pdo, "INSERT IGNORE INTO tipo_antecedente (nombre,color) VALUES
  ('Personal','primary'),('Familiar','secondary'),('Quirurgico','danger'),
  ('Alergico','warning'),('Farmacologico','info')",
  "Tipos de antecedente predeterminados");

// Especialidades de ejemplo
run($pdo, "INSERT IGNORE INTO especialidades (nombre,descripcion) VALUES
  ('Medicina General','Atención médica general'),
  ('Ginecología y Obstetricia','Especialista en salud femenina y control prenatal'),
  ('Traumatología y Ortopedia','Diagnóstico y tratamiento del sistema músculo-esquelético'),
  ('Fisioterapia y Rehabilitación','Recuperación funcional y terapias complementarias'),
  ('Ozonoterapia','Terapias de bienestar y medicina integrativa')",
  "Especialidades de OzonoVital");

// Medicamentos
run($pdo, "INSERT IGNORE INTO medicamentos (nombre,descripcion,dosis_sugerida,activo) VALUES
  ('Ibuprofeno','Antiinflamatorio no esteroideo (AINE)','400mg cada 8h',1),
  ('Paracetamol','Analgésico y antipirético','500mg cada 6h',1),
  ('Amoxicilina','Antibiótico de amplio espectro','500mg cada 8h por 7 días',1),
  ('Metformina','Antidiabético oral para diabetes tipo 2','850mg cada 12h con alimentos',1),
  ('Atorvastatina','Estatina para reducir el colesterol','20mg una vez al día',1),
  ('Losartán','Antihipertensivo, bloqueador de angiotensina II','50mg cada 24h',1),
  ('Omeprazol','Inhibidor de la bomba de protones (gastritis, reflujo)','20mg en ayunas',1),
  ('Loratadina','Antihistamínico para alergias','10mg cada 24h',1),
  ('Diclofenaco','Antiinflamatorio y analgésico','50mg cada 8h con alimentos',1),
  ('Azitromicina','Antibiótico macrólido de amplio espectro','500mg una vez al día por 3 días',1),
  ('Prednisona','Corticosteroide antiinflamatorio','5–60mg/día según indicación médica',1),
  ('Ranitidina','Antiulceroso, reduce la producción de ácido gástrico','150mg cada 12h',1),
  ('Metronidazol','Antibiótico y antiparasitario','500mg cada 8h por 7 días',1),
  ('Clonazepam','Benzodiazepina ansiolítica y anticonvulsivante','0.5mg cada 12h',1),
  ('Vitamina D3','Suplemento vitamínico para huesos e inmunidad','1000 UI una vez al día',1),
  ('Ciprofloxacino','Antibiótico fluoroquinolona de amplio espectro','500mg cada 12h por 7 días',1),
  ('Salbutamol','Broncodilatador para asma y EPOC','2 inhalaciones cada 4-6h según necesidad',1)",
  "Medicamentos de muestra");

/* ── USUARIO ADMINISTRADOR ───────────────────────────────────── */
echo '<h3>Usuario administrador</h3>';
$adminEmail = 'admin@clinica.com';
$chk = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$chk->execute([$adminEmail]);
if (!$chk->fetch()) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol_id) VALUES ('Administrador', ?, ?, 1)")
        ->execute([$adminEmail, $hash]);
    echo "<p class='ok'>✔ Admin creado: <strong>$adminEmail</strong> / <strong>admin123</strong></p>";
} else {
    echo "<p class='warn'>⚠ Admin ya existe.</p>";
}

echo '<hr>';
echo '<p style="background:#d4edda;padding:1rem;border-radius:8px">
  <strong>✅ Instalación completada.</strong><br>
  Inicia sesión en <a href="' . BASE_URL . '/index.php">' . BASE_URL . '/index.php</a><br>
  <strong>Admin:</strong> admin@clinica.com / admin123
</p>';
echo '<p style="color:red"><strong>⚠ ELIMINA este archivo (setup.php) después de configurar.</strong></p>';
