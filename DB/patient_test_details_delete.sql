-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 30, 2025 at 12:12 PM
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
-- Table structure for table `patient_test_details_delete`
--

CREATE TABLE `patient_test_details_delete` (
  `slno` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `opd_id` varchar(50) NOT NULL,
  `ipd_id` varchar(50) NOT NULL,
  `batch_no` int(11) NOT NULL,
  `testid` int(11) NOT NULL,
  `sample_id` int(11) NOT NULL,
  `test_rate` decimal(10,2) NOT NULL,
  `test_discount` decimal(10,2) NOT NULL,
  `dept_serial` varchar(20) NOT NULL,
  `addon_testid` int(11) NOT NULL COMMENT 'main testid',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `user` int(11) NOT NULL,
  `type` int(2) NOT NULL COMMENT '1=doctor, 2=lab_reg, 3=ot, 4=nurse, 5=ipd_dash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patient_test_details_delete`
--
ALTER TABLE `patient_test_details_delete`
  ADD PRIMARY KEY (`slno`),
  ADD KEY `opd_id` (`opd_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patient_test_details_delete`
--
ALTER TABLE `patient_test_details_delete`
  MODIFY `slno` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
