-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2026 at 09:50 AM
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
-- Database: `dbfrozen_food`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_cart`
--

CREATE TABLE `tb_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_cart`
--

INSERT INTO `tb_cart` (`id`, `user_id`, `product_id`, `qty`) VALUES
(13, 4, 29, 8),
(14, 1, 31, 9);

-- --------------------------------------------------------

--
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id` int(11) NOT NULL,
  `id_customer` varchar(15) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `nama_toko` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-Laki','Perempuan') DEFAULT NULL,
  `alamat` text NOT NULL,
  `provinsi` varchar(50) NOT NULL,
  `negara` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `no_ktp` varchar(30) NOT NULL,
  `detail` text NOT NULL,
  `maps_link` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id`, `id_customer`, `nama_customer`, `nama_toko`, `email`, `jenis_kelamin`, `alamat`, `provinsi`, `negara`, `phone`, `no_ktp`, `detail`, `maps_link`) VALUES
(1, 'K001', 'SUSILO HARYAWAN', 'MISTER SOSIS', '', '', 'BLORA', 'JAWA TENGAH', 'INDONESIA', '', '', '', NULL),
(2, 'AB001', 'RADEN YUDHISTIRA', 'MEATSHOP NATA', '', '', 'WATES', 'JAWA TENGAH', 'INDONESIA', '', '', '', NULL),
(3, 'AA001', 'BAMBANG EKO RATIYATNO', 'KK FF', '', '', 'WONOSOBO', 'JAWA TENGAH', 'INDONESIA', '', '', '', NULL),
(4, 'CUST-17768', 'HINDARWAN', 'HANA MAKMUR', 'hanamakmur@gmail.com', 'Laki-Laki', 'PURBALINGGA', 'JAWA TENGAH', 'INDONESIA', '', '', '', NULL),
(5, 'CUST-1776838375', 'Lucian', 'LUCIFER FF', 'Lucifer@gmail.com', 'Laki-Laki', 'SURAKARTA', 'JAWA TENGAH ', 'INDONESIA', '081328580511', '', 'Depan Joglo Mangkubumen Surakarta', 'https://maps.app.goo.gl/6isGfRRm3CPKajKz9');

-- --------------------------------------------------------

--
-- Table structure for table `tb_products`
--

CREATE TABLE `tb_products` (
  `id_Unique` int(11) NOT NULL,
  `id_parent` varchar(11) NOT NULL,
  `name_product` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `qty_cardboard` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_products`
--

INSERT INTO `tb_products` (`id_Unique`, `id_parent`, `name_product`, `image`, `price`, `stock`, `qty_cardboard`) VALUES
(1, 'BA-250', 'BAKSO AYAM 250 G', NULL, 19466, 0, 30),
(2, 'BA-1000', 'BAKSO AYAM 1000 G', NULL, 69002, 0, 7),
(3, 'BS-250', 'BAKSO SAPI 250 G', NULL, 20273, 0, 30),
(4, 'BS-1000', 'BAKSO SAPI 1000 G', NULL, 77308, 0, 7),
(5, 'KA-1000', 'KORNET AYAM 1000 G', NULL, 46488, 0, 10),
(6, 'KA-250', 'KORNET AYAM 250 G', NULL, 12825, 0, 30),
(7, 'KAL-400', 'KORNET AYAM LOYANG 400 G', NULL, 19363, 780, 20),
(8, 'RA-1000', 'ROLLADE AYAM 1000 G', NULL, 66098, 290, 10),
(9, 'RA-250', 'ROLLADE AYAM 250 G', NULL, 18068, 0, 30),
(10, 'RAR-400', 'ROLLADE AYAM ROLL 400 G', NULL, 24797, 201, 50),
(11, 'RS-1000', 'ROLLADE SAPI 1000 G', NULL, 68006, 0, 10),
(12, 'RS-250', 'ROLLADE SAPI 250 G', NULL, 18915, 0, 30),
(13, 'RS-215', 'ROLLADE SAPI 215 G', NULL, 16376, 0, 30),
(14, 'RSR-400', 'ROLLADE SAPI ROLL 400 G', NULL, 26194, 923, 50),
(15, 'SA-250', 'SOSIS AYAM 250 G', NULL, 20606, 0, 30),
(16, 'SS-250', 'SOSIS SAPI 250 G', NULL, 25680, 0, 30),
(17, 'SC-250', 'SOSIS COCKTAIL 250 G', NULL, 25680, 60, 30),
(18, 'BSO-500', 'BRATWURST SAPI ORI 500 G', NULL, 45838, 0, 15),
(19, 'BSM-500', 'BRATWURST SAPI MINI 500 G', NULL, 45838, 0, 15),
(20, 'BAO-500', 'BRATWURST AYAM ORI 500 G', NULL, 44569, 0, 15),
(21, 'BAM-500', 'BRATWURST AYAM MINI 500 G', NULL, 44569, 0, 15),
(22, 'BRS-13', 'BRS COKLAT 13S 500 G', NULL, 30688, 789, 15),
(23, 'BRS-24', 'BRS COKLAT 24S 500 G', NULL, 30688, 1440, 15),
(24, 'BRS-7', 'BRS COKLAT 7S 500 G', NULL, 30688, 1105, 15),
(25, 'BRSM-500', 'BRS MERAH 24 500G', NULL, 36691, 1005, 15),
(26, 'BRSM-500', 'BRS MERAH 24S 500G', NULL, 34549, 1305, 15),
(27, 'KALU-400', 'KORNET AYAM LUNCHEON 400 G', NULL, 26893, 0, 10),
(28, 'KAL-400', 'KORNET AYAM LOYANG 2 400G', NULL, 14894, 1000, 20),
(29, 'BRS-13', 'BRS COKLAT 13S 2 500G', NULL, 27962, 800, 15),
(30, 'BRS-7', 'BRS COKLAT 7S 2 500G', NULL, 27962, 150, 15),
(31, 'BRS-24', 'BRS COKLAT 24S 2 500G', NULL, 28234, 449, 15),
(32, 'RSR-400', 'ROLLADE SAPI ROLL 2 400G', NULL, 20753, 173, 50),
(33, 'RAR-400', 'ROLLADE AYAM ROLL 2 400G', NULL, 19074, 224, 50),
(34, 'RS-1000', 'ROLLADE SAPI 1000 G ( MBG )', NULL, 46000, 21, 10),
(35, 'RA-1000', 'ROLLADE AYAM 1000 G ( MBG )', NULL, 45000, 1290, 10),
(36, 'KA-1000', 'KORNET AYAM 1000 G ( MBG )', NULL, 32000, 0, 10),
(37, 'RS-361000', 'ROLLADE SAPI STIKER 1 : 36 1000 G ( MBG )', '', 46000, 250, 10);

-- --------------------------------------------------------

--
-- Table structure for table `tb_shipper`
--

CREATE TABLE `tb_shipper` (
  `id` int(11) NOT NULL,
  `nama_shipper` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `no_ktp` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_shipper`
--

INSERT INTO `tb_shipper` (`id`, `nama_shipper`, `no_hp`, `alamat`, `keterangan`, `no_ktp`) VALUES
(1, 'PAK BC', '08123456789', 'LAWEYAN', 'KEPALA CABANG,DRIVER', '0564656554666'),
(2, 'RATNA', '0554556256848', 'LAWEYAN', 'DRIVER POCOKAN', '0021545665265');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `username`, `password`, `email`, `role`, `reset_token`, `reset_expires`) VALUES
(1, 'Lucifer', '$2y$10$etWMD2d9vYFMcc8qgZNu2.Pc/iKmDJBnFXqdwIRVmKct3uEbZJxay', 'lucifer@gmail.com', 'user', NULL, NULL),
(2, 'admin', '$2y$10$dsNyXQErKaYb1lqqsMOiD.iRGHAf8i/fQojzc7NSvxZcGDoiTb8qC', 'admin@gmail.com', 'admin', NULL, NULL),
(4, 'hanamakmur', '$2y$10$fw8agotONFYzbT.dmscCX./9CYCNvnxPaA3pk75b9q5KxiHNKO9ZO', 'hanamakmur@gmail.com', 'user', NULL, NULL),
(5, 'Lucifer', '$2y$10$7Y9KuxX17/AtT1ZOkGY9YOJTj6MsOAyxNzhGqRGdjHWcRQRpuCOOm', 'Lucifer@gmail.com', 'user', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `no_invoice` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `shipper` varchar(100) NOT NULL,
  `status` enum('Undelivered','Delivered') NOT NULL DEFAULT 'Undelivered',
  `payment_status` enum('Unpaid','Paid') DEFAULT 'Unpaid',
  `tanggal_paid` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `no_invoice`, `customer_id`, `tanggal`, `tanggal_kirim`, `shipper`, `status`, `payment_status`, `tanggal_paid`) VALUES
(16, 1, 1, '2026-04-20', '2026-04-20', 'JOKO SETIAWAN', '', 'Unpaid', NULL),
(17, 2, 2, '2026-04-20', '2026-04-25', 'JOKO SETIAWAN', 'Undelivered', 'Unpaid', NULL),
(18, 3, 4, '2026-04-22', '2026-04-22', 'JOKO SETIAWAN', 'Undelivered', 'Unpaid', NULL),
(19, 4, 4, '2026-04-22', '2026-04-25', 'JOKO SETIAWAN', 'Undelivered', 'Unpaid', NULL),
(20, 5, 4, '2026-04-22', '2026-04-30', 'JOKO SETIAWAN', 'Delivered', 'Paid', '2026-05-20'),
(21, 6, 4, '2026-04-22', '2026-05-01', 'JOKO SETIAWAN', 'Undelivered', 'Unpaid', NULL),
(22, 7, 5, '2026-04-22', '2026-05-22', 'PAK BC', 'Delivered', 'Unpaid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_detail`
--

CREATE TABLE `transaction_detail` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `name_product` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `persen_diskon` varchar(11) NOT NULL,
  `diskon_rp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_detail`
--

INSERT INTO `transaction_detail` (`id`, `transaction_id`, `name_product`, `harga`, `qty`, `persen_diskon`, `diskon_rp`) VALUES
(14, 16, 'KORNET AYAM LOYANG 400 G', 19363, 10, '17.5', 33885),
(15, 16, 'ROLLADE AYAM 1000 G', 66098, 10, '10', 66098),
(16, 17, 'ROLLADE AYAM ROLL 400 G', 24797, 23, '17.5', 99808),
(17, 17, 'ROLLADE AYAM 1000 G', 66098, 30, '0', 0),
(18, 18, 'BRS COKLAT 7S 500 G', 30688, 45, '5', 69048),
(19, 18, 'BRS MERAH 24 500G', 36691, 300, '5', 550365),
(20, 18, 'KORNET AYAM LOYANG 400 G', 19363, 300, '17.5', 1016558),
(21, 18, 'ROLLADE AYAM ROLL 400 G', 24797, 2500, '17.5', 10848688),
(22, 18, 'ROLLADE SAPI ROLL 400 G', 26194, 1750, '17.5', 8021913),
(23, 19, 'BRS COKLAT 13S 2 500G', 27962, 2, '10', 5592),
(24, 19, 'BRS COKLAT 13S 500 G', 30688, 1, '5', 1534),
(25, 19, 'KORNET AYAM LOYANG 2 400G', 14894, 100, '12.5', 186175),
(26, 20, 'BRS COKLAT 24S 2 500G', 28234, 1, '20', 5647),
(27, 20, 'BRS COKLAT 24S 500 G', 30688, 10, '10', 30688),
(28, 21, 'BRS COKLAT 13S 2 500G', 27962, 10, '0', 0),
(29, 21, 'BRS COKLAT 13S 500 G', 30688, 20, '0', 0),
(30, 21, 'KORNET AYAM 1000 G ( MBG )', 32000, 8, '0', 0),
(31, 21, 'KORNET AYAM 250 G', 12825, 5, '0', 0),
(32, 21, 'KORNET AYAM LOYANG 2 400G', 14894, 40, '0', 0),
(33, 22, 'BRS COKLAT 13S 2 500G', 27962, 8, '17.5', 39147),
(34, 22, 'BRS COKLAT 13S 500 G', 30688, 10, '0', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_cart`
--
ALTER TABLE `tb_cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_customer`
--
ALTER TABLE `tb_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_products`
--
ALTER TABLE `tb_products`
  ADD PRIMARY KEY (`id_Unique`);

--
-- Indexes for table `tb_shipper`
--
ALTER TABLE `tb_shipper`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_cart`
--
ALTER TABLE `tb_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `id_Unique` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tb_shipper`
--
ALTER TABLE `tb_shipper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
