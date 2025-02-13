-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2025 at 05:55 AM
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
  `nama` varchar(50) NOT NULL,
  `email_mentor` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mapel` varchar(20) NOT NULL,
  `rating` multipoint NOT NULL,
  `jurnal` varchar(255) NOT NULL,
  `kuis` varchar(255) NOT NULL,
  `profil` varchar(255) NOT NULL,
  `presensi` enum('Masuk','Tidak','Izin') NOT NULL,
  `comment` varchar(255) NOT NULL,
  `jadwal` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nama` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `sekolah` varchar(255) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL,
  `presensi` enum('Masuk','Tidak','Izin') NOT NULL,
  `ttl` date NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `jadwal` varchar(255) NOT NULL,
  `nilai` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
