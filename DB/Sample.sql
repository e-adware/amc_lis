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
-- Table structure for table `Sample`
--

CREATE TABLE `Sample` (
  `ID` int(11) NOT NULL,
  `Name` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Sample`
--

INSERT INTO `Sample` (`ID`, `Name`) VALUES
(1, 'EDTA Whole Blood'),
(2, 'Serum'),
(3, 'Plasma'),
(4, 'Plasma Fasting'),
(5, 'Plasma P.P.'),
(6, 'Citrate Whole Blood'),
(7, 'Heparinized Whole Blood'),
(8, 'Cerebrospinal Fluid (CSF)'),
(9, 'Urine'),
(10, 'Stool Sample'),
(11, 'Sputum Sample'),
(12, 'Pus / Wound Aspirate'),
(13, 'Throat Swab'),
(14, 'Nasal Swab'),
(15, 'Conjunctival Swab'),
(16, 'Genital Swab (Vaginal/Cervical)'),
(17, 'Urethral Swab'),
(18, 'Sterile Swab (General)'),
(19, 'Swab in Transport Medium'),
(20, 'Bronchoalveolar Lavage (BAL)'),
(21, 'Bronchial Aspirate/Washings'),
(22, 'Body Fluid (Unspecified)'),
(23, 'Pleural Fluid'),
(24, 'Ascitic Fluid'),
(25, 'Pericardial Fluid'),
(26, 'Peritoneal Fluid'),
(27, 'Synovial Fluid'),
(28, 'Amniotic Fluid'),
(29, 'Semen / Seminal Fluid'),
(30, 'Urethral Fluid/Discharge'),
(31, 'Tissue / Biopsy'),
(32, 'Bone Marrow Aspirate'),
(33, 'Peripheral Blood Smear'),
(34, 'Cytology Smear/Slide'),
(35, 'Cervical Smear (Pap Test)'),
(36, 'Calculus (Stone)'),
(37, 'Catheter/Device Tip for Culture'),
(38, 'Nail/Skin Scraping'),
(39, 'Skin Smear/Scraping'),
(40, 'Water Sample'),
(41, 'Blood Culture');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Sample`
--
ALTER TABLE `Sample`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Sample`
--
ALTER TABLE `Sample`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
