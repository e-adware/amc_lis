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
-- Table structure for table `uhid_and_opdid_delete`
--

CREATE TABLE `uhid_and_opdid_delete` (
  `slno` int(10) UNSIGNED NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `opd_id` varchar(50) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `disease_id` int(11) NOT NULL DEFAULT 0,
  `dept` int(11) NOT NULL,
  `hosp_no` varchar(20) NOT NULL,
  `bill_no` varchar(20) NOT NULL,
  `urgent` tinyint(4) NOT NULL DEFAULT 0,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `user` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `type_prefix` varchar(20) NOT NULL DEFAULT '',
  `sample_serial` varchar(50) NOT NULL DEFAULT '',
  `pat_type` varchar(20) NOT NULL,
  `free` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `auth` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `auth_disc` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_serial` int(11) NOT NULL,
  `emer_nr` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `nr_pat_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `nr_covid` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `del_user` int(11) NOT NULL,
  `del_date` date NOT NULL,
  `del_time` time NOT NULL,
  `del_reason` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `uhid_and_opdid_delete`
--
ALTER TABLE `uhid_and_opdid_delete`
  ADD PRIMARY KEY (`slno`),
  ADD KEY `opd_id` (`opd_id`),
  ADD KEY `hosp_no` (`hosp_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `uhid_and_opdid_delete`
--
ALTER TABLE `uhid_and_opdid_delete`
  MODIFY `slno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
