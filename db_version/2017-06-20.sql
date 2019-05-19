-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.21-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura do banco de dados para mm_adm
CREATE DATABASE IF NOT EXISTS `mm_adm` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `mm_adm`;


-- Copiando estrutura para tabela mm_adm.module
CREATE TABLE IF NOT EXISTS `module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(200) NOT NULL COMMENT 'label',
  `parent` int(11) DEFAULT NULL,
  `active` enum('Y','N') DEFAULT 'Y',
  `link` varchar(255) DEFAULT NULL,
  `order` float DEFAULT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- Copiando dados para a tabela mm_adm.module: ~9 rows (aproximadamente)
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` (`module_id`, `text`, `parent`, `active`, `link`, `order`) VALUES
	(1, 'Cadastros', NULL, 'Y', '', 1),
	(2, 'Clientes', 1, 'Y', 'internals/persons', 1.1),
	(6, 'Produtos', 1, 'Y', 'internals/products', 1.2),
	(13, 'Contratos', NULL, 'Y', 'internals/contracts', 2),
	(14, 'Preços', 1, 'Y', 'internals/prices', 1.3),
	(15, 'Acrescimo por Usuário', 1, 'Y', 'internals/price_user', 1.4);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm.person
CREATE TABLE IF NOT EXISTS `person` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `gender` enum('M','F') NOT NULL,
  `birthday` date NOT NULL,
  `dt_register` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_last_change` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `adress` mediumtext,
  `interview` mediumtext,
  `active` enum('Y','N') DEFAULT 'Y',
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mm_adm.person: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
/*!40000 ALTER TABLE `person` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm.person_contract
CREATE TABLE IF NOT EXISTS `person_contract` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `product_price_id` int(11) NOT NULL,
  `dt_register` datetime DEFAULT CURRENT_TIMESTAMP,
  `dt_expire` date DEFAULT NULL,
  `accept` enum('Y','N') DEFAULT 'N',
  `dt_accpept` datetime DEFAULT NULL,
  `user_amount` int(11) NOT NULL DEFAULT '1',
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mm_adm.person_contract: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `person_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_contract` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm.product
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mm_adm.product: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm.product_price_dates
CREATE TABLE IF NOT EXISTS `product_price_dates` (
  `price_date_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `dt_init` date NOT NULL,
  `dt_end` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`price_date_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mm_adm.product_price_dates: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `product_price_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_price_dates` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm.product_price_users
CREATE TABLE IF NOT EXISTS `product_price_users` (
  `price_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_price_id` int(11) NOT NULL,
  `user_amout` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`price_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Copiando dados para a tabela mm_adm.product_price_users: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `product_price_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_price_users` ENABLE KEYS */;


-- Copiando estrutura para tabela mm_adm._user_
CREATE TABLE IF NOT EXISTS `_user_` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `token_change_password` varchar(100) NOT NULL,
  `dt_register` datetime DEFAULT CURRENT_TIMESTAMP,
  `dt_update` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- Copiando dados para a tabela mm_adm._user_: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `_user_` DISABLE KEYS */;
INSERT INTO `_user_` (`id`, `email`, `password`, `token_change_password`, `dt_register`, `dt_update`) VALUES
	(3, 'ulisses.bueno@gmail.com', '4297f44b13955235245b2497399d7a93', '', '2017-06-09 21:29:18', '2017-06-09 21:29:58');
/*!40000 ALTER TABLE `_user_` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
