-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 01, 2012 at 10:32 PM
-- Server version: 5.1.58
-- PHP Version: 5.3.6-13ubuntu3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `dalal`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `mortgageId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `stockId` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `loanValue` double NOT NULL,
  PRIMARY KEY (`mortgageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`mortgageId`, `userId`, `stockId`, `number`, `loanValue`) VALUES
(2, 2, 1, 1, 5.5);

-- --------------------------------------------------------

--
-- Table structure for table `buy`
--

CREATE TABLE IF NOT EXISTS `buy` (
  `buyId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `stockId` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`buyId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `buy`
--

INSERT INTO `buy` (`buyId`, `userId`, `stockId`, `num`, `value`) VALUES
(7, 1, 2, 2, 7.1),
(9, 1, 1, 10, 5.05);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fromId` int(11) NOT NULL,
  `toId` int(11) NOT NULL,
  `stockId` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `value` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`time`, `fromId`, `toId`, `stockId`, `num`, `value`) VALUES
('2012-01-24 10:32:16', 2, 1, 2, 2, 7.02),
('2012-01-24 10:39:51', 1, 2, 2, 5, 7.01),
('2012-01-24 10:41:35', 2, 1, 1, 3, 5.06),
('2012-01-24 10:42:23', 2, 1, 1, 1, 5.07),
('2012-01-24 10:43:24', 2, 1, 1, 1, 5),
('2012-01-24 10:44:28', 1, 2, 2, 2, 7.25),
('2012-01-24 10:46:15', 1, 2, 2, 2, 7.25),
('2012-01-24 10:50:21', 2, 1, 2, 2, 7.3),
('2012-01-24 10:51:44', 2, 1, 2, 2, 7.2),
('2012-01-24 10:52:47', 2, 1, 2, 4, 7.3),
('2012-01-24 16:18:44', 80153, 1, 1, 10, 7.76);

-- --------------------------------------------------------

--
-- Table structure for table `misc_data`
--

CREATE TABLE IF NOT EXISTS `misc_data` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `misc_data`
--

INSERT INTO `misc_data` (`time`, `key`, `value`) VALUES
('2012-01-24 10:50:21', 'index', '6.04'),
('2012-01-24 10:51:44', 'index', '6.04'),
('2012-01-24 10:52:47', 'index', '6.06'),
('2012-01-24 11:08:15', 'ranklist', '[{"holdings":0,"cashInHand":"500000","userId":4,"totalWorth":500000},{"holdings":"163.27","cashInHand":"49966.56","userId":1,"totalWorth":50129.83},{"holdings":"49.7","cashInHand":"30000","userId":3,"totalWorth":30049.7},{"holdings":"490.15","cashInHand":"10033.44","userId":2,"totalWorth":10523.59}]'),
('2012-01-24 11:09:18', 'ranklist', '[{"holdings":0,"cashInHand":"500000","userId":4,"totalWorth":500000},{"holdings":"163.27","cashInHand":"49966.56","userId":1,"totalWorth":50129.83},{"holdings":"49.7","cashInHand":"30000","userId":3,"totalWorth":30049.7},{"holdings":"490.15","cashInHand":"10033.44","userId":2,"totalWorth":10523.59}]'),
('2012-01-24 16:18:44', 'index', '6.52'),
('2012-01-28 15:47:57', 'ranklist', '[{"holdings":0,"cashInHand":"500000","userId":4,"totalWorth":500000,"rank":1},{"holdings":"238.21","cashInHand":"49889.00","userId":1,"totalWorth":50127.21,"rank":2},{"holdings":"49.7","cashInHand":"30000","userId":3,"totalWorth":30049.7,"rank":3},{"holdings":"577.55","cashInHand":"10033.44","userId":2,"totalWorth":10610.99,"rank":4}]');

-- --------------------------------------------------------

--
-- Table structure for table `sell`
--

CREATE TABLE IF NOT EXISTS `sell` (
  `sellId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `stockId` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`sellId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sell`
--

INSERT INTO `sell` (`sellId`, `userId`, `stockId`, `num`, `value`) VALUES
(5, 2, 2, 2, 7.2);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE IF NOT EXISTS `stocks` (
  `stockId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `marketValue` double NOT NULL,
  `exchangePrice` double NOT NULL,
  `lastTrade` double NOT NULL,
  `dayLow` double NOT NULL,
  `dayHigh` double NOT NULL,
  `numIssued` int(11) NOT NULL,
  `sharesInExchange` int(11) NOT NULL,
  `factor` float NOT NULL,
  PRIMARY KEY (`stockId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`stockId`, `name`, `marketValue`, `exchangePrice`, `lastTrade`, `dayLow`, `dayHigh`, `numIssued`, `sharesInExchange`, `factor`) VALUES
(1, 'first', 5.93, 5, 7.76, 5, 7.76, 30, 0, 0.5),
(2, 'st2', 7.1, 7, 7.3, 7, 7.3, 30, 26, 0.5);

-- --------------------------------------------------------

--
-- Table structure for table `stocks_data`
--

CREATE TABLE IF NOT EXISTS `stocks_data` (
  `stockId` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stocks_data`
--

INSERT INTO `stocks_data` (`stockId`, `time`, `key`, `value`) VALUES
(2, '2012-01-24 10:32:16', 'graph_point', '7.02'),
(2, '2012-01-24 10:39:51', 'graph_point', '7.01'),
(1, '2012-01-24 10:41:35', 'graph_point', '5.06'),
(2, '2012-01-24 10:46:15', 'graph_point', '7.25'),
(2, '2012-01-24 10:51:44', 'graph_point', '7.20'),
(2, '2012-01-24 10:52:47', 'graph_point', '7.30'),
(1, '2012-01-24 16:18:44', 'graph_point', '7.76');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `loginMethod` varchar(100) NOT NULL,
  `userName` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `loginMethod`, `userName`, `password`, `verified`) VALUES
(1, 'db', 'chakradarraju', '220a34bbf010e405eb20a4168e69fea1', 0),
(2, '', 'jack', '5f4dcc3b5aa765d61d8327deb882cf99', 0),
(3, '', 'onemore', 'be8f1626ab0d4ff8d36cae9199265271', 0),
(4, '', 'three', '35d6d33467aae9a2e3dccb4b6b027878', 0),
(5, 'oauth', 'facebook', '1281515487', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE IF NOT EXISTS `users_data` (
  `userId` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_data`
--

INSERT INTO `users_data` (`userId`, `time`, `key`, `value`) VALUES
(1, '2012-01-19 16:53:01', 'Display Name', 'Jack'),
(1, '2012-02-01 09:22:05', 'cashInHand', '50362.23'),
(1, '2012-02-01 06:22:34', '2', '12'),
(1, '2012-02-01 09:22:05', '1', '11'),
(1, '2012-01-28 14:52:53', 'graph_point', '5.0123'),
(2, '2012-01-24 10:52:47', 'cashInHand', '10033.44'),
(2, '2012-01-19 16:59:28', 'Display Name', 'Chakradar Raju'),
(2, '2012-01-24 10:43:24', '1', '95'),
(2, '2012-01-24 10:52:47', '2', '2'),
(2, '2012-01-19 17:00:03', 'graph_point', '1000'),
(2, '2012-01-19 17:00:15', 'graph_point', '2000'),
(1, '2012-01-28 14:54:39', 'graph_point', '10'),
(3, '2012-01-19 17:59:46', 'cashInHand', '30000'),
(3, '2012-01-19 17:59:46', 'Display Name', 'Something'),
(3, '2012-01-19 18:03:39', '3', '10'),
(3, '2012-01-19 18:04:59', '2', '7'),
(4, '2012-01-19 18:26:18', 'cashInHand', '500000'),
(4, '2012-01-24 11:08:15', 'graph_point', '500000'),
(1, '2012-01-28 14:54:46', 'graph_point', '7'),
(3, '2012-01-24 11:08:15', 'graph_point', '30049.70'),
(2, '2012-01-24 11:08:15', 'graph_point', '10523.59'),
(4, '2012-01-24 11:09:18', 'graph_point', '500000'),
(1, '2012-01-28 14:54:53', 'graph_point', '3'),
(3, '2012-01-24 11:09:18', 'graph_point', '30049.70'),
(2, '2012-01-24 11:09:18', 'graph_point', '10523.59'),
(1, '2012-01-28 14:55:01', 'graph_point', '6'),
(4, '2012-01-28 15:47:57', 'graph_point', '500000'),
(1, '2012-01-28 15:47:57', 'graph_point', '50127.21'),
(3, '2012-01-28 15:47:57', 'graph_point', '30049.70'),
(2, '2012-01-28 15:47:57', 'graph_point', '10610.99'),
(5, '2012-02-01 10:33:51', 'Display Name', 'Chakradar Raju'),
(5, '2012-02-01 12:07:43', 'cashInHand', '49842'),
(5, '2012-02-01 12:07:43', '1', '26'),
(5, '2012-02-01 11:54:02', '2', '4');

