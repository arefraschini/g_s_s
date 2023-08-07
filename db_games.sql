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
  `gameCode` varchar(10) NOT NULL,
  `teamA` bigint(20) unsigned NOT NULL DEFAULT 0,
  `teamB` bigint(20) unsigned NOT NULL DEFAULT 0,
  `gameDate` date NOT NULL COMMENT 'Giorno della partita',
  `gameTime` time NOT NULL COMMENT 'Ora della partita',
  `gamePlace` varchar(100) DEFAULT NULL COMMENT 'Luogo della partita',
  `gamePlaceDetails` varchar(100) DEFAULT NULL COMMENT 'Palestra e indirizzo',
  PRIMARY KEY (`gameId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Le partite vere e proprie';

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella games.players
CREATE TABLE IF NOT EXISTS `players` (
  `playerId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cognome` varchar(50) NOT NULL DEFAULT '',
  `nome` varchar(50) NOT NULL DEFAULT '',
  `teamId` bigint(20) unsigned NOT NULL DEFAULT 0,
  `numeroMaglia` varchar(2) NOT NULL COMMENT 'Deve essere un numero,  ammessi anche 0 e 00',
  PRIMARY KEY (`playerId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella games.playersingame
CREATE TABLE IF NOT EXISTS `playersingame` (
  `gameId` bigint(20) unsigned NOT NULL,
  `playerId` bigint(20) unsigned NOT NULL,
  `onTheField` bit(1) NOT NULL DEFAULT b'0',
  `tl` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri Liberi tentati',
  `tl_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri Liberi realizzati',
  `2p` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 2p tentati',
  `2p_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 2p realizzati',
  `3p` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 3p tentati',
  `3p_ok` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Tiri 3p realizzati',
  `rimbA` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Rimbalzi Attacco',
  `rimbD` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Rimbalzi Difesa',
  `palPer` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Palle Perse',
  `palRec` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'Palle Recuperate',
  PRIMARY KEY (`gameId`,`playerId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Le giocatrici coinvolte nella partita';

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella games.teams
CREATE TABLE IF NOT EXISTS `teams` (
  `teamId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomeTeam` varchar(50) NOT NULL,
  `campionato` varchar(50) NOT NULL DEFAULT '--',
  `citta` varchar(50) NOT NULL DEFAULT '--',
  PRIMARY KEY (`teamId`),
  UNIQUE KEY `nomeTeam_campionato` (`nomeTeam`,`campionato`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- L’esportazione dei dati non era selezionata.

-- Dump della struttura di tabella games.utenti
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profilo` varchar(20) NOT NULL DEFAULT '',
  `passwd` varchar(70) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `profilo` (`profilo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- L’esportazione dei dati non era selezionata.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
