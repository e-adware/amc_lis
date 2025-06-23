-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2025 at 04:33 PM
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
-- Table structure for table `Units`
--

CREATE TABLE `Units` (
  `ID` int(5) NOT NULL DEFAULT 0,
  `unit_name` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Units`
--

INSERT INTO `Units` (`ID`, `unit_name`) VALUES
(132, 'Microgram%'),
(133, 'NMOL\\L'),
(134, 'mm cm2'),
(136, 'nmol/l'),
(137, '%'),
(138, 'mm (Septum Paradoxical)'),
(139, 'Wt/Ht^2'),
(140, 'pmol/L'),
(141, 'mg/g'),
(142, 'uIU/mL'),
(129, 'Lakhs/cumm'),
(130, 'A.I'),
(131, '&mu;IU/ml.'),
(1, 'Milligram %'),
(2, 'Newton'),
(3, 'gm/dl'),
(4, 'Million/Cumm.'),
(5, '%'),
(6, 'fL'),
(7, 'pg'),
(8, 'g/dl'),
(9, '/Cumm.'),
(10, 'Minutes'),
(11, 'mm in 1st Hr.'),
(12, 'mg/dl'),
(13, 'None'),
(14, 'IU/L'),
(15, 'meq/l'),
(16, 'U/l'),
(17, 'mg/L'),
(18, 'ng/mL'),
(19, 'mIU/ml'),
(20, 'U/ml'),
(21, 'osmol/Kg'),
(22, 'ug/ml'),
(23, 'ug/dl'),
(24, 'mg/day'),
(25, 'gm/day'),
(26, 'ml/min'),
(27, 'Bottle'),
(28, 'Numbers'),
(29, 'Dozen'),
(30, 'Litre'),
(31, 'Gram'),
(32, 'Kilogram'),
(33, 'Quintal'),
(34, 'Millilitre'),
(47, 'IU/ml.'),
(48, 'u mol/l.'),
(49, '< 6% of CPK activity'),
(50, 'mU/mil.RBC\'s'),
(51, 'm mol/l.'),
(52, 'meq/day.'),
(67, 'FRMS'),
(68, 'Grams'),
(69, 'Litre'),
(70, 'Pair'),
(71, 'Pounds'),
(72, 'Packets'),
(77, 'Seconds'),
(78, 'CFT'),
(79, 'Million/ML'),
(80, 'Ug/ml'),
(81, 'uU Insulin/ml.'),
(158, '% of RBC'),
(157, 'million/&mu;L'),
(85, 'Tablets'),
(86, 'minutes second'),
(87, '/100WBC'),
(88, 'of CPK'),
(89, '% of Total CPK'),
(90, 'upto 6% of CPK'),
(91, 'Copies/ML'),
(92, 'Cells'),
(93, 'ml/day'),
(94, 'gm/10gm'),
(95, 'Hrs.'),
(96, 'b/min'),
(97, 'mm'),
(98, 'm/sec'),
(99, 'cm/sec2'),
(100, 'ml'),
(101, 'l/min'),
(102, 'g/cm 2'),
(103, 'pg/mL'),
(104, 'ng/dL'),
(105, 'mU/L'),
(106, 'ug/L'),
(107, '&mu;'),
(108, 'IU/mL'),
(109, 'SERO UNIT'),
(110, 'AU/mL'),
(111, 'UA/ML'),
(112, 'mm/1st hr'),
(113, '&mu;g/ml'),
(114, '/HPF'),
(115, 'l/l'),
(116, 'gm/l'),
(117, '/LPF'),
(118, '% activity of normal'),
(119, '% normal saline'),
(120, 'Organisms/ml'),
(121, 'Bacteria/ml'),
(122, 'mmHg'),
(123, 'mU/10 Billion'),
(124, 'GPL U/ML'),
(125, 'MPL U/ML'),
(126, 'APL U/ML'),
(127, 'PEI U/ML'),
(128, 'ug/24 hrs'),
(135, 'mm.'),
(144, 'Cell/Cumm'),
(145, 'ml/min'),
(146, 'mmol/l'),
(147, 'IU/m'),
(148, '10^3/uL'),
(149, '[10^3/uL]'),
(150, '[10^6/uL]'),
(151, 'mIU/L'),
(152, 'Ul/ml'),
(153, 'gm%'),
(154, 'mm AEFH'),
(155, 'g/g Creatinine '),
(156, 'mg'),
(159, 'GPL AU/mL'),
(160, 'ug/g'),
(161, ' MPL AU/mL'),
(162, ' '),
(163, 'U/L'),
(164, 'mEq/L'),
(165, 'mL/min/1.73 mÂ²'),
(166, 'ng/ml'),
(167, 'uIU/ml'),
(168, ''),
(169, 'mg%'),
(170, 'gm%'),
(171, ''),
(172, 'mg/dL'),
(173, 'GM%'),
(174, 'CUBIC MICRON'),
(175, 'PG'),
(176, 'ng/dl'),
(177, 'nmol/L'),
(178, 'mclu/L'),
(179, 'μg/dl'),
(180, 'μIU/ml'),
(181, 'gm/dl'),
(182, 'U/g Hb.'),
(183, 'µg/mL  FEU'),
(184, 'µg/mL  '),
(185, 'µg/ml FEU'),
(186, 'ng/L');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Units`
--
ALTER TABLE `Units`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
