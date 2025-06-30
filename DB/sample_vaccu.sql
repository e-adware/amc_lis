-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 26, 2025 at 09:01 PM
-- Server version: 5.6.16-1~exp1
-- PHP Version: 7.2.34-51+ubuntu20.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amc_lis`
--

-- --------------------------------------------------------

--
-- Table structure for table `sample_vaccu`
--

CREATE TABLE `sample_vaccu` (
  `slno` int(11) NOT NULL,
  `samp_id` int(11) NOT NULL COMMENT 'sample id',
  `vacc_id` int(11) NOT NULL COMMENT 'vaccu id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sample_vaccu`
--

INSERT INTO `sample_vaccu` (`slno`, `samp_id`, `vacc_id`) VALUES
(8, 28, 18),
(9, 28, 19),
(10, 28, 1),
(11, 28, 17),
(12, 28, 16),
(13, 24, 18),
(14, 24, 19),
(15, 24, 1),
(16, 24, 17),
(17, 24, 16),
(18, 41, 18),
(19, 41, 19),
(20, 22, 15),
(21, 32, 1),
(22, 21, 15),
(23, 20, 15),
(24, 36, 25),
(25, 37, 25),
(26, 8, 8),
(27, 35, 24),
(28, 35, 23),
(29, 6, 5),
(30, 15, 14),
(31, 34, 23),
(32, 1, 1),
(33, 16, 14),
(34, 7, 7),
(35, 38, 26),
(36, 38, 25),
(37, 14, 14),
(38, 25, 18),
(39, 25, 19),
(40, 25, 1),
(41, 25, 17),
(42, 25, 16),
(43, 33, 23),
(44, 26, 18),
(45, 26, 19),
(46, 26, 1),
(47, 26, 17),
(48, 26, 16),
(49, 3, 4),
(50, 4, 10),
(51, 4, 4),
(52, 5, 4),
(53, 23, 18),
(54, 23, 19),
(55, 23, 1),
(56, 23, 17),
(57, 23, 16),
(58, 12, 13),
(59, 12, 14),
(60, 29, 20),
(61, 2, 3),
(62, 2, 2),
(63, 39, 23),
(64, 39, 25),
(65, 11, 12),
(66, 18, 14),
(67, 10, 11),
(68, 19, 14),
(69, 27, 18),
(70, 27, 19),
(71, 27, 1),
(72, 27, 17),
(73, 27, 16),
(74, 13, 14),
(75, 31, 22),
(76, 30, 13),
(77, 30, 14),
(78, 17, 14),
(79, 9, 9),
(80, 40, 28);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sample_vaccu`
--
ALTER TABLE `sample_vaccu`
  ADD PRIMARY KEY (`slno`),
  ADD KEY `samp_id` (`samp_id`),
  ADD KEY `vacc_id` (`vacc_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sample_vaccu`
--
ALTER TABLE `sample_vaccu`
  MODIFY `slno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
