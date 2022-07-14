-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.3
-- Erstellungszeit: 14. Mai 2011 um 17:44
-- Server Version: 5.1.54
-- PHP-Version: 4.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db317613`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_news`
--

CREATE TABLE `admin_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `angebote`
--

CREATE TABLE `angebote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `angebotsnr` int(11) NOT NULL,
  `angebotsdatum` date NOT NULL,
  `kundennachricht` text NOT NULL,
  `hat_stunden` tinyint(4) NOT NULL,
  `hat_artikel` tinyint(4) NOT NULL,
  `rabatt_prozent` float NOT NULL DEFAULT '0',
  `rabatt_betrag` double NOT NULL DEFAULT '0',
  `skonto_prozent` float NOT NULL DEFAULT '0',
  `skonto_betrag` double NOT NULL DEFAULT '0',
  `endbetrag` double NOT NULL DEFAULT '0',
  `kdnr` int(11) NOT NULL,
  `bezahlt` tinyint(4) NOT NULL DEFAULT '0',
  `bezahlt_datum` date NOT NULL,
  `editierbar` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `artikel`
--

CREATE TABLE `artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artnr_lieferant` varchar(100) DEFAULT NULL,
  `lieferantennr` varchar(60) NOT NULL,
  `bezeichnung` text,
  `kategorie` varchar(60) NOT NULL,
  `preis_netto` decimal(19,4) DEFAULT NULL,
  `steuersatz` double NOT NULL,
  `preis_brutto` decimal(19,4) NOT NULL,
  `einheit` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=437 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `a_artikel`
--

CREATE TABLE `a_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artnr` int(11) NOT NULL,
  `art_menge` double NOT NULL,
  `einzelpreis` double NOT NULL,
  `gesamtpreis_artikel` double NOT NULL,
  `angebotsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1429 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `a_stunden`
--

CREATE TABLE `a_stunden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `st_datum` date NOT NULL,
  `ma_nr` int(11) NOT NULL,
  `ma_name` varchar(50) NOT NULL,
  `arbeit_art` text NOT NULL,
  `zeit_stunden` double NOT NULL,
  `euro_st` double NOT NULL,
  `euro_st_gesamt` double NOT NULL,
  `angebotsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=477 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzername` varchar(50) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `stundensatz` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `letzter_login` datetime NOT NULL,
  `ist_admin` tinyint(4) NOT NULL DEFAULT '0',
  `status` enum('ON','OFF') NOT NULL DEFAULT 'OFF',
  `tm` datetime NOT NULL,
  `session_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `login_ip` varchar(20) NOT NULL,
  `gesperrt` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `help`
--

CREATE TABLE `help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(100) NOT NULL,
  `helptext` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kalender`
--

CREATE TABLE `kalender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text NOT NULL,
  `author` varchar(100) NOT NULL,
  `important` tinyint(4) NOT NULL,
  `mail_sent` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kunden`
--

CREATE TABLE `kunden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anrede` varchar(20) DEFAULT NULL,
  `vorname` varchar(100) DEFAULT NULL,
  `nachname` varchar(100) DEFAULT NULL,
  `kontennummer` int(11) DEFAULT NULL,
  `strasse` varchar(100) DEFAULT NULL,
  `hausnummer` varchar(10) DEFAULT NULL,
  `ort` varchar(100) DEFAULT NULL,
  `plz` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Kd_Kontennummer` (`kontennummer`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=618 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lieferscheine`
--

CREATE TABLE `lieferscheine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lieferscheinnr` int(11) NOT NULL,
  `lieferscheindatum` date NOT NULL,
  `kundennachricht` text NOT NULL,
  `hat_stunden` tinyint(4) NOT NULL,
  `hat_artikel` tinyint(4) NOT NULL,
  `rabatt_prozent` float NOT NULL DEFAULT '0',
  `rabatt_betrag` double NOT NULL DEFAULT '0',
  `skonto_prozent` float NOT NULL DEFAULT '0',
  `skonto_betrag` double NOT NULL DEFAULT '0',
  `endbetrag` double NOT NULL DEFAULT '0',
  `kdnr` int(11) NOT NULL,
  `bezahlt` tinyint(4) NOT NULL DEFAULT '0',
  `bezahlt_datum` date NOT NULL,
  `editierbar` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `log_txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=877 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `login_stats`
--

CREATE TABLE `login_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `login_name` varchar(50) NOT NULL,
  `tried_password` varchar(100) NOT NULL,
  `login_ip` varchar(20) NOT NULL,
  `failure_type` varchar(50) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `log_complete`
--

CREATE TABLE `log_complete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `log_txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1114 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `l_artikel`
--

CREATE TABLE `l_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artnr` int(11) NOT NULL,
  `art_menge` double NOT NULL,
  `einzelpreis` double NOT NULL,
  `gesamtpreis_artikel` double NOT NULL,
  `lieferscheinnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mahnungen`
--

CREATE TABLE `mahnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahnungsnr` int(11) NOT NULL,
  `mahnungsdatum` date NOT NULL,
  `kundennachricht` text NOT NULL,
  `hat_stunden` tinyint(4) NOT NULL,
  `hat_artikel` tinyint(4) NOT NULL,
  `rabatt_prozent` float NOT NULL DEFAULT '0',
  `rabatt_betrag` double NOT NULL DEFAULT '0',
  `skonto_prozent` float NOT NULL DEFAULT '0',
  `skonto_betrag` double NOT NULL DEFAULT '0',
  `endbetrag` double NOT NULL DEFAULT '0',
  `kdnr` int(11) NOT NULL,
  `bezahlt` tinyint(4) NOT NULL DEFAULT '0',
  `faelligkeitsdatum` date NOT NULL,
  `editierbar` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `m_artikel`
--

CREATE TABLE `m_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artnr` int(11) NOT NULL,
  `art_menge` double NOT NULL,
  `einzelpreis` double NOT NULL,
  `gesamtpreis_artikel` double NOT NULL,
  `mahnungsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `m_stunden`
--

CREATE TABLE `m_stunden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `st_datum` date NOT NULL,
  `ma_nr` int(11) NOT NULL,
  `ma_name` varchar(50) NOT NULL,
  `arbeit_art` text NOT NULL,
  `zeit_stunden` double NOT NULL,
  `euro_st` double NOT NULL,
  `euro_st_gesamt` double NOT NULL,
  `mahnungsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(30) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rechnungen`
--

CREATE TABLE `rechnungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rechnungsnr` int(11) NOT NULL,
  `rechnungsdatum` date NOT NULL,
  `kundennachricht` text NOT NULL,
  `hat_stunden` tinyint(4) NOT NULL,
  `hat_artikel` tinyint(4) NOT NULL,
  `rabatt_prozent` float NOT NULL DEFAULT '0',
  `rabatt_betrag` double NOT NULL DEFAULT '0',
  `skonto_prozent` float NOT NULL DEFAULT '0',
  `skonto_betrag` double NOT NULL DEFAULT '0',
  `endbetrag` double NOT NULL DEFAULT '0',
  `kdnr` int(11) NOT NULL,
  `bezahlt` tinyint(4) NOT NULL DEFAULT '0',
  `bezahlt_datum` date NOT NULL,
  `editierbar` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `r_artikel`
--

CREATE TABLE `r_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artnr` int(11) NOT NULL,
  `art_menge` double NOT NULL,
  `einzelpreis` double NOT NULL,
  `gesamtpreis_artikel` double NOT NULL,
  `rechnungsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1927 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `r_stunden`
--

CREATE TABLE `r_stunden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `st_datum` date NOT NULL,
  `ma_nr` int(11) NOT NULL,
  `ma_name` varchar(50) NOT NULL,
  `arbeit_art` text NOT NULL,
  `zeit_stunden` double NOT NULL,
  `euro_st` double NOT NULL,
  `euro_st_gesamt` double NOT NULL,
  `rechnungsnr` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=490 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `x_dev_info`
--

CREATE TABLE `x_dev_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info_txt` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=159 ;
