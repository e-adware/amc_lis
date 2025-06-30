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
-- Table structure for table `vaccu_master`
--

CREATE TABLE `vaccu_master` (
  `id` int(5) NOT NULL,
  `type` varchar(100) NOT NULL,
  `rate` decimal(19,2) NOT NULL DEFAULT '0.00',
  `barcode_suffix` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vaccu_master`
--

INSERT INTO `vaccu_master` (`id`, `type`, `rate`, `barcode_suffix`) VALUES
(1, 'EDTA Tube', '0.00', 'ET'),
(2, 'Serum Tube', '0.00', 'ST'),
(3, 'Serum Separator Tube (SST)', '0.00', 'SST'),
(4, 'Sodium Fluoride Tube', '0.00', 'SFT'),
(5, 'Sodium Citrate Tube', '0.00', 'SCT'),
(6, 'Heparin Tube', '0.00', 'HT'),
(7, 'Heparinized Syringe', '0.00', 'HS'),
(8, 'Sterile CSF Collection Tube', '0.00', 'CSF'),
(9, 'Sterile Urine Container', '0.00', 'SUC'),
(10, '24 Hours Urine Jug', '0.00', 'UJ'),
(11, 'Stool Collection Container', '0.00', 'SCC'),
(12, 'Sterile Sputum Container', '0.00', 'SSC'),
(13, 'Sterile Container', '0.00', 'SC'),
(14, 'Swab in Transport Medium', '0.00', 'STM'),
(15, 'Sterile Body Fluid Container', '0.00', 'SBF'),
(16, 'Sterile Cup', '0.00', 'SCP'),
(17, 'Plain/Heparin Tube', '0.00', 'PHT'),
(18, 'Blood Culture Bottle Aerobic', '0.00', 'BCA'),
(19, 'Blood Culture Bottle Anaerobic', '0.00', 'BCN'),
(20, 'Sterile Semen Collection Container', '0.00', 'SMC'),
(21, 'Sterile Container or Swab', '0.00', 'SCS'),
(22, 'Biopsy Container with 10% Formalin', '0.00', 'BCF'),
(23, 'Microscope Slide', '0.00', 'MS'),
(24, 'Liquid-Based Cytology (LBC) Vial', '0.00', 'LBC'),
(25, 'Sterile Dry Container', '0.00', 'SDC'),
(26, 'Petri Dish', '0.00', 'PD'),
(27, 'Sterile Dry Container or Microscope Slide', '0.00', 'SDS'),
(28, 'Sterile Water Collection Bottle', '0.00', 'SWB');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `vaccu_master`
--
ALTER TABLE `vaccu_master`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barcode_suffix` (`barcode_suffix`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `vaccu_master`
--
ALTER TABLE `vaccu_master`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
