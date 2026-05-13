


DROP DATABASE IF EXISTS `bd_ozonovital`;
CREATE DATABASE IF NOT EXISTS `bd_ozonovital` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bd_ozonovital`;

DROP TABLE IF EXISTS `especialidad`;
CREATE TABLE IF NOT EXISTS `especialidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `estatus_cita`;
CREATE TABLE IF NOT EXISTS `estatus_cita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla bd_ozonovital.estatus_cita: ~5 rows (aproximadamente)
INSERT INTO `estatus_cita` (`id`, `nombre`) VALUES
	(4, 'Pendiente'),
	(5, 'Confirmada'),
	(6, 'Cancelada'),
	(7, 'Programada'),
	(8, 'Completada');

DROP TABLE IF EXISTS `medicamento`;
CREATE TABLE IF NOT EXISTS `medicamento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rol` (`id`, `nombre`) VALUES
	(1, 'Administrador'),
	(2, 'Invitado');


DROP TABLE IF EXISTS `tipo_antecedente`;
CREATE TABLE IF NOT EXISTS `tipo_antecedente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_ROL` (`rol_id`) USING BTREE,
  CONSTRAINT `FK_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `login`, `password_hash`, `rol_id`, `status`) VALUES
	(1, 'admin@local.com', '$2y$10$Ytj7ygxUSpDuaqVV27pUtOkvoWndrXxP6LztwcLO5Wyu6tKYcTUGO', 1, 0);
	
DROP TABLE IF EXISTS `especialista`;
CREATE TABLE IF NOT EXISTS `especialista` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  `especialidad_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_especialista_especialidad` (`especialidad_id`),
  CONSTRAINT `FK_especialista_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `horario`;
CREATE TABLE IF NOT EXISTS `horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `especialistaId` int(11) NOT NULL,
  `dia_semana` tinyint(1) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_horario_especialista` (`especialistaId`),
  CONSTRAINT `FK_horario_especialista` FOREIGN KEY (`especialistaId`) REFERENCES `especialista` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `paciente`;
CREATE TABLE IF NOT EXISTS `paciente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  `cedula` int(11) NOT NULL,
  `fecha_nacimiento` datetime NOT NULL,
  `usuario_id` int(11) DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_paciente_usuarios` (`usuario_id`),
  CONSTRAINT `FK_paciente_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cita`;
CREATE TABLE IF NOT EXISTS `cita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `especialista_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  `nota` varchar(200) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_Paciente` (`paciente_id`) USING BTREE,
  KEY `FK_Especialista` (`especialista_id`) USING BTREE,
  KEY `FK_cita_estatus_cita` (`status_id`),
  CONSTRAINT `FK_Especialista` FOREIGN KEY (`especialista_id`) REFERENCES `especialista` (`id`),
  CONSTRAINT `FK_Paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`),
  CONSTRAINT `FK_cita_estatus_cita` FOREIGN KEY (`status_id`) REFERENCES `estatus_cita` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `antecedentes_medicos`;
CREATE TABLE IF NOT EXISTS `antecedentes_medicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL,
  `notas_adicionales` text DEFAULT NULL,
  `tipo_antecedente_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_antecedentes_medicos_paciente` (`paciente_id`),
  KEY `FK_antecedentes_medicos_tipo_antecedente` (`tipo_antecedente_id`),
  CONSTRAINT `FK_antecedentes_medicos_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_antecedentes_medicos_tipo_antecedente` FOREIGN KEY (`tipo_antecedente_id`) REFERENCES `tipo_antecedente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `historia_clinica`;
CREATE TABLE IF NOT EXISTS `historia_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cita_id` int(11) NOT NULL,
  `motivo_consulta` text NOT NULL,
  `tratamiento` text NOT NULL,
  `observaciones` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_historia_clinica_cita` (`cita_id`),
  CONSTRAINT `FK_historia_clinica_cita` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `diagnostico`;
CREATE TABLE IF NOT EXISTS `diagnostico` (
  `id` int(11) NOT NULL,
  `cita_id` int(11) NOT NULL,
  `motivo_consulta` text NOT NULL,
  `tratamiento` text NOT NULL,
  `indicaciones` text DEFAULT NULL,
  `notas_adicionales` text DEFAULT NULL,
  `medicamento_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_diagnostico_cita` (`cita_id`),
  KEY `FK_diagnostico_medicamento` (`medicamento_id`),
  CONSTRAINT `FK_diagnostico_cita` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_diagnostico_medicamento` FOREIGN KEY (`medicamento_id`) REFERENCES `medicamento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
