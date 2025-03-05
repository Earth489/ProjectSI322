-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 01:56 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `si322`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_Id` int(11) NOT NULL,
  `product_Name` varchar(255) NOT NULL,
  `product_detail` text DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `product_price` decimal(8,2) NOT NULL,
  `Product_exchanged` text DEFAULT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_Id`, `product_Name`, `product_detail`, `Image`, `product_price`, `Product_exchanged`, `user_id`) VALUES
(48, 'เก้าอีทำงาน', '', 'chair.jpg', 1500.00, 'เก้าอี', 2),
(49, 'เครื่องดูดฝุ่น', 'ระบบดูด:แห้ง', 'vacuumcleaner.jpg', 3000.00, 'เครื่องใช้ไฟฟ้า', 2),
(50, 'vans old skool', 'สีดำ ไซส์ 42 ', 'vansoldskool.jpg', 1700.00, 'รองเท้า', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(15) NOT NULL,
  `tel` varchar(10) NOT NULL,
  `birth_date` date NOT NULL,
  `address` text NOT NULL,
  `community` varchar(255) NOT NULL,
  `user_type` varchar(15) NOT NULL DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `firstname`, `lastname`, `gender`, `tel`, `birth_date`, `address`, `community`, `user_type`, `profile_image`) VALUES
(2, 'earthaniwat@gmail.com', '12345678aa', 'อนิวัตติ์ ', 'ยานาบัว', 'ชาย', '0936048915', '2003-12-29', '418/1 จัสมินอพาร์เมนต์ ซ.ประชาสงเคราะห์ 29 แขวงดินแดง เขตดินแดง 10400', 'หอการค้า', 'user', 'user.png'),
(10, 'admin@gmail.com', 'admin123', 'tester', 'admin', 'อื่นๆ', '0936048915', '2025-01-01', '..', '..', 'admin', NULL),
(11, 'earthaniwat256@gmail.com', '12345678aa', 'Aniwat', 'Aniwat', 'ชาย', '0936048915', '2003-12-29', '418/1 จัสมินอพาร์เมนต์\r\nซ.ประชาสงเคราะห์ 29\r\nแขวงดินแดง เขตดินแดง 10400', 'หอการค้า', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
