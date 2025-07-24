-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 22, 2025 at 05:50 PM
-- Server version: 10.1.48-MariaDB-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amc_his`
--

-- --------------------------------------------------------

--
-- Table structure for table `pat_free_master`
--

CREATE TABLE `pat_free_master` (
  `id` int(11) NOT NULL,
  `free_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pat_free_master`
--

INSERT INTO `pat_free_master` (`id`, `free_name`) VALUES
(1, 'Atal Amrit Yojana'),
(2, 'RSBY'),
(3, 'Authorised Person'),
(4, 'Ayushman Bharat'),
(5, 'BPL Holder'),
(6, 'AES'),
(7, 'Project'),
(8, 'Cancer'),
(9, 'RBSK'),
(10, 'ART'),
(11, 'Staff'),
(12, 'Staff Dependent'),
(13, 'Student'),
(14, 'Principal'),
(15, 'Superintendent'),
(16, 'COVID-19'),
(17, 'COVID-19 Suspect'),
(18, 'Covid-19 Treated'),
(19, 'JSY'),
(20, ' ECHS- ex-servicemen health scheme'),
(21, 'PROJECT TOPSPIN TRIAL- CARDIOLOGY project');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pat_free_master`
--
ALTER TABLE `pat_free_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pat_free_master`
--
ALTER TABLE `pat_free_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
