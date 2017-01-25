-- Dumping database structure for blackhole
DROP DATABASE IF EXISTS `blackhole`;
CREATE DATABASE IF NOT EXISTS `blackhole` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `blackhole`;

-- Dumping structure for table blackhole.categories
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `information` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Dumping data for table blackhole.categories: ~7 rows (approximately)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`category_id`, `name`, `information`) VALUES
	(1, 'Coding Challenge', NULL),
	(2, 'Test', NULL),
	(3, 'Question', NULL),
	(4, 'General Inquiry', NULL),
	(5, 'Hardware Problem', NULL),
	(6, 'Other', NULL),
	(7, 'Physics Question', NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Dumping structure for table blackhole.message_attachments
DROP TABLE IF EXISTS `message_attachments`;
CREATE TABLE IF NOT EXISTS `message_attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file` varchar(128) NOT NULL,
  `uploader_ip` varchar(128) NOT NULL,
  `file_size` int(11) NOT NULL,
  PRIMARY KEY (`attachment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table blackhole.tickets
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `subject` varchar(4096) NOT NULL,
  `author_name` varchar(64) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `date_created` int(11) NOT NULL,
  `date_updated` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table blackhole.ticket_messages
DROP TABLE IF EXISTS `ticket_messages`;
CREATE TABLE IF NOT EXISTS `ticket_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `author_name` varchar(64) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `date_created` int(11) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table blackhole.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL,
  `password` varchar(128) NOT NULL,
  `date_created` int(11) NOT NULL,
  `date_seen` int(11) DEFAULT NULL,
  `active_ticket` int(11) DEFAULT NULL,
  `rank` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for trigger blackhole.after_delete_tickets
DROP TRIGGER IF EXISTS `after_delete_tickets`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `after_delete_tickets` AFTER DELETE ON `tickets` FOR EACH ROW DELETE FROM
ticket_messages
WHERE ticket_messages.ticket_id = OLD.ticket_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger blackhole.after_delete_ticket_messages
DROP TRIGGER IF EXISTS `after_delete_ticket_messages`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `after_delete_ticket_messages` AFTER DELETE ON `ticket_messages` FOR EACH ROW DELETE FROM message_attachments
WHERE message_attachments.message_id = OLD.message_id//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;
