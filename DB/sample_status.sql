-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 06, 2025 at 04:06 PM
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
-- Database: `smch_lis`
--

-- --------------------------------------------------------

--
-- Table structure for table `sample_status`
--

CREATE TABLE `sample_status` (
  `sample_id` int(11) NOT NULL,
  `status_name` char(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sample_status`
--

INSERT INTO `sample_status` (`sample_id`, `status_name`) VALUES
(1, 'Mislabelled'),
(2, 'Leak'),
(3, 'Hemolysed'),
(4, 'Wrong Container'),
(5, 'Inadequate'),
(6, 'Clotted Vial'),
(7, 'Incomplete Form'),
(8, 'Contaminated'),
(9, 'Turbid'),
(10, 'Icteric'),
(11, 'Variant Window'),
(12, 'Lipaemic'),
(13, 'Sample Not Received'),
(14, 'Others');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sample_status`
--
ALTER TABLE `sample_status`
  ADD PRIMARY KEY (`sample_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sample_status`
--
ALTER TABLE `sample_status`
  MODIFY `sample_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
