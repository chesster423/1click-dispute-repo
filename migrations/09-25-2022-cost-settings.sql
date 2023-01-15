-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.33 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table 1clickdispute.cost_settings
CREATE TABLE IF NOT EXISTS `cost_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `initialCost` float NOT NULL,
  `costPerPage` float NOT NULL,
  `certifiedCost` float NOT NULL,
  `additionalCost` float NOT NULL,
  `updatedAt` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table 1clickdispute.cost_settings: ~0 rows (approximately)
DELETE FROM `cost_settings`;
/*!40000 ALTER TABLE `cost_settings` DISABLE KEYS */;
INSERT INTO `cost_settings` (`id`, `initialCost`, `costPerPage`, `certifiedCost`, `additionalCost`, `updatedAt`) VALUES
	(1, 0.1, 0.1, 6, 2.5, '2022-09-25 18:56:57');
/*!40000 ALTER TABLE `cost_settings` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
