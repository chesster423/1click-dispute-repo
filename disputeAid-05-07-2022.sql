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

-- Dumping structure for table 1clickdispute.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ' ',
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ' ',
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ' ',
  `passwordToken` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `passwordTokenExpire` datetime DEFAULT NULL,
  `picture` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user.png',
  `createdOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table 1clickdispute.admins: ~2 rows (approximately)
DELETE FROM `admins`;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `passwordToken`, `passwordTokenExpire`, `picture`, `createdOn`) VALUES
	(1, 'Senaid Bacinovic', 'senaidbacinovic@gmail.com', '$2y$10$MdhJKx2WrqmLMJf3KcopV.0Y0eTT2ohg6.TLVuiB8esDhiHbVak.m', '', '2019-06-07 16:09:56', '9jSY92Pzjnxzrmin8CLH19QO.png', '2019-02-04 11:47:02'),
	(3, 'Dominique Brown', 'info@sdcapitalholdingsllc.com', '$2y$10$b2AkNhKdv.goSsBSd2.VJeA8514jIhEzyKjsO5YzXmnrM5xMuvh6i', '', NULL, 'user.png', '2020-08-13 14:53:08');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.credits_history
CREATE TABLE IF NOT EXISTS `credits_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `creditsAmount` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `actionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table 1clickdispute.credits_history: ~17 rows (approximately)
DELETE FROM `credits_history`;
/*!40000 ALTER TABLE `credits_history` DISABLE KEYS */;
INSERT INTO `credits_history` (`id`, `userID`, `creditsAmount`, `item`, `actionDate`) VALUES
	(4, 157, 6, 'certified letter printed', '2022-01-22 08:17:33'),
	(5, 157, 1, 'standard letter printed', '2022-01-22 08:17:57'),
	(6, 157, 1, 'standard letter printed', '2022-02-07 20:47:44'),
	(7, 157, 1, 'standard letter printed', '2022-02-20 15:53:32'),
	(8, 157, 1, 'standard letter printed', '2022-02-20 15:56:03'),
	(9, 157, 8, 'certified letter printed', '2022-03-02 19:47:23'),
	(10, 157, 8, 'certified letter printed', '2022-03-02 19:51:55'),
	(11, 157, 4, 'certified letter printed', '2022-03-07 18:17:51'),
	(12, 157, 4, 'certified letter printed', '2022-03-07 18:20:12'),
	(13, 157, 4, 'certified letter printed', '2022-03-07 20:32:59'),
	(14, 157, 4, 'certified letter printed', '2022-03-07 20:33:50'),
	(15, 157, 4, 'certified letter printed', '2022-03-07 20:43:58'),
	(16, 157, 4, 'certified letter printed', '2022-03-07 20:45:21'),
	(17, 157, 4, 'certified letter printed', '2022-03-07 20:45:37'),
	(18, 157, 4, 'certified letter printed', '2022-03-07 20:47:34'),
	(19, 157, 4, 'certified letter printed', '2022-03-07 20:48:19'),
	(20, 157, 1, 'standard letter printed', '2022-03-07 20:48:30');
/*!40000 ALTER TABLE `credits_history` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.files_log
CREATE TABLE IF NOT EXISTS `files_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table 1clickdispute.files_log: ~0 rows (approximately)
DELETE FROM `files_log`;
/*!40000 ALTER TABLE `files_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `files_log` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.mail_logs
CREATE TABLE IF NOT EXISTS `mail_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_code` varchar(255) NOT NULL,
  `mail_system` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `transaction_name` varchar(255) NOT NULL,
  `success` tinyint(4) NOT NULL DEFAULT '0',
  `log_msg` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_fk` (`user_id`),
  CONSTRAINT `user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table 1clickdispute.mail_logs: ~0 rows (approximately)
DELETE FROM `mail_logs`;
/*!40000 ALTER TABLE `mail_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_logs` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.purchase_history
CREATE TABLE IF NOT EXISTS `purchase_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `creditsAmount` int(11) NOT NULL,
  `receiptUrl` text NOT NULL,
  `purchaseDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table 1clickdispute.purchase_history: ~5 rows (approximately)
DELETE FROM `purchase_history`;
/*!40000 ALTER TABLE `purchase_history` DISABLE KEYS */;
INSERT INTO `purchase_history` (`id`, `userID`, `creditsAmount`, `receiptUrl`, `purchaseDate`) VALUES
	(1, 157, 22, 'https://trello.com/c/HgqDkSFX/10-credits-manager', '2022-04-02 14:13:41'),
	(2, 157, 55, 'https://trello.com/c/HgqDkSFX/10-credits-manager', '2022-04-02 14:13:42'),
	(3, 157, 11, 'https://trello.com/c/HgqDkSFX/10-credits-manager', '2022-04-02 14:13:42'),
	(4, 157, 33, 'https://trello.com/c/HgqDkSFX/10-credits-manager', '2022-04-02 14:13:43'),
	(5, 157, 44, 'https://trello.com/c/HgqDkSFX/10-credits-manager', '2022-04-02 14:13:43');
/*!40000 ALTER TABLE `purchase_history` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `production_key` varchar(255) NOT NULL,
  `development_key` varchar(255) NOT NULL,
  `dev_active` tinyint(4) DEFAULT '0',
  `content` text,
  `dev_username` varchar(50) DEFAULT NULL,
  `dev_password` varchar(50) DEFAULT NULL,
  `dev_url` varchar(255) DEFAULT NULL,
  `prod_username` varchar(50) DEFAULT NULL,
  `prod_password` varchar(50) DEFAULT NULL,
  `prod_url` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;

-- Dumping data for table 1clickdispute.settings: ~4 rows (approximately)
DELETE FROM `settings`;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `name`, `description`, `production_key`, `development_key`, `dev_active`, `content`, `dev_username`, `dev_password`, `dev_url`, `prod_username`, `prod_password`, `prod_url`, `created_at`) VALUES
	(143, 'mail_settings', '-', '-', '-', 0, '{"failed_payment":{"subject":"30DayCRA Payment Failed - Your account has been suspended","body":"<p>Hi ::FIRST_NAME::,<\\/p>\\n\\n<p>Your payment has failed and your account has been disabled.<\\/p>\\n\\n<p>Kind regards,<br \\/>\\n30DayCRA Team<\\/p>\\n"},"new_purchase":{"subject":"Welcome to 30DayCRA App","body":"<p>::FIRST_NAME::,<br \\/>\\n<br \\/>\\nHere are the details you&rsquo;ll need to login your account.<\\/p>\\n\\n<p>URL -&nbsp;https:\\/\\/app.1clickdispute.com\\/member\\/login.php<br \\/>\\nUsername: ::EMAIL::<br \\/>\\nPassword: ::PASSWORD::<\\/p>\\n\\n<p>If you&#39;re having issues logging into your account with the password above please use the &quot;forgot password&quot; function once you arrive at the URL above.<br \\/>\\n<br \\/>\\nKind Regards,<br \\/>\\n30DayCRA Team<\\/p>\\n"},"rebill":{"subject":"30DayCRA - New invoice","body":"<p>::FIRST_NAME::, thank you for your purchase.<\\/p>\\n\\n<p>Here are the details you&rsquo;ll need to login to your account.<\\/p>\\n\\n<p>Username: ::EMAIL::<\\/p>\\n\\n<p>Password: <em>current password<\\/em><\\/p>\\n\\n<p>Kind Regards,<br \\/>\\n30DayCRA&nbsp;Team<\\/p>\\n"},"password_reset":{"subject":"Your Requested Password","body":"<p>Dear ::FIRST_NAME::,<\\/p>\\n\\n<p>This email was sent automatically in response to your request to recover your password. This is done for your protection.<\\/p>\\n\\n<p>Only you, the recipient of this email can take the next step in the password recovery process.<\\/p>\\n\\n<p>New Password: ::PASSWORD::<\\/p>\\n\\n<p><em><strong>If you&#39;re having issues logging in with this password please send us an email at info@30daycra.com with a loom.com video link talking through the issue.<\\/strong><\\/em><\\/p>\\n\\n<p>Kind regards,<br \\/>\\n30DayCRA Team<\\/p>\\n"}}', NULL, NULL, NULL, NULL, NULL, NULL, '2020-11-23 22:18:04'),
	(150, 'stripe', 'Stripe Key', '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', 'sk_test_gmJW4Cr4HS28kJfI76pMmKXW001g1UOWQr', 0, NULL, '', '', '', '', '', '', '2022-03-15 02:36:50'),
	(151, 'sendgrid', 'Sendgrid Key', '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', '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', 0, NULL, '', '', '', '', '', '', '2022-03-15 02:36:50'),
	(152, 'lob', 'Lob Key', 'prodkey1234', 'devkey', 1, NULL, '', '', '', '', '', '', '2022-03-15 02:36:50');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

-- Dumping structure for table 1clickdispute.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `accType` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'basic',
  `planID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customerID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cardID` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currentCredits` int(11) unsigned DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastUpdated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `picture` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'user.png',
  `user_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `signature` text COLLATE utf8_unicode_ci,
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expireOn` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table 1clickdispute.users: ~0 rows (approximately)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `accType`, `planID`, `customerID`, `cardID`, `currentCredits`, `password`, `token`, `lastUpdated`, `picture`, `user_type`, `signature`, `createdOn`, `expireOn`) VALUES
	(157, 'DeveLoper', 'chesster423@gmail.com', 'price_HOH5NhxZtUgk0n', 'price_HOH5NhxZtUgk0n', 'cus_HrSdFSPPNBOxxW', NULL, NULL, '$2y$10$qn2TChRwI/PURkpOzc3QGOrKLeKVsbGHk66anVluMtRWXkFlADhGO', 'GZ0i8uYqiPGWfMmdj72j', '2022-03-29 13:36:51', 'user.png', 'default', '<?xml version="1.0"?>\r\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\r\n<svg xmlns="http://www.w3.org/2000/svg" width="15cm" height="15cm">\r\n	<g fill="#fff">\r\n		<rect x="0" y="0" width="400" height="100"/>\r\n		<g fill="none" stroke="#000" stroke-width="2">\r\n			<polyline points="53.5,25.19 56.5,26.19 63.5,28.19 85.5,34.19 115.5,45.19 143.5,61.19 164.5,73.19 178.5,80.19 185.5,82.19 186.5,82.19 186.5,79.19 182.5,74.19 177.5,67.19"/>\r\n			<polyline points="57.5,71.19 88.5,69.19 145.5,54.19 206.5,33.19 244.5,16.19 251.5,12.19 251.5,11.19 248.5,11.19 240.5,11.19 211.5,15.19"/>\r\n			<polyline points="63.5,35.19 87.5,39.19 148.5,43.19 213.5,45.19 261.5,45.19 280.5,45.19 282.5,45.19 281.5,45.19 280.5,45.19 278.5,44.19 268.5,41.19 220.5,31.19"/>\r\n			<polyline points="89.5,28.19 96.5,42.19 111.5,63.19 122.5,76.19 132.5,81.19 149.5,80.19 181.5,63.19 213.5,41.19 229.5,28.19 234.5,24.19 234.5,23.19 234.5,25.19 235.5,27.19 239.5,31.19 248.5,38.19 264.5,42.19 284.5,45.19 305.5,44.19 317.5,40.19 321.5,39.19 321.5,37.19 316.5,36.19 304.5,37.19 297.5,40.19"/>\r\n		</g>\r\n	</g>\r\n</svg>\r\n', '2020-06-05 22:30:20', '2022-09-19 05:19:50'),
	(162, 'YourFinancesSimplified', 'dbrown@sdcapitalholdingsllc.com', 'basic', NULL, NULL, NULL, NULL, '$2y$10$//tZndUrDXzb3ACGLIDvmeJ/bSBdF8lHCf9W/dPmErMvEbIfrNGZm', 'S_4PYSsr8pZwXgEZDY3Y', '2021-10-29 12:07:18', 'user.png', 'default', '<?xml version="1.0"?>\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n<svg xmlns="http://www.w3.org/2000/svg" width="15cm" height="15cm">\n	<g fill="#fff">\n		<rect x="0" y="0" width="400" height="100"/>\n		<g fill="none" stroke="#000" stroke-width="2">\n			<polyline points="73.5,30.25 73.5,30.25 73.5,34.25 73.5,39.25 71.5,53.25 70.5,58.25 69.5,69.25 68.5,74.25 67.5,83.25 66.5,85.25 66.5,87.25 66.5,88.25 66.5,88.25"/>\n			<polyline points="54.5,37.25 53.5,36.25 56.5,36.25 60.5,36.25 64.5,36.25 70.5,36.25 86.5,36.25 88.5,36.25 102.5,36.25 107.5,36.25 114.5,37.25 116.5,39.25 118.5,44.25 118.5,49.25 118.5,55.25 115.5,69.25 104.5,85.25 98.5,91.25 82.5,104.25 75.5,109.25 64.5,115.25 58.5,117.25 56.5,118.25 48.5,121.25 46.5,122.25 43.5,122.25 43.5,122.25 43.5,122.25"/>\n			<polyline points="101.5,68.25 101.5,67.25 101.5,67.25 101.5,68.25 101.5,70.25 101.5,73.25 101.5,75.25 101.5,79.25 102.5,80.25 103.5,81.25 105.5,83.25 106.5,83.25 108.5,83.25 110.5,83.25 110.5,82.25 110.5,80.25 110.5,74.25 110.5,72.25 108.5,66.25 105.5,61.25 101.5,59.25 100.5,59.25 98.5,57.25 97.5,57.25 97.5,57.25 98.5,57.25 99.5,58.25 107.5,58.25 113.5,58.25 116.5,58.25 122.5,58.25 125.5,58.25 127.5,60.25 127.5,63.25 127.5,66.25 127.5,71.25 125.5,77.25 125.5,80.25 124.5,83.25 124.5,85.25 124.5,85.25 124.5,83.25 127.5,79.25 129.5,76.25 134.5,70.25 136.5,67.25 138.5,64.25 139.5,64.25 139.5,64.25 139.5,64.25 139.5,67.25 139.5,68.25 139.5,70.25 139.5,71.25 139.5,71.25 140.5,71.25 141.5,71.25 143.5,69.25 144.5,68.25 145.5,67.25 146.5,67.25 146.5,67.25 146.5,67.25 146.5,73.25 146.5,75.25 147.5,81.25 147.5,84.25 148.5,86.25 149.5,86.25 154.5,84.25 155.5,82.25 159.5,78.25 160.5,76.25 162.5,74.25 163.5,73.25 163.5,73.25"/>\n			<polyline points="180.5,40.25 180.5,40.25"/>\n			<polyline points="165.5,75.25 165.5,75.25 165.5,74.25 165.5,73.25 165.5,71.25 165.5,71.25 165.5,70.25 164.5,71.25 164.5,74.25 163.5,77.25 163.5,81.25 163.5,85.25 163.5,90.25 163.5,92.25 164.5,95.25 166.5,96.25 168.5,94.25 168.5,93.25 168.5,91.25 168.5,89.25 168.5,88.25 168.5,87.25 169.5,88.25 170.5,89.25 172.5,92.25 174.5,96.25 176.5,99.25"/>\n			<polyline points="181.5,102.25 181.5,100.25 181.5,99.25 181.5,99.25 181.5,99.25 182.5,99.25 184.5,99.25 187.5,99.25 188.5,99.25 189.5,99.25 190.5,99.25 190.5,99.25 190.5,99.25"/>\n			<polyline points="190.5,82.25 189.5,81.25 190.5,81.25 191.5,81.25 194.5,81.25 201.5,81.25 203.5,81.25 206.5,80.25 206.5,80.25 207.5,79.25 205.5,79.25 201.5,79.25 197.5,79.25 194.5,79.25 193.5,79.25 192.5,80.25 193.5,82.25 203.5,84.25 211.5,85.25 241.5,85.25 248.5,84.25 252.5,83.25 260.5,80.25 263.5,79.25 266.5,79.25 267.5,78.25 267.5,78.25 268.5,78.25 276.5,77.25 279.5,76.25 285.5,75.25 287.5,74.25"/>\n		</g>\n	</g>\n</svg>\n', '2020-08-13 23:14:56', '2023-01-01 15:00:00'),
	(163, 'Senaid Bacinovic', 'senaidbacinovic@gmail.com', 'basic', NULL, NULL, NULL, NULL, '$2y$10$qtGZnDpObBDbvZU6.FJXDeJ36EP8Pqoa6QnUlMf/T0akjdUXQ33oO', 'A6ruZJj1xN9RKjUWtChO', '2020-10-16 18:18:25', 'user.png', 'default', '<?xml version="1.0"?>\r\n<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\r\n<svg xmlns="http://www.w3.org/2000/svg" width="15cm" height="15cm">\r\n	<g fill="#fff">\r\n		<rect x="0" y="0" width="400" height="100"/>\r\n		<g fill="none" stroke="#000" stroke-width="2">\r\n			<polyline points="123,26.55 122,26.55 122,27.55 122,31.55 124,36.55 138,43.55 157,50.55 173,55.55 189,56.55 202,53.55 204,51.55 204,50.55 204,48.55 204,46.55 205,44.55 205,42.55 205,40.55 206,40.55 207,40.55 209,45.55 213,48.55 221,52.55 232,54.55 255,55.55 272,52.55 279,48.55 281,46.55 281,44.55 281,41.55 277,36.55 264,31.55 248,28.55 228,28.55 207,30.55 196,34.55 184,39.55 184,40.55 184,43.55 184,44.55 183,44.55 182,44.55 178,44.55 173,45.55 167,49.55 156,56.55 148,61.55 147,63.55 148,64.55 151,64.55 154,62.55 155,59.55 156,53.55 155,50.55 154,49.55 155,49.55 156,49.55 160,49.55 171,49.55 193,52.55 211,55.55 217,56.55 217,59.55 216,61.55 216,63.55 220,62.55 229,60.55 232,57.55 232,55.55 232,56.55 232,57.55 234,57.55 237,58.55 239,58.55 246,58.55 255,56.55 257,56.55 259,55.55 262,55.55 270,53.55 280,50.55 287,46.55 289,44.55 289,42.55 289,39.55 289,35.55 289,25.55 289,18.55 289,14.55 289,11.55 289,8.55 289,7.55 290,7.55 292,7.55 294,9.55 312,21.55 331,35.55 351,49.55 363,57.55 368,59.55 370,62.55"/>\r\n		</g>\r\n	</g>\r\n</svg>\r\n', '2020-08-14 14:07:59', '2021-01-01 00:00:00'),
	(466, 'Chesster Dingcong', 'chesster99@gmail.com', 'basic', NULL, NULL, NULL, NULL, '$2y$10$oKp3WAA89SjQKQGXz7CTBu1QhFinocgr.pMOJkGXvdybOUXKOsXNG', NULL, '2022-03-18 13:22:04', 'user.png', 'default', NULL, '2022-03-18 13:22:04', '2022-03-18 13:22:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
