-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2025 at 03:47 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kripto`
--

-- --------------------------------------------------------

--
-- Table structure for table `file_rahasia`
--

CREATE TABLE `file_rahasia` (
  `id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `nama_file` varchar(200) NOT NULL,
  `path_asli` varchar(200) NOT NULL,
  `path_enkripsi` varchar(200) NOT NULL,
  `kunci` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `file_rahasia`
--

INSERT INTO `file_rahasia` (`id`, `judul`, `nama_file`, `path_asli`, `path_enkripsi`, `kunci`, `timestamp`) VALUES
(1, 'ini testing satu', 'anjayy.jpg', '../uploads_asli/anjayy.jpg', '../hasil_enkripsi/enc_anjayy.jpg', '999', '2025-11-06 14:47:59'),
(2, 'prabo', '⭐.png', '../uploads_asli/⭐.png', '../hasil_enkripsi/enc_⭐.png', '999', '2025-11-06 14:52:47'),
(3, 'rudal hitam', 'maps.png', '../uploads_asli/maps.png', '../hasil_enkripsi/enc_maps.png', '999', '2025-11-06 15:43:32');

-- --------------------------------------------------------

--
-- Table structure for table `teks_super`
--

CREATE TABLE `teks_super` (
  `id` int NOT NULL,
  `judul` text NOT NULL,
  `hasil_enkripsi` text NOT NULL,
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `teks_super`
--

INSERT INTO `teks_super` (`id`, `judul`, `hasil_enkripsi`, `waktu`) VALUES
(9, 'Testting satu', '206b6b2a2a3e293e292f25262a2a', '2025-11-06 15:42:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `created_at`, `updated_at`) VALUES
(3, 'azzam', '$argon2id$v=19$m=65536,t=4,p=1$cUIyTlFXc0cyRm0yTi9DeA$tnLBK0fLNE4e2uCVlgXRrBwM5BJpmi2i+2km8uOqUdg', 'yanto@gmail.com', 'sulthan azzam', '2025-11-06 15:28:59', '2025-11-06 15:28:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file_rahasia`
--
ALTER TABLE `file_rahasia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teks_super`
--
ALTER TABLE `teks_super`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file_rahasia`
--
ALTER TABLE `file_rahasia`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teks_super`
--
ALTER TABLE `teks_super`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
