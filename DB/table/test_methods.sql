-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 23, 2025 at 04:34 PM
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
-- Table structure for table `test_methods`
--

CREATE TABLE `test_methods` (
  `id` int(3) NOT NULL DEFAULT 0,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `test_methods`
--

INSERT INTO `test_methods` (`id`, `name`) VALUES
(1, 'Alkaline Picrate'),
(2, 'Accelerated selective detergent method'),
(3, 'Agglutination '),
(4, 'Arsenazo III'),
(5, 'Autometed Coagulometry'),
(6, 'Benedict Reaction '),
(7, 'Biuret endpoint '),
(8, 'Biuret Method'),
(9, 'C.L.I.A'),
(10, 'Calculated'),
(11, 'Chemiluminiscence Assay(Access-2 Beckmam Coulter'),
(12, 'CNP-G3'),
(13, 'Colorimetric'),
(14, 'Colorimetric - glycerol -3 POD'),
(15, 'Colorimetric bromocresol geen'),
(16, 'Colorimetric with precipitation'),
(17, 'Coulter Principle'),
(18, 'Coulter Principle & Microscopy'),
(19, 'DGKC-Enzymatic Kinetic'),
(20, 'Dipstick & Microscopy'),
(21, 'Dipstix'),
(22, 'E.C.L.I.A'),
(23, 'Electrochemiluminiscence'),
(24, 'Elisa'),
(25, 'Enzymatic -UV Kinetic'),
(26, 'Enzymatic Uricase colorimetric -trinder endpoint '),
(27, 'Enzymatic-colorimetric Trinder endpoint'),
(28, 'Enzyme linked Immunofluorescent Asay'),
(29, 'FERROZINE'),
(30, 'Flocculation'),
(31, 'Glucose Oxidase'),
(32, 'Gross Examination, Microscopy'),
(33, 'Heat method'),
(34, 'Hemospot'),
(35, 'HPLC'),
(36, 'Immunochromatography'),
(37, 'Immunoturbidimetric Method'),
(38, 'Impedance Hydrofocus & Microscopy'),
(39, 'Ion Selective Electrode'),
(40, 'Ion Specific Electrode '),
(41, 'Ivy Method'),
(42, 'Kinetic'),
(43, 'Manual Method'),
(44, 'Microscopical'),
(45, 'Microscopy'),
(46, 'Modified IFCC-enzymatic , Kinetic'),
(47, 'PBS + Microscopy'),
(48, 'PEP'),
(49, 'Phophomolybdate'),
(50, 'Photometric -DCA'),
(51, 'Photometry'),
(52, 'Pyrogallol red / molybdate'),
(53, 'Spectrophotometry'),
(54, 'Supravital staining & Smear study'),
(55, 'Tube Agglutination method'),
(56, 'Turbidimetry'),
(57, 'Urine Analyser'),
(58, 'Uristix'),
(59, 'UV IFCC'),
(60, 'Westergren method'),
(61, 'Xilydil Blue'),
(62, 'Z N Stain & Micrscopy'),
(63, 'Immunoenzymatic colorimetric'),
(64, 'R S Photometric'),
(65, 'Multiple - point rate'),
(66, 'Two-point rate'),
(67, 'End-point colorimetric'),
(68, 'potentiometric'),
(69, 'Fixed-point immuno-rate'),
(70, 'immunometric'),
(71, 'Competitive binding assay'),
(72, 'Competitive binding assay'),
(73, 'Competitive immunoassay'),
(74, 'Diphylline diazonium salt'),
(75, 'BCG'),
(76, 'PMPP,AMP BUFFER'),
(77, 'UV WITH P5P'),
(78, 'Bromophenol blue'),
(79, 'Amylopectin'),
(80, 'Direct measure'),
(81, 'Urease,UV'),
(82, 'Cholesterol oxidase,esterase,peroxidase'),
(83, 'Rosalki,other modified'),
(84, 'ISE direct'),
(85, 'Two point rate-enzymatic'),
(86, 'Direct measure,PTA/MgCl2'),
(87, 'Pyridol azo dye'),
(88, 'G-glutamyl-p-nitroanilide'),
(89, 'Lactate to pyruvate'),
(90, 'Enzymatic with colipase'),
(91, 'Formazan dye'),
(92, 'Two point rate-Alumina'),
(93, 'Biuret,end point'),
(94, 'Uricase'),
(95, 'Phosphomolybdate reduction'),
(96, 'Enzymatic-end point'),
(97, 'gtt'),
(98, 'Calculated'),
(99, 'Turbidimetry-2 point rate'),
(100, 'iron'),
(101, 'Enzymatic,colorimetric'),
(102, 'UV WITH P-5-P (IFCC)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `test_methods`
--
ALTER TABLE `test_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
