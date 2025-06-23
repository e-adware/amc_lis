-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2025 at 04:50 PM
-- Server version: 10.6.18-MariaDB-0ubuntu0.22.04.1
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
-- Table structure for table `Sample`
--

CREATE TABLE `Sample` (
  `ID` int(5) NOT NULL DEFAULT 0,
  `Name` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Sample`
--

INSERT INTO `Sample` (`ID`, `Name`) VALUES
(103, 'Blood + Urine'),
(1, ' No Sample'),
(2, 'Blood'),
(3, 'CSF'),
(4, 'Stool'),
(5, 'Sputum'),
(6, 'Pus'),
(7, 'Urine'),
(8, 'Throat Swab'),
(9, 'Bronchioalveolar Lavage'),
(10, 'Body Fluids'),
(11, 'Stone'),
(12, 'Others'),
(13, 'TIP'),
(14, 'Nasal Swab'),
(15, 'Conjunctial Swab'),
(16, 'VAGINAL/CERVICAL/HV SWAB'),
(17, 'Urethral Fluids'),
(18, 'Semen'),
(19, 'Early Morning Urine First Sample'),
(20, 'Fluids'),
(21, 'Seminal Fluids'),
(22, 'Tur Prostate Chips'),
(23, 'Needle Biopsy Liver'),
(24, 'Phlebotamist requried'),
(25, '24 Hrs Urine'),
(26, 'Gastric Biopsy'),
(27, 'Gastro Duodenal Biopsy'),
(28, 'Duodenal Biopsy'),
(29, 'Serum'),
(30, 'Plasma'),
(31, 'Whole Blood'),
(32, 'Plasma+Urine'),
(33, 'Plasma Glucose'),
(34, 'Urethral Swab'),
(35, 'Culture Tips'),
(36, 'Blood For Serum In Plain Tube'),
(37, 'Blood In EDTA Tube(Purple)'),
(38, 'Water For Analysis For Sterile Bottles'),
(39, 'Esophageal/Gastric/Duodenal Biospsy'),
(40, 'Patient'),
(41, 'Culture Of AFB'),
(42, 'Whole Blood (EDTA)'),
(43, 'BM Smears/P Smears'),
(44, 'BM Citrate'),
(45, 'Citrate'),
(46, 'Bone Marrow'),
(47, 'Peripheral Smear/BM'),
(48, 'BM Smears/P Smears'),
(49, '10 ml Citrate'),
(50, 'Heparin'),
(51, 'Direct Peripheral Smear'),
(52, 'Direct Peripheral Smear/EDTA'),
(53, '2ml Plain Tube'),
(54, 'Contact Phlebotomy'),
(55, 'Cervical/Vaginal Secretions'),
(56, 'Sputum/Nasal Secretions'),
(57, 'Citrate,Tasting'),
(58, 'EDTA / Citrate'),
(59, 'EDTA-6ml'),
(60, 'Bone Marrow / Urine'),
(61, 'periferal smear'),
(62, 'BLOOD(SERUM)'),
(63, 'Tissue'),
(64, 'Pleural Fluid'),
(65, 'Bal Fluid'),
(66, 'Endometrial Tissue'),
(67, 'Pericardial Fluid'),
(68, 'Peritonial Fluid'),
(69, 'Synvial Fluid'),
(70, 'Blood(Blood Fluid)'),
(71, 'Amniotic Fluid'),
(72, 'Pap Smear'),
(73, 'Blood In Plain Tube'),
(74, 'Blood (EDTA)'),
(75, 'Urine 24hrs Collection'),
(76, 'NONE'),
(77, 'Asitic Fluid'),
(78, 'EDTA'),
(79, 'Blood Culture Bottle Bactec/Bactalert'),
(80, 'Sterile Container/Bact Alert Aerobic Bot'),
(81, 'Sterile Swab'),
(82, 'Sterile Container'),
(83, 'Sterile Universal Container'),
(84, 'Sterile Swab In Transport Media'),
(85, 'Suprapubic aspirate in syringe'),
(86, 'Sterile EDTA Tube - Blood'),
(87, 'Plain Tube -Blood'),
(88, 'Bact Alert Bottle'),
(89, 'Water In Sterile Container'),
(90, 'Sterile Container / Sterile Swab'),
(91, 'Bact- Alert Anaerobic/Sterile Syringe'),
(92, 'Sterile Universal in Transport Media'),
(93, 'Urine 24 hrs'),
(94, 'Heparinised Blood in Syringe'),
(95, 'Plain'),
(96, 'Ascitic Fluid'),
(97, 'Fluoride'),
(98, 'Fluoride + Urine'),
(99, 'Fluoride,urine'),
(100, 'Urine(Spot Sample)'),
(101, 'Plain & EDTA'),
(102, 'Smear'),
(104, 'FNAC'),
(105, 'Slide'),
(106, 'Bronchial Aspirate'),
(107, 'Whole Blood + Serum'),
(108, 'Fasting Plasma'),
(109, 'PP Plasma'),
(110, 'None Sample'),
(111, 'Plasma + Serum'),
(112, 'Skin Injection'),
(113, 'Sputam'),
(114, 'Throat Swab'),
(115, 'Vaginal Swab'),
(116, 'Nail Scrapping'),
(117, 'Swab'),
(118, 'Skin Smear'),
(119, 'Urethral Discharge'),
(120, 'Scrapping'),
(121, 'Morning Cortisol'),
(122, 'Evening Cortisol'),
(123, 'Heparin+EDTA'),
(124, 'Random Plasma'),
(125, 'Glucose');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Sample`
--
ALTER TABLE `Sample`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
