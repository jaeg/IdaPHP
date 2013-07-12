-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 12, 2013 at 04:23 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `chatbot`
--
CREATE DATABASE IF NOT EXISTS `chatbot` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chatbot`;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `KeywordID` int(11) NOT NULL AUTO_INCREMENT,
  `ResponseID` int(11) NOT NULL,
  `KeywordValue` varchar(255) NOT NULL,
  `KeywordWeight` float NOT NULL,
  PRIMARY KEY (`KeywordID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `keywords`
--

INSERT INTO `keywords` (`KeywordID`, `ResponseID`, `KeywordValue`, `KeywordWeight`) VALUES
(1, 1, 'Test', 1),
(2, 2, 'like', 0.3),
(3, 2, 'pie', 0.3);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `MessageID` int(11) NOT NULL AUTO_INCREMENT,
  `ResponseID` int(11) NOT NULL,
  `MessageValue` text NOT NULL,
  PRIMARY KEY (`MessageID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `ResponseID`, `MessageValue`) VALUES
(1, 1, 'You sent me a test message!'),
(2, 2, 'I like pie too!'),
(3, 2, 'I''m fond of pie myself.');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE IF NOT EXISTS `responses` (
  `ResponseID` int(11) NOT NULL AUTO_INCREMENT,
  `ResponseType` varchar(10) NOT NULL DEFAULT 'Statement',
  PRIMARY KEY (`ResponseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`ResponseID`, `ResponseType`) VALUES
(1, 'Statement'),
(2, 'Statement');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
