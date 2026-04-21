-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 02:18 PM
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
-- Table structure for table `tb_customer`
--

CREATE TABLE `tb_customer` (
  `id` int(11) NOT NULL,
  `id_customer` varchar(10) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `nama_toko` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `provinsi` varchar(50) NOT NULL,
  `negara` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `no_ktp` varchar(30) NOT NULL,
  `detail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_customer`
--

INSERT INTO `tb_customer` (`id`, `id_customer`, `nama_customer`, `nama_toko`, `alamat`, `provinsi`, `negara`, `phone`, `no_ktp`, `detail`) VALUES
(1, 'K001', 'SUSILO HARYAWAN', 'MISTER SOSIS', 'BLORA', 'JAWA TENGAH', 'INDONESIA', '', '', ''),
(2, 'AB001', 'RADEN YUDHISTIRA', 'MEATSHOP NATA', 'WATES', 'JAWA TENGAH', 'INDONESIA', '', '', ''),
(3, 'AA001', 'BAMBANG EKO RATIYATNO', 'KK FF', 'WONOSOBO', 'JAWA TENGAH', 'INDONESIA', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_products`
--

CREATE TABLE `tb_products` (
  `id_Unique` int(11) NOT NULL,
  `id_parent` varchar(11) NOT NULL,
  `name_product` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `qty_cardboard` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_products`
--

INSERT INTO `tb_products` (`id_Unique`, `id_parent`, `name_product`, `price`, `stock`, `qty_cardboard`) VALUES
(1, 'BA-250', 'BAKSO AYAM 250 G', 19466, 0, 30),
(2, 'BA-1000', 'BAKSO AYAM 1000 G', 69002, 0, 7),
(3, 'BS-250', 'BAKSO SAPI 250 G', 20273, 0, 30),
(4, 'BS-1000', 'BAKSO SAPI 1000 G', 77308, 0, 7),
(5, 'KA-1000', 'KORNET AYAM 1000 G', 46488, 0, 10),
(6, 'KA-250', 'KORNET AYAM 250 G', 12825, 5, 30),
(7, 'KAL-400', 'KORNET AYAM LOYANG 400 G', 19363, 1080, 20),
(8, 'RA-1000', 'ROLLADE AYAM 1000 G', 66098, 290, 10),
(9, 'RA-250', 'ROLLADE AYAM 250 G', 18068, 0, 30),
(10, 'RAR-400', 'ROLLADE AYAM ROLL 400 G', 24797, 201, 50);

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
(1, 'JOKO SETIAWAN', '08151236665', 'LAWEYAN', 'DRIVER', '0564656554666'),
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
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `username`, `password`, `email`, `role`) VALUES
(1, 'Lucifer', '$2y$10$etWMD2d9vYFMcc8qgZNu2.Pc/iKmDJBnFXqdwIRVmKct3uEbZJxay', 'clashermedusa@gmail.com', 'user'),
(2, 'admin', '$2y$10$dsNyXQErKaYb1lqqsMOiD.iRGHAf8i/fQojzc7NSvxZcGDoiTb8qC', 'admin@gmail.com', 'admin');

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
  `shipper` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `no_invoice`, `customer_id`, `tanggal`, `tanggal_kirim`, `shipper`) VALUES
(16, 1, 1, '2026-04-20', '2026-04-20', 'JOKO SETIAWAN'),
(17, 2, 2, '2026-04-20', '0000-00-00', 'JOKO SETIAWAN');

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
(17, 17, 'ROLLADE AYAM 1000 G', 66098, 30, '0', 0);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `tb_customer`
--
ALTER TABLE `tb_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_products`
--
ALTER TABLE `tb_products`
  MODIFY `id_Unique` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tb_shipper`
--
ALTER TABLE `tb_shipper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaction_detail`
--
ALTER TABLE `transaction_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
