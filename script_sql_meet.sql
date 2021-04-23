-- --------------------------------------------------------
-- Host:                         localhost
-- Versión del servidor:         5.7.24 - MySQL Community Server (GPL)
-- SO del servidor:              Win64
-- HeidiSQL Versión:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Volcando estructura de base de datos para meet
CREATE DATABASE IF NOT EXISTS `meet` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `meet`;

-- Volcando estructura para tabla meet.meet_info
CREATE TABLE IF NOT EXISTS `meet_info` (
  `conference_id` char(250) NOT NULL DEFAULT '0',
  `meeting_code` varchar(50) DEFAULT NULL,
  `duration_seconds` int(11) DEFAULT NULL,
  `organizer_email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`conference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla meet.meet_participant
CREATE TABLE IF NOT EXISTS `meet_participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(250) DEFAULT NULL,
  `conference_id` char(250) DEFAULT NULL,
  `device_type` varchar(250) DEFAULT NULL,
  `identifier` varchar(250) DEFAULT NULL,
  `duration_seconds_in_call` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `FK_meet_participant_meet_info` (`conference_id`),
  CONSTRAINT `FK_meet_participant_meet_info` FOREIGN KEY (`conference_id`) REFERENCES `meet_info` (`conference_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3391 DEFAULT CHARSET=latin1;

-- La exportación de datos fue deseleccionada.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
