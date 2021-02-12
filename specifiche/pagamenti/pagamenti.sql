DROP DATABASE IF EXISTS `pagamenti`;
CREATE DATABASE `pagamenti` DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci;
USE `pagamenti`;

#
# table structure for table 'usr'
#
DROP TABLE IF EXISTS `usr`;
CREATE TABLE `usr` (
  `nick` VARCHAR(16) NOT NULL,
  `pwd` VARCHAR(16) NOT NULL,
  `nome` VARCHAR(64) NOT NULL,
  `saldo` INT DEFAULT 0,
  `negozio` BIT DEFAULT 0,
  PRIMARY KEY (`nick`)
) ENGINE=InnoDB;

#
# data for table 'usr'
#
INSERT INTO `usr` (`nick`, `pwd`, `nome`, `saldo`, `negozio`) VALUES
('boss', 'Thor1944', 'Siglund Thor', 9999900, 0),
('donald', 'duck!', 'Gianni Pasticcio', 100, 0),
('orto', 'insalatina', 'Pecetto S.C.A.R.L.', 200000, 1),
('pwr', 'pwr2013', 'Il web facile S.R.L.', 10000, 1);

#
# table structure for table 'log'
#
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `src` VARCHAR(16) NOT NULL,
  `dst` VARCHAR(16) NOT NULL,
  `importo` INT UNSIGNED NOT NULL,
  `data` DATETIME NOT NULL,
  PRIMARY KEY (`id`, `src`, `dst`),
  KEY `fk_log_usr` (`src`),
  KEY `fk_log_usr1` (`dst`),
  CONSTRAINT `fk_log_usr` FOREIGN KEY (`src`) REFERENCES `usr` (`nick`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_log_usr1` FOREIGN KEY (`dst`) REFERENCES `usr` (`nick`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6;

#
# data for table 'log'
#
INSERT INTO `log` (`id`, `src`, `dst`, `importo`, `data`) VALUES
(1, 'donald', 'orto', 1000, '2013-05-02 08:30:00'),
(2, 'donald', 'orto', 2000, '2020-05-03 10:30:00'),
(3, 'donald', 'pwr', 55000, '2020-06-07 17:00:00'),
(4, 'pwr', 'donald', 5000, '2020-06-08 15:00:00'),
(5, 'pwr', 'boss', 100000, '2020-06-09 09:00:00');

#
# Permessi user: uReadOnly; pwd: posso_solo_leggere (solo SELECT)
#
GRANT USAGE ON `pagamenti`.* TO 'uReadOnly'@'%' IDENTIFIED BY PASSWORD '*0FBF5C395B1E6B971E9CBB18F95041B49D0B0947';

GRANT SELECT ON `pagamenti`.* TO 'uReadOnly'@'%';

#
# Permessi user: uReadWrite; pwd: SuperPippo!!! (solo SELECT, INSERT, UPDATE)
#
GRANT USAGE ON `pagamenti`.* TO 'uReadWrite'@'%' IDENTIFIED BY PASSWORD '*400BF58DFE90766AF20296B3D89A670FC66BEAEC';

GRANT SELECT, INSERT, UPDATE ON `pagamenti`.* TO 'uReadWrite'@'%';
loglog