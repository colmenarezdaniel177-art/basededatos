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
CREATE DATABASE IF NOT EXISTS `ozono_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ozono_db`;

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
  `citas_max_por_dia` int(11) NOT NULL DEFAULT 1,
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

 
INSERT INTO `especialistas` (`nombre`, `apellido`, `especialidad_id`, `telefono`, `email`, `citas_max_por_dia`, `activo`) VALUES
('Carlos', 'Mendoza', 1, '04141111111', 'carlos.mendoza@clinica.com', 8, 1),
('María', 'Rodríguez', 2, '04142222222', 'maria.rodriguez@clinica.com', 6, 1),
('Juan', 'Martínez', 3, '04143333333', 'juan.martinez@clinica.com', 7, 1),
('Ana', 'Gómez', 4, '04144444444', 'ana.gomez@clinica.com', 5, 1),
('Luisa', 'Pérez', 5, '04145555555', 'luisa.perez@clinica.com', 6, 1),
('Pedro', 'Sánchez', 1, '04146666666', 'pedro.sanchez@clinica.com', 8, 1),
('Elena', 'Díaz', 2, '04147777777', 'elena.diaz@clinica.com', 6, 1),
('Miguel', 'Hernández', 3, '04148888888', 'miguel.hernandez@clinica.com', 7, 1),
('Carmen', 'Álvarez', 4, '04149999999', 'carmen.alvarez@clinica.com', 5, 1),
('Jorge', 'Torres', 5, '04141010101', 'jorge.torres@clinica.com', 6, 1),
('Sofía', 'Ramírez', 1, '04141112131', 'sofia.ramirez@clinica.com', 8, 1),
('Diego', 'Flores', 2, '04141415161', 'diego.flespecialistasores@clinica.com', 6, 1),
('Laura', 'Benítez', 3, '04141718191', 'laura.benitez@clinica.com', 7, 1),
('Luis', 'Medina', 4, '04142021222', 'luis.medina@clinica.com', 5, 1),
('Clara', 'Castillo', 5, '04142324252', 'clara.castillo@clinica.com', 6, 1),
('Andrés', 'Silva', 1, '04142627282', 'andres.silva@clinica.com', 8, 1),
('Lucía', 'Castro', 2, '04142930313', 'lucia.castro@clinica.com', 6, 1),
('Gabriel', 'Ortiz', 3, '04143233343', 'gabriel.ortiz@clinica.com', 7, 1),
('Patricia', 'Blanco', 4, '04143536373', 'patricia.blanco@clinica.com', 5, 1),
('Roberto', 'Rubio', 5, '04143839404', 'roberto.rubio@clinica.com', 6, 1),
('Gabriela', 'Morales', 1, '04144142434', 'gabriela.morales@clinica.com', 8, 1),
('Ricardo', 'Ortega', 2, '04144445464', 'ricardo.ortega@clinica.com', 6, 1),
('Daniela', 'Núñez', 3, '04144748494', 'daniela.nunez@clinica.com', 7, 1),
('Fernando', 'Molina', 4, '04145051525', 'fernando.molina@clinica.com', 5, 1),
('Isabella', 'Delgado', 5, '04145354555', 'isabella.delgado@clinica.com', 6, 1),
('Alejandro', 'Ríos', 1, '04145657585', 'alejandro.rios@clinica.com', 8, 1),
('Camila', 'Suárez', 2, '04145960616', 'camila.suarez@clinica.com', 6, 1),
('Manuel', 'Salazar', 3, '04146263646', 'manuel.salazar@clinica.com', 7, 1),
('Valeria', 'Guerrero', 4, '04146566676', 'valeria.guerrero@clinica.com', 5, 1),
('Francisco', 'Cabrera', 5, '04146869707', 'francisco.cabrera@clinica.com', 6, 1);

INSERT INTO `horarios_especialista` (`especialistaId`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
(1, 1, '08:00:00', '12:00:00'),
(2, 1, '14:00:00', '18:00:00'),
(3, 2, '08:00:00', '13:00:00'),
(4, 2, '13:00:00', '17:00:00'),
(5, 3, '09:00:00', '14:00:00'),
(6, 3, '14:00:00', '19:00:00'),
(7, 4, '08:00:00', '12:00:00'),
(8, 4, '13:00:00', '18:00:00'),
(9, 5, '08:00:00', '12:00:00'),
(10, 5, '14:00:00', '18:00:00'),
(11, 1, '08:00:00', '12:00:00'),
(12, 1, '14:00:00', '18:00:00'),
(13, 2, '08:00:00', '13:00:00'),
(14, 2, '13:00:00', '17:00:00'),
(15, 3, '09:00:00', '14:00:00'),
(16, 3, '14:00:00', '19:00:00'),
(17, 4, '08:00:00', '12:00:00'),
(18, 4, '13:00:00', '18:00:00'),
(19, 5, '08:00:00', '12:00:00'),
(20, 5, '14:00:00', '18:00:00'),
(21, 1, '08:00:00', '12:00:00'),
(22, 1, '14:00:00', '18:00:00'),
(23, 2, '08:00:00', '13:00:00'),
(24, 2, '13:00:00', '17:00:00'),
(25, 3, '09:00:00', '14:00:00'),
(26, 3, '14:00:00', '19:00:00'),
(27, 4, '08:00:00', '12:00:00'),
(28, 4, '13:00:00', '18:00:00'),
(29, 5, '08:00:00', '12:00:00'),
(30, 5, '14:00:00', '18:00:00');

INSERT INTO usuarios (nombre, email, password, rol_id, especialista_id, activo) VALUES 
('Carlos Mendoza', 'carlos.mendoza@clinica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 1, 1),
('María Rodríguez', 'maria.rodriguez@clinica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 2, 1),
('Juan Martínez', 'juan.martinez@clinica.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 3, 1),
('Juan Pérez', 'juan.perez@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 1),
('María Gómez', 'maria.gomez@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 1);

INSERT INTO `pacientes` (`usuario_id`, `nombre`, `apellido`, `cedula`, `fecha_nacimiento`, `genero`, `telefono`, `email`, `direccion`) VALUES
(NULL, 'Juan', 'Pérez', '15234567', '1985-04-12', 'M', '04245550101', 'juan.perez@mail.com', 'Av. Bolivar, Edif. Central, Ap. 4B'),
(NULL, 'María', 'Gómez', '18456123', '1990-08-22', 'F', '04245550102', 'maria.gomez@mail.com', 'Calle Los Cerezos, Casa Nro 12'),
(NULL, 'Pedro', 'Martínez', '12789456', '1978-01-05', 'M', '04245550103', 'pedro.martinez@mail.com', 'Urb. La Florida, Av. 3 con Calle 2'),
(NULL, 'Ana', 'Rodríguez', '20123789', '1995-11-30', 'F', '04245550104', 'ana.rodriguez@mail.com', 'Residencias El Sol, Torre B, Piso 8'),
(NULL, 'Luis', 'Fernández', '14567890', '1982-06-15', 'M', '04245550105', 'luis.fernandez@mail.com', 'Sector El Centro, Calle Comercio'),
(NULL, 'Carmen', 'Sánchez', '16890123', '1988-09-09', 'F', '04245550106', 'carmen.sanchez@mail.com', 'Urb. Las Mercedes, Av. Principal'),
(4, 'José', 'López', '11234567', '1975-03-25', 'M', '04245550107', 'jose.lopez@mail.com', 'Barrio Ajuro, Calle Principal #45'),
(4, 'Elena', 'Díaz', '22345678', '1998-05-14', 'F', '04245550108', 'elena.diaz@mail.com', 'Conjunto Res. El Parque, Casa 7'),
(NULL, 'Carlos', 'González', '9876543', '1968-12-20', 'M', '04245550109', 'carlos.gonzalez@mail.com', 'Av. Universidad, Res. Alba, Piso 2'),
(NULL, 'Martha', 'Álvarez', '13456789', '1980-07-07', 'F', '04245550110', 'martha.alvarez@mail.com', 'Calle Sucre, Nro 89-B'),
(NULL, 'Jorge', 'Hernández', '17567891', '1989-02-17', 'M', '04245550111', 'jorge.hernandez@mail.com', 'Urb. Indio Manaure, Manzana D'),
(NULL, 'Lucía', 'Torres', '21789456', '1994-10-02', 'F', '04245550112', 'lucia.torres@mail.com', 'Av. Lara, C.C. Paseo, Oficina 12'),
(NULL, 'Andrés', 'Ramírez', '19123456', '1992-04-28', 'M', '04245550113', 'andres.ramirez@mail.com', 'Urb. El Parral, Av. Los Próceres'),
(NULL, 'Sofia', 'Flores', '25456123', '2001-07-19', 'F', '04245550114', 'sofia.flores@mail.com', 'Calle Carabobo, Edif. Don Bosco'),
(NULL, 'Diego', 'Benítez', '10789456', '1972-11-11', 'M', '04245550115', 'diego.benitez@mail.com', 'Vereda 4, Sector Sabana Grande'),
(NULL, 'Laura', 'Medina', '23123789', '1997-01-24', 'F', '04245550116', 'laura.medina@mail.com', 'Res. Las Acacias, Torre 1, Apt 1A'),
(NULL, 'Gabriel', 'Castillo', '15567890', '1984-08-05', 'M', '04245550117', 'gabriel.castillo@mail.com', 'Av. Francisco de Miranda, Chacao'),
(5, 'Patricia', 'Silva', '18890123', '1991-03-12', 'F', '04245550118', 'patricia.silva@mail.com', 'Urb. Prebo, Calle 130, Casa 4'),
(5, 'Roberto', 'Castro', '12234567', '1977-05-30', 'M', '04245550119', 'roberto.castro@mail.com', 'Calle Falcón, Sector Pueblo Nuevo'),
(5, 'Clara', 'Ortiz', '24345678', '2000-09-15', 'F', '04245550120', 'clara.ortiz@mail.com', 'Av. Las Delicias, Urb. El Bosque'),
(NULL, 'Francisco', 'Blanco', '8876543', '1965-02-14', 'M', '04245550121', 'francisco.blanco@mail.com', 'Calle Miranda, Res. El Ancla'),
(NULL, 'Daniela', 'Rubio', '16456789', '1986-12-01', 'F', '04245550122', 'daniela.rubio@mail.com', 'Urb. La Viña, Av. Carabobo'),
(NULL, 'Ricardo', 'Morales', '20567891', '1993-06-18', 'M', '04245550123', 'ricardo.morales@mail.com', 'Sector La Popa, Calle J-4'),
(NULL, 'Gabriela', 'Ortega', '14789456', '1981-10-22', 'F', '04245550124', 'gabriela.ortega@mail.com', 'Av. Intercomunal, Conjunto Res. Oasis'),
(NULL, 'Fernando', 'Núñez', '17123456', '1987-03-08', 'M', '04245550125', 'fernando.nunez@mail.com', 'Urb. El Manzano, Calle Los Pinos'),
(NULL, 'Isabella', 'Molina', '26456123', '2002-11-05', 'F', '04245550126', 'isabella.molina@mail.com', 'Calle Piar, Edif. San José, Piso 3'),
(NULL, 'Alejandro', 'Delgado', '11789456', '1974-04-16', 'M', '04245550127', 'alejandro.delgado@mail.com', 'Urb. Nueva Segovia, Carrera 1'),
(NULL, 'Camila', 'Ríos', '22123789', '1996-07-27', 'F', '04245550128', 'camila.rios@mail.com', 'Res. Vista Hermosa, Torre Unique'),
(NULL, 'Manuel', 'Suárez', '13567890', '1979-09-13', 'M', '04245550129', 'manuel.suarez@mail.com', 'Calle Vargas, Nro 120'),
(NULL, 'Valeria', 'Salazar', '19890123', '1992-12-25', 'F', '04245550130', 'valeria.salazar@mail.com', 'Urb. El Trigal, Av. Atlántico');

INSERT INTO `antecedentes` (`paciente_id`, `tipo_id`, `descripcion`, `fecha`) VALUES
(1, 1, 'Hipertensión arterial controlada con Losartán', '2020-05-10'),
(2, 4, 'Alergia severa a la Penicilina', '2015-08-20'),
(3, 3, 'Apendicectomía sin complicaciones', '2012-03-14'),
(4, 2, 'Madre con antecedentes de Cáncer de Mama', '2021-01-15'),
(5, 5, 'Uso crónico de Omeprazol por gastritis', '2023-02-11'),
(6, 1, 'Diabetes Mellitus Tipo 2 en tratamiento', '2019-11-04'),
(7, 3, 'Cirugía de hernia inguinal izquierda', '2018-06-25'),
(8, 4, 'Alergia al Ibuprofeno y AINES', '2017-09-12'),
(9, 2, 'Padre fallecido por Infarto Agudo al Miocardio', '2010-04-03'),
(10, 1, 'Asma bronquial intermitente', '2005-08-14'),
(11, 3, 'Colecistectomía laparoscópica', '2022-10-30'),
(12, 4, 'Rinitis alérgica estacional', '2016-05-05'),
(13, 2, 'Hermano con Diabetes Tipo 1', '2020-07-22'),
(14, 5, 'Consumo de suplementos de Vitamina D3', '2024-01-10'),
(15, 1, 'Hipotiroidismo en tratamiento con levotiroxina', '2014-12-01'),
(16, 3, 'Cesárea segmentaria anterior', '2021-05-18'),
(17, 1, 'Dislipidemia mixta en control', '2022-03-19'),
(18, 4, 'Alergia al polen y polvo', '2013-04-11'),
(19, 3, 'Fractura de fémur izquierdo con material de osteosíntesis', '2015-11-27'),
(20, 2, 'Abuela materna con Alzheimer', '2019-02-14'),
(21, 1, 'Gastritis crónica erosiva', '2023-06-02'),
(22, 3, 'Amigdalectomía en la infancia', '2002-07-09'),
(23, 4, 'Alergia a los mariscos', '2011-10-05'),
(24, 2, 'Padre con Hipertensión Arterial', '2018-12-12'),
(25, 5, 'Tratamiento previo con Clonazepam por ansiedad', '2023-08-24'),
(26, 1, 'Migraña crónica con aura', '2016-01-20'),
(27, 3, 'Artroscopia de rodilla derecha', '2020-09-02'),
(28, 4, 'Dermatitis por contacto (níquel)', '2018-04-15'),
(29, 2, 'Madre con Hipotiroidismo', '2022-11-11'),
(30, 1, 'Obesidad Grado I', '2023-05-30');


INSERT INTO `citas` (`paciente_id`, `especialista_id`, `usuario_id`, `fecha`, `motivo`, `status_id`, `estado`) VALUES
(1, 1, 1, '2026-05-10', 'Control anual de rutina', 3, 'Completada'),
(2, 2, 1, '2026-05-11', 'Evaluación ginecológica y eco', 3, 'Completada'),
(3, 3, 1, '2026-05-11', 'Dolor severo en rodilla derecha', 3, 'Completada'),
(4, 4, 1, '2026-05-12', 'Sesión de rehabilitación lumbar', 3, 'Completada'),
(5, 5, 1, '2026-05-12', 'Evaluación para terapia con ozono', 3, 'Completada'),
(6, 6, 1, '2026-05-13', 'Malestar general y fiebre alta', 3, 'Completada'),
(7, 7, 1, '2026-05-13', 'Control prenatal semana 24', 3, 'Completada'),
(8, 8, 1, '2026-05-14', 'Esguince de tobillo izquierdo', 3, 'Completada'),
(9, 9, 1, '2026-05-14', 'Fisioterapia post-operatoria', 3, 'Completada'),
(10, 10, 1, '2026-05-15', 'Tratamiento de dolor crónico', 3, 'Completada'),
(11, 11, 1, '2026-05-15', 'Chequeo de presión arterial', 3, 'Completada'),
(12, 12, 1, '2026-05-18', 'Consulta ginecológica de rutina', 3, 'Completada'),
(13, 13, 1, '2026-05-18', 'Dolor en hombro izquierdo', 3, 'Completada'),
(14, 14, 1, '2026-05-19', 'Rehabilitación cervical', 3, 'Completada'),
(15, 15, 1, '2026-05-19', 'Sesión de ozonoterapia revitalizante', 3, 'Completada'),
(16, 16, 1, '2026-05-20', 'Sintomas gripales persistentes', 3, 'Completada'),
(17, 17, 1, '2026-05-20', 'Control post-parto', 3, 'Completada'),
(18, 18, 1, '2026-05-21', 'Evaluación por dolor de columna', 3, 'Completada'),
(19, 19, 1, '2026-05-21', 'Fisioterapia de mano', 3, 'Completada'),
(20, 20, 1, '2026-05-22', 'Manejo de dolor por artrosis', 3, 'Completada'),
(21, 21, 1, '2026-05-22', 'Revisión de exámenes de laboratorio', 3, 'Completada'),
(22, 22, 1, '2026-05-25', 'Irregularidad menstrual', 3, 'Completada'),
(23, 23, 1, '2026-05-25', 'Sospecha de fractura en dedo', 3, 'Completada'),
(24, 24, 1, '2026-05-26', 'Masaje terapéutico y descarga', 3, 'Completada'),
(25, 25, 1, '2026-05-26', 'Ozonoterapia para hernia discal', 3, 'Completada'),
(26, 26, 1, '2026-05-27', 'Infección urinaria aparente', 3, 'Completada'),
(27, 27, 1, '2026-05-27', 'Control de miomatosis uterina', 3, 'Completada'),
(28, 28, 1, '2026-05-28', 'Dolor muscular generalizado', 3, 'Completada'),
(29, 29, 1, '2026-05-28', 'Rehabilitación post-Ictus', 3, 'Completada'),
(30, 30, 1, '2026-05-28', 'Ozonoterapia sistémica', 3, 'Completada');

INSERT INTO `consultas` (`cita_id`, `paciente_id`, `especialista_id`, `fecha`, `motivo_consulta`, `diagnostico`, `tratamiento`, `observaciones`) VALUES
(1, 1, 1, '2026-05-10 08:30:00', 'Control anual de rutina', 'Hipertensión esencial estable', 'Continuar Losartán 50mg diario', 'Paciente asintomático. Próxima cita en 6 meses.'),
(2, 2, 2, '2026-05-11 14:15:00', 'Evaluación ginecológica y eco', 'Control ginecológico normal', 'Ninguno', 'Eco pélvico sin alteraciones detectables.'),
(3, 3, 3, '2026-05-11 09:00:00', 'Dolor severo en rodilla derecha', 'Gonalgia por probable lesión de meniscos', 'Analgesia e indicación de Resonancia', 'Reposo físico por 10 días.'),
(4, 4, 4, '2026-05-12 13:00:00', 'Sesión de rehabilitación lumbar', 'Lumbalgia mecánica crónica', 'Fisioterapia (TENS, calor, ultrasonido)', 'Mejora leve del rango de movimiento.'),
(5, 5, 5, '2026-05-12 10:00:00', 'Evaluación para terapia con ozono', 'Sindrome de fatiga crónica', 'Autohemoterapia mayor con ozono', 'Paciente tolera bien el primer ciclo.'),
(6, 6, 6, '2026-05-13 11:00:00', 'Malestar general y fiebre alta', 'Faringoamigdalitis bacteriana aguda', 'Antibioticoterapia sistémica', 'Tomar abundantes líquidos, reposo.'),
(7, 7, 7, '2026-05-13 08:30:00', 'Control prenatal semana 24', 'Embarazo cronológicamente de 24 semanas', 'Suplementos de Hierro y Ácido Fólico', 'Frecuencia cardiaca fetal normal, 142 lpm.'),
(8, 8, 8, '2026-05-14 15:30:00', 'Esguince de tobillo izquierdo', 'Esguince de tobillo Grado II', 'Inmovilización con bota y reposo', 'Derivar a fisioterapia en 2 semanas.'),
(9, 9, 9, '2026-05-14 11:30:00', 'Fisioterapia post-operatoria', 'Post-operatorio tardío de rodilla', 'Ejercicios de fortalecimiento muscular', 'Evolución satisfactoria del tendón.'),
(10, 10, 10, '2026-05-15 16:00:00', 'Tratamiento de dolor crónico', 'Fibromialgia severa', 'Insuflación rectal de ozono', 'Refiere disminución del umbral de dolor.'),
(11, 11, 11, '2026-05-15 09:15:00', 'Chequeo de presión arterial', 'Síndrome de bata blanca (PA normal en casa)', 'Monitoreo ambulatorio de PA', 'Cifras tensionales estables en la consulta.'),
(12, 12, 12, '2026-05-18 15:00:00', 'Consulta ginecológica de rutina', 'Salud reproductiva óptima', 'Multivitamínicos', 'Citología tomada hoy.'),
(13, 13, 13, '2026-05-18 10:45:00', 'Dolor en hombro izquierdo', 'Tendinitis del manguito rotador', 'AINEs y calor local', 'Evitar levantar peso.'),
(14, 14, 14, '2026-05-19 14:00:00', 'Rehabilitación cervical', 'Cervicalgia por mala postura', 'Tracción cervical y masoterapia', 'Se enseñan ejercicios ergonómicos.'),
(15, 15, 15, '2026-05-19 12:00:00', 'Sesión de ozonoterapia revitalizante', 'Estrés oxidativo elevado', 'Ozonoterapia endovenosa', 'Reporta mayor energía vital.'),
(16, 16, 16, '2026-05-20 08:00:00', 'Sintomas gripales persistentes', 'Bronquitis aguda no especificada', 'Broncodilatadores e hidratación', 'No amerita antibióticos por ahora.'),
(17, 17, 17, '2026-05-20 10:00:00', 'Control post-parto', 'Puerperio fisiológico tardío', 'Planificación familiar discutida', 'Útero totalmente involucionado.'),
(18, 18, 18, '2026-05-21 11:15:00', 'Evaluación por dolor de columna', 'Escoliosis leve del adulto', 'Remisión a RX de columna total', 'Dolor tolerable.'),
(19, 19, 19, '2026-05-21 16:30:00', 'Fisioterapia de mano', 'Rigidez articular post-inmovilización', 'Parafina y movilización pasiva', 'Gana 10 grados de flexión.'),
(20, 20, 20, '2026-05-22 14:00:00', 'Manejo de dolor por artrosis', 'Osteoartrosis de cadera bilateral', 'Infiltración local periarticular con ozono', 'Disminución inmediata del dolor al caminar.'),
(21, 21, 21, '2026-05-22 09:45:00', 'Revisión de exámenes de laboratorio', 'Hipercolesterolemia pura', 'Tratamiento con estatinas y dieta', 'LDL elevado en 180 mg/dL.'),
(22, 22, 22, '2026-05-25 17:00:00', 'Irregularidad menstrual', 'Sindrome de Ovarios Poliquísticos (SOP)', 'Tratamiento hormonal dirigido', 'Se solicita perfil hormonal completo.'),
(23, 23, 23, '2026-05-25 08:30:00', 'Sospecha de fractura en dedo', 'Contusión severa de falange (sin fractura)', 'Inmovilización sindactilia', 'Crioterapia por 48 horas.'),
(24, 24, 24, '2026-05-26 13:30:00', 'Masaje terapéutico y descarga', 'Contractura muscular en trapecios', 'Terapia manual descontracturante', 'Alta tensión por estrés laboral.'),
(25, 25, 25, '2026-05-26 11:00:00', 'Ozonoterapia para hernia discal', 'Hernia discal L4-L5 extruida', 'Infiltración paravertebral con ozono', 'Excelente tolerancia al procedimiento.'),
(26, 26, 26, '2026-05-27 10:15:00', 'Infección urinaria aparente', 'Infección de vías urinarias bajas (Cistitis)', 'Antibioterapia empírica', 'Se solicita urocultivo de control.'),
(27, 27, 27, '2026-05-27 16:00:00', 'Control de miomatosis uterina', 'Miomatosis uterina intramural', 'Conducta expectante', 'Miomas estables en tamaño según eco.'),
(28, 28, 28, '2026-05-28 09:00:00', 'Dolor muscular generalizado', 'Mialgia tensional', 'Ejercicios de estiramiento asistidos', 'Mejoría tras la sesión.'),
(29, 29, 29, '2026-05-28 15:15:00', 'Rehabilitación post-Ictus', 'Hemiparesia izquierda residual', 'Reeducación de la marcha y equilibrio', 'Progreso lento pero constante.'),
(30, 30, 30, '2026-05-28 12:30:00', 'Ozonoterapia sistémica', 'Envejecimiento celular prematuro', 'Ozonoterapia por insuflación', 'Tratamiento preventivo antiedad.');

INSERT INTO `consulta_medicamentos` (`consulta_id`, `medicamento_id`, `dosis`, `frecuencia`, `duracion`) VALUES
(1, 6, '50mg', 'Cada 24 horas', 'Continuo'),
(3, 1, '400mg', 'Cada 8 horas', '5 días'),
(4, 9, '50mg', 'Cada 12 horas con alimentos', '7 días'),
(6, 3, '500mg', 'Cada 8 horas', '7 días'),
(6, 2, '500mg', 'Cada 6 horas si hay fiebre', '3 días'),
(8, 1, '400mg', 'Cada 8 horas', '4 días'),
(11, 6, '25mg', 'Cada 24 horas en la mañana', 'Evaluación continua'),
(13, 9, '50mg', 'Cada 8 horas', '5 días'),
(16, 17, '2 inhalaciones', 'Cada 6 horas', '5 días'),
(16, 2, '500mg', 'Cada 8 horas', '3 días'),
(21, 5, '20mg', 'Una vez al día (noche)', '3 meses'),
(23, 2, '500mg', 'Cada 6 horas si hay dolor', '3 días'),
(26, 16, '500mg', 'Cada 12 horas', '7 días'),
(26, 2, '500mg', 'Cada 8 horas si hay molestia', '3 días'),
(1, 15, '1000 UI', 'Una vez al día', '30 días'),
(5, 15, '1000 UI', 'Una vez al día', '60 días'),
(7, 15, '1000 UI', 'Una vez al día', 'Toda la gestación'),
(10, 14, '0.25mg', 'Cada 24 horas (antes de dormir)', '15 días'),
(14, 1, '400mg', 'Cada 12 horas', '3 días'),
(15, 15, '1000 UI', 'Una vez al día', '30 días'),
(18, 9, '50mg', 'Cada 8 horas si amerita', '5 días'),
(20, 1, '400mg', 'Cada 8 horas', '5 días'),
(22, 11, '5mg', 'Cada 24 horas', '10 días'),
(24, 2, '500mg', 'Cada 8 horas', '3 días'),
(25, 14, '0.5mg', 'Cada 24 horas en la noche', '7 días'),
(27, 2, '500mg', 'Cada 8 horas si hay dolor menstrual', '3 días'),
(28, 1, '400mg', 'Cada 8 horas', '3 días'),
(29, 15, '1000 UI', 'Una vez al día', '90 días'),
(30, 15, '1000 UI', 'Una vez al día', '30 días'),
(12, 15, '1000 UI', 'Una vez al día', '30 días');
SET FOREIGN_KEY_CHECKS = 1;
