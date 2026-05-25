-- ============================================================
--  OzonoVital / MediCitas — Schema completo
--  Generado para MySQL 5.7+ / MariaDB 10.3+
--  Uso: mysql -u root -p < clinica_db.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Base de datos
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `clinica_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `clinica_db`;

-- ------------------------------------------------------------
-- Tablas
-- ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`     VARCHAR(50)  NOT NULL UNIQUE,
  `es_sistema` TINYINT(1)   DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `especialidades` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`      VARCHAR(100) NOT NULL,
  `descripcion` TEXT,
  `activo`      TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `especialistas` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`          VARCHAR(100) NOT NULL,
  `apellido`        VARCHAR(100) NOT NULL,
  `especialidad_id` INT,
  `telefono`        VARCHAR(20),
  `email`           VARCHAR(150),
  `activo`          TINYINT(1)   DEFAULT 1,
  `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`          VARCHAR(100) NOT NULL,
  `email`           VARCHAR(150) NOT NULL UNIQUE,
  `password`        VARCHAR(255) NOT NULL,
  `rol_id`          INT          NOT NULL DEFAULT 2,
  `especialista_id` INT          NULL,
  `activo`          TINYINT(1)   DEFAULT 1,
  `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`rol_id`)          REFERENCES `roles`(`id`),
  FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pacientes` (
  `id`               INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id`       INT,
  `nombre`           VARCHAR(100) NOT NULL,
  `apellido`         VARCHAR(100) NOT NULL,
  `cedula`           VARCHAR(20),
  `fecha_nacimiento` DATE,
  `genero`           ENUM('M','F') DEFAULT 'M',
  `telefono`         VARCHAR(20),
  `email`            VARCHAR(150),
  `direccion`        TEXT,
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `status_cita` (
  `id`     INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50)  NOT NULL,
  `color`  VARCHAR(30)  DEFAULT 'secondary',
  `activo` TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tipo_antecedente` (
  `id`     INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(80)  NOT NULL,
  `color`  VARCHAR(30)  DEFAULT 'secondary',
  `activo` TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `antecedentes` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `paciente_id` INT  NOT NULL,
  `tipo_id`     INT,
  `descripcion` TEXT NOT NULL,
  `fecha`       DATE,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tipo_id`)     REFERENCES `tipo_antecedente`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `medicamentos` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `nombre`         VARCHAR(150) NOT NULL,
  `descripcion`    TEXT,
  `dosis_sugerida` VARCHAR(100),
  `activo`         TINYINT(1)   DEFAULT 1,
  `created_at`     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `citas` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `paciente_id`     INT  NOT NULL,
  `especialista_id` INT  NOT NULL,
  `usuario_id`      INT,
  `fecha`           DATE NOT NULL,
  `motivo`          TEXT,
  `status_id`       INT,
  `estado`          VARCHAR(50)  DEFAULT 'Pendiente',
  `created_at`      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`paciente_id`)     REFERENCES `pacientes`(`id`),
  FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`),
  FOREIGN KEY (`usuario_id`)      REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`status_id`)       REFERENCES `status_cita`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consultas` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `cita_id`         INT,
  `paciente_id`     INT  NOT NULL,
  `especialista_id` INT  NOT NULL,
  `fecha`           DATETIME DEFAULT CURRENT_TIMESTAMP,
  `motivo_consulta` TEXT,
  `diagnostico`     TEXT,
  `tratamiento`     TEXT,
  `observaciones`   TEXT,
  FOREIGN KEY (`cita_id`)         REFERENCES `citas`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`paciente_id`)     REFERENCES `pacientes`(`id`),
  FOREIGN KEY (`especialista_id`) REFERENCES `especialistas`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `consulta_medicamentos` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `consulta_id`    INT NOT NULL,
  `medicamento_id` INT NOT NULL,
  `dosis`          VARCHAR(100),
  `frecuencia`     VARCHAR(100),
  `duracion`       VARCHAR(100),
  FOREIGN KEY (`consulta_id`)    REFERENCES `consultas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`medicamento_id`) REFERENCES `medicamentos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `horarios_especialista` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `especialistaId` INT        NOT NULL,
  `dia_semana`     TINYINT(1) DEFAULT NULL,
  `hora_inicio`    TIME       DEFAULT NULL,
  `hora_fin`       TIME       DEFAULT NULL,
  FOREIGN KEY (`especialistaId`) REFERENCES `especialistas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Datos por defecto
-- ------------------------------------------------------------

-- Roles de sistema
INSERT IGNORE INTO `roles` (`nombre`, `es_sistema`) VALUES
  ('admin',   1),
  ('usuario', 1),
  ('medico',  1);

-- Status de cita
INSERT IGNORE INTO `status_cita` (`nombre`, `color`) VALUES
  ('Pendiente',  'warning'),
  ('Confirmada', 'info'),
  ('Completada', 'success'),
  ('Cancelada',  'danger');

-- Tipos de antecedente
INSERT IGNORE INTO `tipo_antecedente` (`nombre`, `color`) VALUES
  ('Personal',      'primary'),
  ('Familiar',      'secondary'),
  ('Quirurgico',    'danger'),
  ('Alergico',      'warning'),
  ('Farmacologico', 'info');

-- Especialidades
INSERT IGNORE INTO `especialidades` (`nombre`, `descripcion`) VALUES
  ('Medicina General',           'Atención médica general'),
  ('Ginecología y Obstetricia',  'Especialista en salud femenina y control prenatal'),
  ('Traumatología y Ortopedia',  'Diagnóstico y tratamiento del sistema músculo-esquelético'),
  ('Fisioterapia y Rehabilitación', 'Recuperación funcional y terapias complementarias'),
  ('Ozonoterapia',               'Terapias de bienestar y medicina integrativa');

-- Medicamentos
INSERT IGNORE INTO `medicamentos` (`nombre`, `descripcion`, `dosis_sugerida`, `activo`) VALUES
  ('Ibuprofeno',     'Antiinflamatorio no esteroideo (AINE)',                   '400mg cada 8h',                        1),
  ('Paracetamol',    'Analgésico y antipirético',                               '500mg cada 6h',                        1),
  ('Amoxicilina',    'Antibiótico de amplio espectro',                          '500mg cada 8h por 7 días',             1),
  ('Metformina',     'Antidiabético oral para diabetes tipo 2',                 '850mg cada 12h con alimentos',         1),
  ('Atorvastatina',  'Estatina para reducir el colesterol',                     '20mg una vez al día',                  1),
  ('Losartán',       'Antihipertensivo, bloqueador de angiotensina II',         '50mg cada 24h',                        1),
  ('Omeprazol',      'Inhibidor de la bomba de protones (gastritis, reflujo)',  '20mg en ayunas',                       1),
  ('Loratadina',     'Antihistamínico para alergias',                           '10mg cada 24h',                        1),
  ('Diclofenaco',    'Antiinflamatorio y analgésico',                           '50mg cada 8h con alimentos',           1),
  ('Azitromicina',   'Antibiótico macrólido de amplio espectro',               '500mg una vez al día por 3 días',      1),
  ('Prednisona',     'Corticosteroide antiinflamatorio',                        '5-60mg/día según indicación médica',   1),
  ('Ranitidina',     'Antiulceroso, reduce la producción de ácido gástrico',   '150mg cada 12h',                       1),
  ('Metronidazol',   'Antibiótico y antiparasitario',                           '500mg cada 8h por 7 días',             1),
  ('Clonazepam',     'Benzodiazepina ansiolítica y anticonvulsivante',          '0.5mg cada 12h',                       1),
  ('Vitamina D3',    'Suplemento vitamínico para huesos e inmunidad',           '1000 UI una vez al día',               1),
  ('Ciprofloxacino', 'Antibiótico fluoroquinolona de amplio espectro',         '500mg cada 12h por 7 días',            1),
  ('Salbutamol',     'Broncodilatador para asma y EPOC',                       '2 inhalaciones cada 4-6h según necesidad', 1);

-- ------------------------------------------------------------
-- Usuario administrador
-- Contraseña: admin123  (hash bcrypt)
-- ------------------------------------------------------------
INSERT IGNORE INTO `usuarios` (`nombre`, `email`, `password`, `rol_id`, `activo`) VALUES
  ('Administrador', 'admin@clinica.com',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
