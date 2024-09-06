-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.11.4-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database games
CREATE DATABASE IF NOT EXISTS `games` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_general_ci */;
USE `games`;

-- Dump della struttura di tabella games.games
CREATE TABLE IF NOT EXISTS `games` (
  `gameId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gameCode` varchar(10) NOT NULL COMMENT 'Codice per distribuire la visione dei dati',
  `teamA` bigint(20) unsigned NOT NULL DEFAULT 0,
  `teamB` bigint(20) unsigned NOT NULL DEFAULT 0,
  `gameDate` date NOT NULL COMMENT 'Giorno della partita',
  `gameTime` time NOT NULL COMMENT 'Ora della partita',
  `gamePlace` varchar(100) DEFAULT NULL COMMENT 'Luogo della partita',
  `gamePlaceDetails` varchar(100) DEFAULT NULL COMMENT 'Palestra e indirizzo',
  `stato` varchar(1) NOT NULL DEFAULT 'N' COMMENT 'N - Nuova; G - In Gioco; F - Finita; C - Cancellata',
  `quarter` varchar(3) DEFAULT NULL COMMENT 'Indica il quarto a cui la partota é arrivata',
  PRIMARY KEY (`gameId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Le partite vere e proprie';

-- Dump dei dati della tabella games.games: ~0 rows (circa)
REPLACE INTO `games` (`gameId`, `gameCode`, `teamA`, `teamB`, `gameDate`, `gameTime`, `gamePlace`, `gamePlaceDetails`, `stato`, `quarter`) VALUES
	(1, 'c0943ae', 2, 1, '2024-11-22', '21:00:00', 'Legnano', 'PalaVirtus, via Santa Teresa del Bambin GesÃ¹ 34', 'G', 'q2'),
	(2, '2d90008', 4, 1, '2024-12-01', '20:30:00', 'Mercato Comunale, piazza Venditti', 'Palestra Galbani', 'N', NULL);

-- Dump della struttura di tabella games.players
CREATE TABLE IF NOT EXISTS `players` (
  `playerId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cognome` varchar(50) NOT NULL DEFAULT '',
  `nome` varchar(50) NOT NULL DEFAULT '',
  `teamId` bigint(20) unsigned NOT NULL DEFAULT 0,
  `numeroMaglia` varchar(2) NOT NULL COMMENT 'Deve essere un numero,  ammessi anche 0 e 00',
  PRIMARY KEY (`playerId`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Dump dei dati della tabella games.players: ~28 rows (circa)
REPLACE INTO `players` (`playerId`, `cognome`, `nome`, `teamId`, `numeroMaglia`) VALUES
	(1, 'Rossi', 'Mario', 1, '12'),
	(2, 'Verdi', 'Giulio', 2, '21'),
	(3, 'RF', 'Na', 2, '27'),
	(4, 'RF2', 'Na2', 1, '27'),
	(5, 'Altro cognme', 'Nome altro', 1, '28'),
	(6, 'Pales', 'Tra', 2, '24'),
	(7, 'Gall', 'Eggiante', 2, '26'),
	(8, 'Surname', 'Name', 1, '1'),
	(9, 'Bianchi', 'Piero', 2, '0'),
	(10, 'uno', 'uno piu uno', 2, '1'),
	(11, 'due', 'due piu due', 2, '2'),
	(12, 'tre', 'tre piu tre', 2, '3'),
	(13, 'nove', 'nove per nove', 2, '9'),
	(14, 'ten', 'ten per ten', 2, '10'),
	(15, 'trenta', 'zero', 1, '30'),
	(16, 'trent', 'uno', 1, '31'),
	(17, 'trenta', 'due', 1, '32'),
	(18, 'quar', 'anta', 1, '40'),
	(19, 'venti', 'zero', 1, '20'),
	(20, 'cinque', 'ante', 1, '50'),
	(21, 'Rossi', 'Mario', 4, '60'),
	(22, 'Marco', 'Bianchi', 4, '61'),
	(23, 'Verdi', 'Giulio', 4, '62'),
	(24, 'Proverbio', 'Fabio', 4, '63'),
	(25, 'Rotondi', 'Lucio', 4, '64'),
	(26, 'Pellai', 'Alberto', 4, '65'),
	(27, 'Svevo', 'Italo', 4, '66'),
	(28, 'Sciascia', 'Salvatore', 4, '67'),
	(29, 'Eco', 'Umberto', 4, '68'),
	(30, 'Alighieri', 'Dante', 4, '69');

-- Dump della struttura di tabella games.playersingame
CREATE TABLE IF NOT EXISTS `playersingame` (
  `gameId` bigint(20) unsigned NOT NULL,
  `playerId` bigint(20) unsigned NOT NULL,
  `onTheField` bit(1) NOT NULL DEFAULT b'0',
  `fouls` varchar(5) DEFAULT '',
  `tl` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri Liberi tentati',
  `tl_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri Liberi realizzati',
  `p2` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 2p tentati',
  `p2_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 2p realizzati',
  `p3` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 3p tentati',
  `p3_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 3p realizzati',
  `rimbA` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Rimbalzi Attacco',
  `rimbD` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Rimbalzi Difesa',
  `palPer` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Palle Perse',
  `palRec` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Palle Recuperate',
  PRIMARY KEY (`gameId`,`playerId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Le giocatrici coinvolte nella partita';

-- Dump dei dati della tabella games.playersingame: ~20 rows (circa)
REPLACE INTO `playersingame` (`gameId`, `playerId`, `onTheField`, `fouls`, `tl`, `tl_ok`, `p2`, `p2_ok`, `p3`, `p3_ok`, `rimbA`, `rimbD`, `palPer`, `palRec`) VALUES
	(1, 1, b'0', 'FF', 3, 3, 0, 0, 0, 0, 2, 0, 0, 0),
	(1, 2, b'1', '', 6, 4, 2, 1, 3, 2, 0, 2, 0, 0),
	(1, 3, b'0', '', 0, 0, 4, 4, 0, 0, 0, 0, 0, 0),
	(1, 4, b'1', 'TTTFF', 0, 0, 2, 0, 7, 5, 0, 2, 0, 0),
	(1, 5, b'0', '', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 6, b'1', '', 3, 3, 1, 1, 1, 1, 0, 0, 0, 0),
	(1, 7, b'1', '', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 8, b'1', 'T', 1, 0, 2, 1, 1, 1, 0, 1, 0, 0),
	(1, 9, b'0', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 10, b'0', '', 0, 0, 1, 1, 1, 0, 0, 0, 0, 0),
	(1, 11, b'0', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 12, b'0', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 13, b'1', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 14, b'1', 'FFU', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 15, b'0', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
	(1, 16, b'0', '', 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
	(1, 17, b'0', 'FFF', 6, 5, 0, 0, 0, 0, 2, 4, 0, 0),
	(1, 18, b'1', '', 0, 0, 3, 2, 0, 0, 0, 0, 0, 0),
	(1, 19, b'1', 'TFUT', 0, 0, 0, 0, 7, 4, 4, 4, 0, 0),
	(1, 20, b'1', '', 0, 0, 0, 0, 0, 0, 1, 0, 0, 0);

-- Dump della struttura di tabella games.teams
CREATE TABLE IF NOT EXISTS `teams` (
  `teamId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomeTeam` varchar(50) NOT NULL,
  `campionato` varchar(50) NOT NULL DEFAULT '--',
  `citta` varchar(50) NOT NULL DEFAULT '--',
  PRIMARY KEY (`teamId`),
  UNIQUE KEY `nomeTeam_campionato` (`nomeTeam`,`campionato`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Dump dei dati della tabella games.teams: ~3 rows (circa)
REPLACE INTO `teams` (`teamId`, `nomeTeam`, `campionato`, `citta`) VALUES
	(1, 'ABA U19', 'U19', 'Legnano'),
	(2, 'ABA - C', 'Serie C', 'Legnano'),
	(4, 'FairPlay', 'C-Deve-Essere', 'SVO');

-- Dump della struttura di tabella games.utenti
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profilo` varchar(20) NOT NULL DEFAULT '',
  `passwd` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `profilo` (`profilo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- Dump dei dati della tabella games.utenti: ~0 rows (circa)
REPLACE INTO `utenti` (`id`, `profilo`, `passwd`) VALUES
	(1, 'admin', 'admin');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
