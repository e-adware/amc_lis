-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 07, 2025 at 04:48 PM
-- Server version: 10.11.13-MariaDB-0ubuntu0.24.04.1-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `sample_prefix_master`
--

CREATE TABLE `sample_prefix_master` (
  `slno` int(11) NOT NULL,
  `sample_prefix` char(100) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1=hide'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sample_prefix_master`
--

INSERT INTO `sample_prefix_master` (`slno`, `sample_prefix`, `status`) VALUES
(1, 'OPD_BIO/', 0),
(2, 'IPD_BIO/', 0),
(3, 'EC/', 0),
(4, 'NRHM/', 0),
(5, 'NRM_EMRG/', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sample_prefix_master`
--
ALTER TABLE `sample_prefix_master`
  ADD PRIMARY KEY (`slno`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sample_prefix_master`
--
ALTER TABLE `sample_prefix_master`
  MODIFY `slno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
