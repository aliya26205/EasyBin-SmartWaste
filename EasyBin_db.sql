-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql304.infinityfree.com
-- Generation Time: Feb 23, 2026 at 12:40 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41140604_easybin`
--

-- --------------------------------------------------------

--
-- Table structure for table `bins`
--

CREATE TABLE `bins` (
  `bin_id` varchar(20) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `alert_sent` tinyint(4) DEFAULT 0,
  `prev_status` varchar(10) DEFAULT 'EMPTY'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bins`
--

INSERT INTO `bins` (`bin_id`, `location`, `distance`, `status`, `last_updated`, `alert_sent`, `prev_status`) VALUES
('BIN001', 'Canteen', 18, 'HALF', '2026-02-23 05:21:24', 0, 'HALF'),
('BIN002', 'Library', 30, 'EMPTY', '2026-02-12 11:12:40', 0, 'EMPTY'),
('BIN003', 'Admin block', 0, 'EMPTY', '2026-02-13 05:03:57', 0, 'EMPTY');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `profile_photo` varchar(255) DEFAULT 'default_profile.png',
  `cover_photo` varchar(255) DEFAULT 'default_cover.jpg',
  `about` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `created_at`, `profile_photo`, `cover_photo`, `about`) VALUES
(......);

-- --------------------------------------------------------

--
-- Table structure for table `workers`
--

CREATE TABLE `workers` (
  `worker_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `workers`
--

INSERT INTO `workers` (`worker_id`, `name`, `phone`, `area`, `created_at`) VALUES
(2, 'Aliya Banu', '917259746462', 'Main Block', '2026-02-18 15:34:37'),
(4, 'chaitra', '916235985790', 'Ground', '2026-02-18 16:35:32'),
(5, 'Deeksha', '9167890432', 'Canteen', '2026-02-19 08:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `work_assignments`
--

CREATE TABLE `work_assignments` (
  `id` int(11) NOT NULL,
  `worker_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `work_time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `work_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('ASSIGNED','COMPLETED') DEFAULT 'ASSIGNED',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `work_assignments`
--

INSERT INTO `work_assignments` (`id`, `worker_id`, `work_date`, `work_time`, `location`, `work_type`, `description`, `status`, `created_at`) VALUES
(15, 2, '2026-02-20', '17:43:00', 'Academic Block 2', 'Cleaning', 'gg', 'ASSIGNED', '2026-02-19 09:10:58'),
(13, 2, '2026-02-20', '12:41:00', 'Academic Block 2', 'Cleaning', '', 'ASSIGNED', '2026-02-19 05:09:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bins`
--
ALTER TABLE `bins`
  ADD PRIMARY KEY (`bin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`worker_id`);

--
-- Indexes for table `work_assignments`
--
ALTER TABLE `work_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `worker_id` (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `worker_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `work_assignments`
--
ALTER TABLE `work_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
