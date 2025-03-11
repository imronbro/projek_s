-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2025 at 04:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scover`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `email` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`email`, `full_name`, `password`) VALUES
('scover@gmail.com', 'Scover Center', '$2y$10$ywznVv6AOYCrdHzU4zbODeyV5onBoEndzHIANdpfTosM.GW/IyBUu');

-- --------------------------------------------------------

--
-- Table structure for table `kuis`
--

CREATE TABLE `kuis` (
  `nama` varchar(255) NOT NULL,
  `kelas` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `nilai` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor`
--

CREATE TABLE `mentor` (
  `pengajar_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mapel` varchar(20) NOT NULL,
  `nohp` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `ttl` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor`
--

INSERT INTO `mentor` (`pengajar_id`, `full_name`, `email`, `password`, `mapel`, `nohp`, `gambar`, `alamat`, `ttl`, `created_at`) VALUES
(2, 'Firman Tua Parhusip', 'firmanparhusip65@gmail.com', '$2y$10$A4Iq2nhKxBY/FPAto19VSuaSo3jBPW8ZGSa9kNe/u/W2sRChmEc9q', 'BIOLOGI', '+62895393687196', 'uploads/Twibon GDGoC.png', 'Banyuwangi', '2003-11-11', '2025-03-03 04:22:13'),
(3, 'affan', 'affan@gmail.com', '$2y$10$88UaWSFD57HVpPxKL7gI..CT14KgkoHzFflGJ7J23OF8aVAopeOJy', 'FISIKA', '083223456782', 'uploads/Screenshot (7).png', 'Gadang', '2004-08-10', '2025-03-03 04:08:10'),
(4, 'Imron', 'imron@gmail.com', '$2y$10$/1dsbGGdg6TT113vQjrrCu/4eucNqov3Bt6wEmuBmH/YqkCalcydi', 'KIMIA', '083223456781', 'uploads/Screenshot (5).png', 'Kediri', '2005-02-02', '2025-03-03 04:09:41'),
(5, 'Wafiq', 'wafiq@gmail.com', '$2y$10$voarSQnfb4wMd917XOZmCeKdPUXjdFaYHsVpB9jpI0YdCokcTvIT6', 'MATEMATIKA', '083223456786', 'uploads/Screenshot 2025-02-12 153724.png', 'Jl. Veteran UNIVERSITAS NEGERI MALANG', '2003-11-11', '2025-03-03 04:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_siswa`
--

CREATE TABLE `nilai_siswa` (
  `id` int(11) NOT NULL,
  `pengajar_id` int(11) DEFAULT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `nama_kuis` varchar(255) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_siswa`
--

INSERT INTO `nilai_siswa` (`id`, `pengajar_id`, `siswa_id`, `nama_kuis`, `nilai`, `waktu`) VALUES
(1, 2, 3, 'Kuis Math', 99, '2025-03-10 05:23:11'),
(2, 2, 4, 'Kuis BIO A', 100, '2025-03-10 06:47:29'),
(3, 2, 5, 'Kuis BIO A', 100, '2025-03-10 06:58:51'),
(4, 2, 3, 'fi', 80, '2025-03-10 07:02:22'),
(5, 3, 4, 'Kuis BIO A', 100, '2025-03-10 07:36:05'),
(6, 3, 3, 'Kuis BIO A', 100, '2025-03-10 07:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_pengajar`
--

CREATE TABLE `presensi_pengajar` (
  `id` int(11) NOT NULL,
  `pengajar_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `sesi` enum('Sesi 1','Sesi 2','Sesi 3','Sesi 4','Sesi 5','Sesi 6','Sesi 7') NOT NULL,
  `tempat` varchar(100) NOT NULL,
  `status` enum('Hadir','Izin','Sakit') NOT NULL,
  `komentar` text DEFAULT NULL,
  `waktu_presensi` timestamp NOT NULL DEFAULT current_timestamp(),
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi_pengajar`
--

INSERT INTO `presensi_pengajar` (`id`, `pengajar_id`, `full_name`, `tanggal`, `sesi`, `tempat`, `status`, `komentar`, `waktu_presensi`, `gambar`) VALUES
(19, 2, 'Firman Tua Parhusip', '2025-03-06', 'Sesi 1', 'SMA AL-Izzah', 'Hadir', '', '2025-03-06 05:09:10', 'uploads/ppppp.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `presensi_siswa`
--

CREATE TABLE `presensi_siswa` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `sesi` enum('Sesi 1','Sesi 2','Sesi 3','Sesi 4','Sesi 5','Sesi 6','Sesi 7') NOT NULL,
  `status` enum('Hadir','Izin','Sakit') NOT NULL,
  `komentar` text DEFAULT NULL,
  `waktu_presensi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presensi_siswa`
--

INSERT INTO `presensi_siswa` (`id`, `siswa_id`, `full_name`, `tanggal`, `sesi`, `status`, `komentar`, `waktu_presensi`) VALUES
(5, 3, '', '2025-02-24', 'Sesi 1', 'Hadir', NULL, '2025-02-24 05:15:27'),
(6, 3, 'Firman Tua Parhusip', '2025-02-24', 'Sesi 1', 'Hadir', NULL, '2025-02-24 05:21:52'),
(7, 3, 'Firman Tua Parhusip', '2025-02-25', 'Sesi 4', 'Izin', 'malas', '2025-02-24 05:26:04'),
(8, 4, 'imron', '2025-02-24', 'Sesi 3', 'Sakit', 'Demam', '2025-02-24 05:30:04'),
(9, 4, 'imron', '2025-02-24', 'Sesi 1', 'Hadir', NULL, '2025-02-24 05:31:24'),
(10, 4, 'imron', '2025-02-24', 'Sesi 1', 'Hadir', NULL, '2025-02-24 05:31:27'),
(11, 3, 'Firman Tua Parhusip', '2025-02-24', 'Sesi 1', 'Hadir', NULL, '2025-02-24 12:33:09'),
(12, 3, 'Firman Tua Parhusip', '2025-02-24', 'Sesi 1', 'Izin', 'mungkin', '2025-02-24 12:53:43'),
(13, 3, 'Firman Tua Parhusip', '2025-02-27', 'Sesi 1', 'Hadir', NULL, '2025-02-27 11:22:18'),
(14, 3, 'Firman Tua Parhusip', '2025-02-28', 'Sesi 2', 'Izin', 'olimpiade', '2025-02-28 02:30:04'),
(15, 3, 'Firman Tua Parhusip', '2025-03-02', 'Sesi 4', 'Izin', NULL, '2025-03-02 02:26:54'),
(16, 3, 'Firman Tua Parhusip', '2025-03-02', 'Sesi 5', 'Hadir', NULL, '2025-03-02 09:25:22'),
(17, 3, 'Firman Tua Parhusip', '2025-03-03', 'Sesi 1', 'Hadir', NULL, '2025-03-03 03:20:27'),
(18, 3, 'Firman Tua Parhusip', '0000-00-00', 'Sesi 3', 'Sakit', 'dfghj', '2025-03-05 02:46:49');

-- --------------------------------------------------------

--
-- Table structure for table `rating_pengajar`
--

CREATE TABLE `rating_pengajar` (
  `id` int(11) NOT NULL,
  `pengajar_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_pengajar`
--

INSERT INTO `rating_pengajar` (`id`, `pengajar_id`, `siswa_id`, `rating`, `komentar`, `created_at`) VALUES
(2, 3, 3, 5, 'mantap man', '2025-03-04 04:24:34'),
(3, 4, 3, 4, 'lumayan', '2025-03-04 04:32:23'),
(4, 4, 4, 3, 'anjay', '2025-03-04 04:51:41'),
(5, 5, 3, 5, 'l\r\n', '2025-03-04 06:30:46'),
(6, 2, 3, 3, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-03-05 04:45:46'),
(7, 2, 3, 4, 'rarww', '2025-03-05 04:47:04'),
(8, 2, 3, 5, 'rr', '2025-03-05 04:55:32'),
(9, 2, 3, 1, 'd', '2025-03-05 04:56:35'),
(10, 2, 4, 5, '', '2025-03-10 04:01:25'),
(11, 2, 4, 5, '', '2025-03-10 04:01:27'),
(12, 2, 4, 5, '', '2025-03-10 04:01:30'),
(13, 2, 3, NULL, '', '2025-03-10 04:15:53'),
(14, 2, 3, NULL, '', '2025-03-10 04:44:55'),
(15, 4, 3, NULL, '', '2025-03-10 04:51:35'),
(16, 2, 3, NULL, '', '2025-03-10 04:52:48'),
(17, 3, 3, 4, '', '2025-03-10 05:03:02'),
(18, 2, 3, NULL, '', '2025-03-10 05:03:06'),
(19, 2, 3, 5, '', '2025-03-10 05:03:09'),
(20, 2, 6, 5, 'mantap bang', '2025-03-10 05:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `sekolah` varchar(255) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ttl` date NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `nohp` varchar(50) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `full_name`, `email`, `sekolah`, `kelas`, `password`, `ttl`, `alamat`, `nohp`, `gambar`, `created_at`) VALUES
(3, 'Firman Tua Parhusip', 'firmanparhusip65@gmail.com', 'SMA 2 Malang', '12', '$2y$10$DfQdNluCC7GY.fY0dBw8VucgIimtEXbaBRMw4P4ARgYr3awsRk3Me', '2004-03-27', 'Jl. Veteran UNIVERSITAS NEGERI MALANG', '083223456787', 'uploads/poto gw.jpg', '2025-02-27 12:22:37'),
(4, 'imron', 'imron@gmail.com', 'SMA 2 Malang', '12 SMA', '$2y$10$1GATuIkj2mBkL2li/vQaIe7rXBss6PE4hX7fIIiQb.bNIkzYuVvUC', '2004-11-11', 'Kediri', '083223456784', 'uploads/Screenshot (7).png', '2025-02-24 05:30:52'),
(5, 'Firman Tua', 'firmanparhusip03@gmail.com', '', '', '$2y$10$WNUEmHSQcVJ3AnT4FsCin.5/hW6J6MTnSF8bdAM2bMqcE6Ny4nlfW', '0000-00-00', '', '', '', '2025-03-02 15:44:07'),
(6, 'Fadhil', 'fadhil@gmail.com', 'MAN Tahah laut', '12', '$2y$10$RY4AsQWcXMAncKm3KR.9xuY4CgJ2N.MMyfQQ3kFm5bMYs4TR1c2Iu', '2025-02-05', 'kediri', '+628534422116', 'uploads/WIN_20250126_15_56_18_Pro.jpg', '2025-03-05 03:30:48'),
(7, 'Smith', 'smith@gmail.com', 'SMA DEL', '12', '$2y$10$ZvIrV7sb/MGizDgx9UKrpepkQF.jkvVUG3KcrQP.ik2GAQXlBpgAG', '2004-03-10', 'Amerika', '+628223564783', 'uploads/smith.jpeg', '2025-03-10 08:16:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`full_name`);

--
-- Indexes for table `mentor`
--
ALTER TABLE `mentor`
  ADD PRIMARY KEY (`pengajar_id`);

--
-- Indexes for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajar_id` (`pengajar_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `presensi_pengajar`
--
ALTER TABLE `presensi_pengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajar_id` (`pengajar_id`);

--
-- Indexes for table `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `rating_pengajar`
--
ALTER TABLE `rating_pengajar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajar_id` (`pengajar_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswa_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mentor`
--
ALTER TABLE `mentor`
  MODIFY `pengajar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `presensi_pengajar`
--
ALTER TABLE `presensi_pengajar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rating_pengajar`
--
ALTER TABLE `rating_pengajar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nilai_siswa`
--
ALTER TABLE `nilai_siswa`
  ADD CONSTRAINT `nilai_siswa_ibfk_1` FOREIGN KEY (`pengajar_id`) REFERENCES `mentor` (`pengajar_id`),
  ADD CONSTRAINT `nilai_siswa_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`);

--
-- Constraints for table `presensi_pengajar`
--
ALTER TABLE `presensi_pengajar`
  ADD CONSTRAINT `presensi_pengajar_ibfk_1` FOREIGN KEY (`pengajar_id`) REFERENCES `mentor` (`pengajar_id`);

--
-- Constraints for table `presensi_siswa`
--
ALTER TABLE `presensi_siswa`
  ADD CONSTRAINT `presensi_siswa_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`);

--
-- Constraints for table `rating_pengajar`
--
ALTER TABLE `rating_pengajar`
  ADD CONSTRAINT `rating_pengajar_ibfk_1` FOREIGN KEY (`pengajar_id`) REFERENCES `mentor` (`pengajar_id`),
  ADD CONSTRAINT `rating_pengajar_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
