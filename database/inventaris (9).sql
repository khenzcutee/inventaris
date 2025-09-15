-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 12:00 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventaris`
--

-- --------------------------------------------------------

--
-- Table structure for table `divisi`
--

CREATE TABLE `divisi` (
  `id` int(11) NOT NULL,
  `nama_divisi` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisi`
--

INSERT INTO `divisi` (`id`, `nama_divisi`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'General Affair', '2025-08-12', '2025-08-12', 0, 0),
(2, 'Service', '2025-08-19', '2025-08-19', 1, 1),
(4, 'Aftersales', '2025-08-26', '2025-08-26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id` int(11) NOT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `nomor_stnk` varchar(100) NOT NULL,
  `bahan_bakar` varchar(55) NOT NULL,
  `warna` varchar(55) NOT NULL,
  `jenis_kendaraan` varchar(100) NOT NULL,
  `merek` varchar(100) NOT NULL,
  `kilometer` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `id_lokasi` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id`, `plat_nomor`, `nomor_stnk`, `bahan_bakar`, `warna`, `jenis_kendaraan`, `merek`, `kilometer`, `gambar`, `id_lokasi`, `id_status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'B 3759 KC', '1234567890', 'Bensin', 'Putih1', 'Motor', 'Yamaha', 10000, 'mio.jpg', 1, 1, '2025-08-12', '2025-08-22', 1, 1),
(2, 'B 3647 KPT', '1234567890', 'Bensin', 'Kuning', 'Motor', 'Hino', 176456423, 'img_68a52a4e2ad897.97425001.jpg', 1, 1, '2025-08-20', '2025-08-26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lokasi`
--

CREATE TABLE `lokasi` (
  `id` int(11) NOT NULL,
  `nama_lokasi` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lokasi`
--

INSERT INTO `lokasi` (`id`, `nama_lokasi`, `alamat`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Maxi Kelapa Gading1', 'Royal Gading Square RG-10/18 B, JL. Pegangsaan Dua, RT.4/RW.4, Tugu Sel., Kec. Koja, Jkt Utara, Daerah Khusus Ibukota Jakarta 14260', '2025-08-12', '2025-08-12', 0, 0),
(2, 'Maxi Marunda', 'Kawasan Industri, Marunda Center No.6 Blok H1, Sagara Makmur, Kec. Tarumajaya, Kabupaten Bekasi, Jawa Barat 17211', '2025-08-19', '2025-08-19', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pemakaian`
--

CREATE TABLE `pemakaian` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_keluar` date NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `id_status` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemakaian`
--

INSERT INTO `pemakaian` (`id`, `id_user`, `id_inventaris`, `tanggal_keluar`, `tanggal_masuk`, `id_status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(18, 1, 2, '2025-08-21', '2025-08-21', 5, '2025-08-21', '2025-08-21', 1, 1),
(19, 1, 1, '2025-08-21', '2025-08-21', 5, '2025-08-21', '2025-08-21', 1, 1),
(22, 1, 2, '2025-08-21', '2025-08-21', 5, '2025-08-21', '2025-08-21', 1, 1),
(23, 1, 2, '2025-08-21', '2025-08-21', 5, '2025-08-21', '2025-08-21', 1, 1),
(24, 1, 1, '2025-08-21', '2025-08-21', 5, '2025-08-21', '2025-08-21', 1, 1),
(25, 1, 2, '2025-08-22', '2025-08-22', 5, '2025-08-22', '2025-08-22', 1, 1),
(29, 1, 2, '2025-08-22', '2025-08-25', 5, '2025-08-22', '2025-08-22', 1, 1),
(33, 4, 1, '2025-08-22', '2025-08-25', 5, '2025-08-22', '2025-08-22', 4, 4),
(39, 1, 2, '2025-08-25', '0000-00-00', 7, '2025-08-25', '2025-08-25', 1, 1),
(40, 1, 2, '2025-08-25', '2025-08-25', 5, '2025-08-25', '2025-08-25', 1, 1),
(41, 1, 2, '2025-08-25', '0000-00-00', 7, '2025-08-25', '2025-08-25', 1, 1),
(42, 1, 2, '2025-08-25', '2025-08-26', 5, '2025-08-25', '2025-08-25', 1, 1),
(43, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(44, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(45, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(46, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(47, 1, 1, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(48, 1, 1, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(49, 4, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(50, 1, 1, '2025-08-26', '0000-00-00', 5, '2025-08-26', '2025-08-26', 1, 1),
(51, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(52, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(53, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(54, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1),
(56, 1, 2, '2025-08-26', '0000-00-00', 7, '2025-08-26', '2025-08-26', 1, 1),
(57, 1, 2, '2025-08-26', '0000-00-00', 7, '2025-08-26', '2025-08-26', 1, 1),
(58, 1, 2, '2025-08-26', '0000-00-00', 7, '2025-08-26', '2025-08-26', 1, 1),
(59, 1, 2, '2025-08-26', '0000-00-00', 7, '2025-08-26', '2025-08-26', 1, 1),
(60, 1, 2, '2025-08-26', '2025-08-26', 5, '2025-08-26', '2025-08-26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `id_status` int(11) NOT NULL,
  `tanggal_request` datetime NOT NULL,
  `tanggal_digunakan` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`id`, `id_user`, `id_kendaraan`, `id_status`, `tanggal_request`, `tanggal_digunakan`, `keterangan`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 1, 1, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 0, 0),
(2, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 0, 0),
(3, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(4, 1, 1, 9, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(5, 1, 2, 9, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(6, 1, 2, 9, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(7, 1, 2, 9, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(8, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(9, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(10, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(11, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(12, 1, 2, 7, '0000-00-00 00:00:00', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1),
(13, 1, 2, 9, '2025-08-26 16:24:39', NULL, NULL, '2025-08-26', '2025-08-26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nama_roles` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nama_roles`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(3, 'Super Admin', '2025-08-12', '2025-08-12', 1, 1),
(4, 'Operator', '2025-08-12', '2025-08-12', 1, 1),
(5, 'User Biasa', '2025-08-19', '2025-08-19', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `nama_status` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `nama_status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Tersedia', '2025-08-12', '2025-08-12', 0, 0),
(2, 'Sedang Dipakai', '2025-08-12', '2025-08-12', 0, 0),
(3, 'Inventaris ', '2025-08-12', '2025-08-12', 0, 0),
(5, 'Selesai', '2025-08-20', '2025-08-20', 1, 1),
(6, 'Request', '2025-08-21', '2025-08-21', 1, 1),
(7, 'Ditolak', '2025-08-25', '2025-08-25', 1, 1),
(8, 'Pending', '2025-08-26', '2025-08-26', 1, 1),
(9, 'Approved', '2025-08-26', '2025-08-26', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_divisi` int(11) NOT NULL,
  `id_roles` int(11) NOT NULL,
  `created_at` date NOT NULL,
  `updated_at` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `id_divisi`, `id_roles`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'Daniel Carqua', 'Daniel', '$2y$10$2z6ZBVpUgzspCWzDH.5PN.MJnqJUUfMkSNNexefHYrCXTaRS7LPvi', 4, 3, '2025-08-12', '2025-08-12', 0, 0),
(4, 'Daniel N', 'DanielB', '$2y$10$OiMlXz0EuHvu9FZzDZP/Pey6uPhiDwlEEg6uqtinLEBXBPse3StAy', 2, 5, '2025-08-22', '2025-08-22', 1, 1),
(5, 'Carqua', 'Carqua', '$2y$10$RHbShqI150dX.E/M835C7uOb9AujMHskQ.U5yfjDuRSSgNuekXt52', 1, 4, '2025-08-26', '2025-08-26', 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kendaraan_lokasi` (`id_lokasi`),
  ADD KEY `fk_kendaraan_status` (`id_status`);

--
-- Indexes for table `lokasi`
--
ALTER TABLE `lokasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemakaian`
--
ALTER TABLE `pemakaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pemakaian_user` (`id_user`),
  ADD KEY `fk_pemakaian_kendaraan` (`id_inventaris`),
  ADD KEY `fk_pemakaian_status` (`id_status`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_request_status` (`id_status`),
  ADD KEY `fk_request_kendaraan` (`id_kendaraan`),
  ADD KEY `fk_request_user` (`id_user`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_divisi` (`id_divisi`),
  ADD KEY `fk_user_roles` (`id_roles`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lokasi`
--
ALTER TABLE `lokasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pemakaian`
--
ALTER TABLE `pemakaian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `fk_kendaraan_lokasi` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kendaraan_status` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pemakaian`
--
ALTER TABLE `pemakaian`
  ADD CONSTRAINT `fk_pemakaian_kendaraan` FOREIGN KEY (`id_inventaris`) REFERENCES `kendaraan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pemakaian_status` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pemakaian_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `fk_request_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_status` FOREIGN KEY (`id_status`) REFERENCES `status` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_request_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_divisi` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_roles` FOREIGN KEY (`id_roles`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
