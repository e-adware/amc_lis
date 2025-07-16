-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 16, 2025 at 01:48 PM
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
-- Table structure for table `testmaster`
--

CREATE TABLE `testmaster` (
  `testid` int(11) NOT NULL,
  `test_code` varchar(10) DEFAULT NULL,
  `testname` varchar(100) NOT NULL,
  `rate` decimal(19,2) NOT NULL,
  `instruction` varchar(1000) NOT NULL,
  `notes` varchar(1000) NOT NULL,
  `turn_around_time_routine_str` varchar(20) DEFAULT NULL,
  `turn_around_time_routine` int(11) DEFAULT NULL COMMENT 'minutes',
  `turn_around_time_urgent_str` varchar(20) DEFAULT NULL,
  `turn_around_time_urgent` int(11) DEFAULT NULL,
  `report_delivery` int(11) NOT NULL COMMENT '0=same day,1=next day,2=3rd day...',
  `sample_details` varchar(500) NOT NULL,
  `category_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `equipment` int(10) NOT NULL,
  `sex` varchar(10) NOT NULL DEFAULT 'all',
  `lineno` int(10) NOT NULL,
  `vac_charge` int(11) NOT NULL,
  `out_sample` int(11) NOT NULL COMMENT '0=in, 1=out',
  `suffix` varchar(10) NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testmaster`
--

INSERT INTO `testmaster` (`testid`, `test_code`, `testname`, `rate`, `instruction`, `notes`, `turn_around_time_routine_str`, `turn_around_time_routine`, `turn_around_time_urgent_str`, `turn_around_time_urgent`, `report_delivery`, `sample_details`, `category_id`, `type_id`, `type_name`, `equipment`, `sex`, `lineno`, `vac_charge`, `out_sample`, `suffix`, `sequence`) VALUES
(6, '', 'GLUCOSE FBS', 50.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 2, 0, '', 1),
(7, '', 'GLUCOSE PPBS', 50.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 2, 2, 0, '', 2),
(33, '', 'LFT (LIVER FUNCTION TEST)', 600.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 4),
(34, '', 'GAMMA GT', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 5),
(36, '', 'LIPID PROFILE', 500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 6),
(37, '', 'KFT', 150.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 7),
(40, '', 'AST', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 8),
(41, '', 'ALT', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 9),
(43, '', 'ALP', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 10),
(45, '', 'TOTAL PROTEIN', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 11),
(47, '', 'TOTAL CHOLESTEROL ', 80.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 12),
(48, '', 'TRIGLYCERIDES ', 150.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 13),
(49, '', 'HDL CHOLESTEROL ', 150.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 14),
(50, '', 'LDL CHOLESTEROL', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 15),
(52, '', 'UREA ', 80.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 17),
(53, '', 'CREATININE', 80.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 18),
(54, '', 'URIC ACID ', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 19),
(55, '', 'CALCIUM ', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 20),
(56, '', 'SODIUM ', 75.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 21),
(57, '', 'POTASSIUM ', 75.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 22),
(58, '', 'CHLORIDE ', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 23),
(61, '', 'AMYLASE', 350.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 24),
(62, '', 'LIPASE', 350.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 25),
(64, '', 'CPK', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 26),
(90, '', 'RA FACTOR ', 250.00, '', '', '0@0#0', 0, '0@0#0', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 0, 0, 0, '', 29),
(285, '', 'TRIPLE TEST ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 35),
(356, '', 'GTT (EXTENDED)', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 41),
(445, '', 'MAGNESIUM', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 46),
(507, '', 'PROLACTIN', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 55),
(515, '', 'GTT', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 57),
(584, '', 'VITAMIN D3', 1500.00, '', '', '0@0#0', 0, '0@0#0', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 66),
(591, '', 'ESTRADIOL', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 68),
(684, '', 'ACL(ANTI CARDIOLIPIN)', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 81),
(813, '', 'VITAMIN B12 ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 91),
(814, '', 'DHEA-S (PATH- BC086)', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 92),
(831, '', 'SERUM INSULIN (F) ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 94),
(832, '', 'SERUM INSULIN (PP)  ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 95),
(833, '', 'SERUM INSULIN ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 96),
(840, '', 'PCOD(Polycystic ovarian disease) Panel', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 97),
(865, '', 'NT ProBNP (Done by Electro Chemi)', 2500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 101),
(872, '', 'LDL/HDL RATIO', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 103),
(882, '', 'TT3', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 106),
(893, '', 'TT4', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 107),
(904, '', 'TSH ', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 108),
(915, '', 'THYROID PROFILE', 500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 109),
(1058, '', 'G-6PD', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 118),
(1078, '', 'CK-MB', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 119),
(1080, '', 'LDH', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 120),
(1083, '', 'CRP ', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 122),
(1085, '', 'PSA', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 123),
(1114, '', 'BUN ', 80.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 125),
(1119, '', 'DIRECT (TIBC)', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 126),
(1136, '', 'BETA HCG ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 128),
(1140, '', 'PHOSPHORUS', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 129),
(1144, '', 'IRON', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 130),
(1160, '', 'FERRITIN ', 500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 131),
(1170, '', 'ALBUMIN ', 80.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 133),
(1181, '', 'FSH ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 137),
(1184, '', 'Free T4 ', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 138),
(1188, '', 'LH ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 139),
(1190, '', 'PLASMA AMMONIA (NH3)', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 141),
(1220, '', 'GLOBULIN ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 143),
(1247, '', 'Free T3 ', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 145),
(1256, '', 'CA-125  ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 147),
(1265, '', 'IgM HEPATITIS B CORE', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 148),
(1279, '', 'PROGESTERON', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 151),
(1282, '', 'IgE SERUM ', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 1, 1, 0, '', 152),
(1286, '', 'Hs TROPONIN-I', 750.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 153),
(1317, '', 'SERUM ELECTROLYTES', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 157),
(1327, '', 'GLUCOSE RBS', 50.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 2, 0, '', 3),
(1333, '', 'VLDL CHOLESTEROL', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 158),
(1355, '', 'ADA ', 400.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 160),
(1470, '', 'D-DIMER', 1000.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 1, 1, 0, '', 170),
(1615, '', 'A/G RATIO', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 1, 0, '', 177),
(2514, '', 'FREE T3 & FREE T4', 400.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 2, 'all', 0, 0, 0, '', 209),
(2521, '', 'PROCALCITONIN', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 212),
(2568, '', 'HBA1C', 500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 0, 0, 0, '', 214),
(2611, '', 'CPK-MB', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 0, 0, 0, '', 230),
(2651, '', 'BILURUBIN TOTAL', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 234),
(2657, '', 'ASO', 250.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 235),
(2667, '', 'Globlin', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 238),
(2668, '', 'ALK', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 239),
(2691, '', 'VITAMIN D', 1500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 242),
(2703, '', 'ANTI HEV', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 245),
(2800, '', 'BILIRUBIN CONJUGATED', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 265),
(2802, '', 'BILIRUBIN UNCONJUGATED', 100.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 266),
(2803, '', 'RF', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 267),
(2804, '', 'CA 125', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 268),
(2805, '', 'CEA', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 269),
(2806, '', 'BILIRUBIN & FRACTIONS', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 270),
(2807, '', 'GLUCOSE CHALLENGE TEST', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 271),
(2808, '', 'MINI GTT', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 272),
(2809, '', 'SODIUM+POTASSIUM', 150.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 273),
(2810, '', 'PROTEIN & FRACTIONS', 200.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 274),
(2811, '', 'NEONATAL BILIRUBIN & FRACTIONS', 150.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 275),
(2812, '', 'GTT FOR PREGNANCY', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 276),
(2813, '', 'LIVER ENZYME', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 277),
(2814, '', 'HB Typing', 500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 278),
(2815, '', 'OGTT', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 279),
(2816, '', 'IRON PROFILE', 850.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 280),
(2817, '', 'SARS-COV2 IG', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 281),
(2818, '', 'NT-proBNP-5600', 2500.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 282),
(2819, '', 'Free PSA', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 284),
(2820, '', 'AFP', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 0, 'all', 0, 0, 0, '', 0),
(2821, '', 'GLUCOSE RBS(SR)', 50.00, '', '', '0@0#0', 0, '0@0#0', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 0, 0, 0, '', 3),
(2822, '', 'GLUCOSE FBS (SR)', 0.00, '', '', '', 0, '', 0, 0, '', 1, 20, 'Biochemistry', 1, 'all', 1, 2, 0, '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `testmaster`
--
ALTER TABLE `testmaster`
  ADD PRIMARY KEY (`testid`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `testname` (`testname`),
  ADD KEY `out_sample` (`out_sample`),
  ADD KEY `equipment` (`equipment`),
  ADD KEY `sex` (`sex`),
  ADD KEY `vac_charge` (`vac_charge`),
  ADD KEY `lineno` (`lineno`),
  ADD KEY `report_delivery_2` (`report_delivery`),
  ADD KEY `sequence` (`sequence`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `testmaster`
--
ALTER TABLE `testmaster`
  MODIFY `testid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2823;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
