-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.13.0.7147
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bd_ozonovital
DROP DATABASE IF EXISTS `bd_ozonovital`;
CREATE DATABASE IF NOT EXISTS `bd_ozonovital` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bd_ozonovital`;

-- Dumping structure for table bd_ozonovital.rol
DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.rol: ~2 rows (approximately)
INSERT INTO `rol` (`id`, `nombre`) VALUES
	(1, 'Administrador'),
	(2, 'Invitado');

-- Dumping structure for table bd_ozonovital.usuarios

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

-- Dumping data for table bd_ozonovital.usuarios: ~1 rows (approximately)
INSERT INTO `usuarios` (`id`, `login`, `password_hash`, `rol_id`, `status`) VALUES
	(1, 'admin@local.com', '$2y$10$Ytj7ygxUSpDuaqVV27pUtOkvoWndrXxP6LztwcLO5Wyu6tKYcTUGO', 1, 0);



-- Dumping structure for table bd_ozonovital.especialidad
DROP TABLE IF EXISTS `especialidad`;
CREATE TABLE IF NOT EXISTS `especialidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(75) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.especialidad: ~3 rows (approximately)
INSERT INTO `especialidad` (`id`, `nombre`) VALUES
	(1, 'Ginecologia'),
	(2, 'Cardiologia'),
	(5, 'Pediatria');

-- Dumping structure for table bd_ozonovital.especialista
DROP TABLE IF EXISTS `especialista`;
CREATE TABLE IF NOT EXISTS `especialista` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` text NOT NULL,
  `especialidad_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_especialista_especialidad` (`especialidad_id`),
  CONSTRAINT `FK_especialista_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.especialista: ~0 rows (approximately)

-- Dumping structure for table bd_ozonovital.paciente
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.paciente: ~0 rows (approximately)




-- Dumping structure for table bd_ozonovital.antecedentes_medicos
DROP TABLE IF EXISTS `antecedentes_medicos`;
CREATE TABLE IF NOT EXISTS `antecedentes_medicos` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo_antecedente` enum('Familiar','Personal Patológico','Personal No Patológico','Quirúrgico','Alérgico') DEFAULT NULL,
  `notas_adicionales` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_antecedentes_medicos_paciente` (`paciente_id`),
  CONSTRAINT `FK_antecedentes_medicos_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.antecedentes_medicos: ~0 rows (approximately)

-- Dumping structure for table bd_ozonovital.cita
DROP TABLE IF EXISTS `cita`;
CREATE TABLE IF NOT EXISTS `cita` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `especialista_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` enum('Programada','Confirmada','En Espera','En Consulta','Completada','Cancelada','No Asistió','Reprogramada') DEFAULT NULL,
  `nota` varchar(200) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `FK_Paciente` (`paciente_id`) USING BTREE,
  KEY `FK_Especialista` (`especialista_id`) USING BTREE,
  CONSTRAINT `FK_Especialista` FOREIGN KEY (`especialista_id`) REFERENCES `especialista` (`id`),
  CONSTRAINT `FK_Paciente` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.cita: ~0 rows (approximately)




-- Dumping structure for table bd_ozonovital.historia_clinica
DROP TABLE IF EXISTS `historia_clinica`;
CREATE TABLE IF NOT EXISTS `historia_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cita_id` int(11) NOT NULL,
  `motivo_consulta` text NOT NULL,
  `tratamiento` text NOT NULL,
  `observaciones` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_historia_clinica_cita` (`cita_id`),
  CONSTRAINT `FK_historia_clinica_cita` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table bd_ozonovital.historia_clinica: ~0 rows (approximately)





/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
