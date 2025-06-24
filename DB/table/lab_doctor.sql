-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 24, 2025 at 04:06 PM
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
-- Table structure for table `lab_doctor`
--

CREATE TABLE `lab_doctor` (
  `id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `category` varchar(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `desig` varchar(50) NOT NULL,
  `qual` varchar(50) NOT NULL,
  `phn` varchar(50) NOT NULL,
  `password` varchar(40) NOT NULL,
  `result_approve` int(11) NOT NULL,
  `sign_name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lab_doctor`
--

INSERT INTO `lab_doctor` (`id`, `sequence`, `category`, `name`, `desig`, `qual`, `phn`, `password`, `result_approve`, `sign_name`) VALUES
(103, 1, '1', 'DR. GOVINDA SHARMA', 'Consultant Doctor', 'MBBS,MD,FRCS', '', '1234', 0, '103.jpg'),
(105, 1, '1', 'DR. KESHAB BORA', 'Consultant Biochemist', 'MD', '', '', 0, '105.jpg'),
(106, 1, '1', 'DR. MONIGOPA DAS', 'DOCTOR ON DUTY', '.', '', '', 0, '106.jpg'),
(107, 1, '1', 'DR. SYEDA MOHSINA ROHMAN', 'Consultant Pathologist', '.', '', '', 0, '107.jpg'),
(108, 1, '1', 'DR. RASHMI RAJKAKATI', 'DOCTOR ON DUTY', '.', '', '', 0, '108.jpg'),
(109, 1, '1', 'DR. ALAKA DAS', 'Consultant Biochemist', '.', '', '', 0, '109.jpg'),
(110, 1, '1', 'DR. RUMI DEORI', 'DOCTOR ON DUTY', '.', '', '', 0, '110.jpg'),
(111, 1, '1', 'DR. JAYANTA DAS', 'DOCTOR ON DUTY', '.', '', '', 0, '111.jpg'),
(112, 1, '1', 'DR. BIPUL KR. TALUKDAR', 'DOCTOR ON DUTY', '.', '', '', 0, '112.jpg'),
(113, 1, '1', 'DR. SWARNALI CHOUDHURY', 'DOCTOR ON DUTY', '.', '', '', 0, '113.jpg'),
(114, 1, '1', 'DR. SHAKIBA SHAH', 'DOCTOR ON DUTY', '.', '', '', 0, '114.jpg'),
(115, 1, '1', 'DR. MEENAKSHI SAIKIA', 'DOCTOR ON DUTY', '.', '', '', 0, '115.jpg'),
(116, 1, '1', 'DR. BINOD BURAGOHAIN', 'DOCTOR ON DUTY', '.', '', '', 0, '116.jpg'),
(117, 1, '1', 'DR. ERFANA HAZARIKA', 'DOCTOR ON DUTY', '.', '', '', 0, '117.jpg'),
(118, 1, '1', 'DR. RIKURAJ KONWAR', 'Doctor on Duty', '.', '', '', 0, '118.jpg'),
(119, 1, '1', 'DR. MOITREE LAHON', 'Biochemist / DOD', '.', '', '', 0, '119.jpg'),
(120, 1, '1', 'DR. SANKAR HAZARIKA', 'Biochemist / DOD', '.', '', '', 0, '120.jpg'),
(121, 1, '1', 'DR. BIDYUT BHUYAN', 'Biochemist / DOD', '.', '', '', 0, '121.jpg'),
(122, 1, '1', 'DR. JURI KALITA', 'Biochemist / DOD', '.', '', '', 0, '122.jpg'),
(130, 1, '1', 'DR. PRATIM GUPTA', 'Consultant Biochemist', '-', '', '', 0, '130.jpg'),
(133, 1, '1', 'DR. NISHAN SAHARIA', 'Consultant Biochemist', 'MD', '', '', 0, '133.jpg'),
(134, 1, '1', 'DR. SAJIDA SULTANA RAHMAN', 'Consultant Biochemist', 'MD', '', '', 0, '134.jpg'),
(144, 1, '1', 'DR. LAKEE BARUAH', 'Consultant Pathologist', '..', '', '', 0, '144.jpg'),
(148, 1, '1', 'DR. SHABNAM BORBORAH', ' ', ' ', '', '', 0, '148.jpg'),
(149, 1, '1', 'DR. SALMA AHMED', 'Consultant Biochemist', ' ', '', '', 0, '149.jpg'),
(150, 1, '1', 'DR. GAUTAM KUMAR DAS', 'Consultant Biochemist', ' ', '', '', 0, '150.jpg'),
(151, 1, '1', 'DR. TANIMA BANERJEE', 'Consultant Biochemist', ' ', '', '', 0, '151.jpg'),
(152, 1, '1', 'DR. DIPIKA SINGHA', '.', 'MD', '', '', 0, '152.jpg'),
(153, 1, '1', 'DR. PRIYANKI GOGOI', '.', '.', '', '', 0, '153.jpg'),
(154, 1, '1', 'DR. IRENE AHMED', '.', '.', '', '', 0, '154.jpg'),
(155, 1, '1', 'DR. SOUVIK PRAMANIK', '.', '.', '', '1234', 0, '155.jpg'),
(156, 1, '1', 'DR. JAYASREE SARMA', '.', '.', '', '', 0, '156.jpg'),
(157, 1, '1', 'DR. NIMITA', '.', '.', '', '', 0, '157.jpg'),
(158, 1, '1', 'DR. PRIYASHREE CHOUDHURY', '.', '.', '', '', 0, '158.jpg'),
(159, 1, '1', 'DR. THOKCHOM ROJIA DEVI', '.', '.', '', '', 0, '159.jpg'),
(160, 1, '1', 'DR. ARNAB KUMAR SARMA', '.', '.', '', '', 0, '160.jpg'),
(162, 1, '1', 'DR. DEBASHREE YEIN', '.', '.', '', '', 0, '162.jpg'),
(163, 1, '1', 'DR. KABYASHREE SONOWAL', '.', '.', '', '', 0, '163.jpg'),
(164, 1, '1', 'DR. ANINDITA BISWAS', '.', '.', '', '', 0, '164.jpg'),
(165, 1, '1', 'DRR... THOKCHOMSARITA DEVI', '.', '.', '', '', 0, '165.jpg'),
(166, 1, '1', 'DR. NAHIDA AFREEN', '.', '.', '', '', 0, '166.jpg'),
(167, 1, '1', 'LAKEE BARUAH', '.', '.', '', '', 0, '167.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lab_doctor`
--
ALTER TABLE `lab_doctor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
