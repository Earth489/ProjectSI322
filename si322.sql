-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2025 at 12:36 PM
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
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `chat_id` int(11) NOT NULL,
  `matchs_id` int(11) DEFAULT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `sender_firstname` varchar(255) DEFAULT NULL,
  `sender_lastname` varchar(255) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`chat_id`, `matchs_id`, `sender_id`, `sender_firstname`, `sender_lastname`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 3, 14, NULL, NULL, 2, '123', '2025-03-24 01:09:07'),
(2, 3, 2, NULL, NULL, 14, 'fsefse', '2025-03-24 01:09:15'),
(3, 3, 2, NULL, NULL, 14, 'ดพกดพด', '2025-03-24 01:12:14'),
(4, 3, 14, NULL, NULL, 2, 'ดพกดกพ', '2025-03-24 01:12:30'),
(5, 3, 14, NULL, NULL, 2, 'fesfes', '2025-03-24 01:15:12'),
(6, 3, 2, 'อนิวัตติ์', 'ยานาบัว', 14, 'gdrgrd', '2025-03-24 01:55:38'),
(7, 3, 2, 'อนิวัตติ์', 'ยานาบัว', 14, 'กไฟ', '2025-03-24 01:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `matchs_id` int(11) DEFAULT NULL,
  `product_owner_id` int(11) DEFAULT NULL,
  `product_owner_product_id` int(11) DEFAULT NULL,
  `interested_user_id` int(11) DEFAULT NULL,
  `interested_user_product_id` int(11) DEFAULT NULL,
  `exchange_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interested`
--

CREATE TABLE `interested` (
  `interested_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `interested_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `selected_product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interested`
--

INSERT INTO `interested` (`interested_id`, `product_id`, `user_id`, `interested_date`, `status`, `selected_product_id`) VALUES
(9, 61, 14, '2025-03-16 12:39:24', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matchs`
--

CREATE TABLE `matchs` (
  `matchs_id` int(11) NOT NULL,
  `product_owner_id` int(11) DEFAULT NULL,
  `product_owner_product_id` int(11) DEFAULT NULL,
  `interested_user_id` int(11) DEFAULT NULL,
  `interested_user_product_id` int(11) DEFAULT NULL,
  `match_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matchs`
--

INSERT INTO `matchs` (`matchs_id`, `product_owner_id`, `product_owner_product_id`, `interested_user_id`, `interested_user_product_id`, `match_date`) VALUES
(3, 2, 61, 14, 59, '2025-03-18 06:42:43');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_Id` int(11) NOT NULL,
  `product_Name` varchar(255) NOT NULL,
  `product_detail` text NOT NULL,
  `Image` varchar(255) NOT NULL,
  `product_price` decimal(8,2) NOT NULL,
  `Product_exchanged` varchar(255) NOT NULL,
  `user_id` int(10) NOT NULL,
  `product_category` varchar(255) NOT NULL,
  `product_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_Id`, `product_Name`, `product_detail`, `Image`, `product_price`, `Product_exchanged`, `user_id`, `product_category`, `product_status`) VALUES
(48, 'เก้าอีทำงาน', '', 'chair.jpg', 1500.00, 'เก้าอี', 2, '', ''),
(49, 'เครื่องดูดฝุ่น', 'ระบบกรองฝุ่นแบบ:กรองฝุ่น 2 ชั้น แยกเป็นกรองหยาบ กับกรองละเอียด\r\n', 'vacuumcleaner.jpg', 3000.00, 'เครื่องใช้ไฟฟ้า', 2, '', ''),
(50, 'vans old skool', 'สีดำ ไซส์ 42 ', 'vansoldskool.jpg', 1700.00, 'รองเท้า', 2, '', ''),
(55, 'หม้อหุงข้าว', 'aaa', 'ricecooker.jpg', 1000.00, 'เครื่องใช้ไฟฟ้า', 15, '', ''),
(56, 'หม้อหุงข้าว', 'gerdgfd', 'ricecooker.jpg', 1000.00, '', 2, '', ''),
(57, 'fesfes', 'fesfes', 'microwave.jpg', 2323.00, '1', 2, '1', ''),
(58, 'frfr', 'frddr', 'airfrye.jpg', 10000.00, 'ของเล่นและเกม', 2, 'หนังสือและเครื่องเขียน', ''),
(59, 'dawsdaxassa', 'gfrdg', 'ricecooker.jpg', 1223.00, 'เครื่องใช้ในบ้าน', 14, 'เครื่องใช้ในบ้าน', ''),
(60, 'yjyju', 'ku8khk', 'microwave.jpg', 1233.00, 'เครื่องใช้ในบ้าน', 14, 'เครื่องใช้ในบ้าน', ''),
(61, 'grgr', 'grgdr', 'airfrye.jpg', 1234.00, 'เครื่องใช้ในบ้าน', 2, 'เครื่องใช้ในบ้าน', ''),
(62, 'frdfrdf', '12335pou', 'user (1).png', 1111.00, 'อุปกรณ์อิเล็กทรอนิกส์', 2, 'อุปกรณ์อิเล็กทรอนิกส์', ''),
(63, 'frfrdf', 'grdgrdg', 'user (2).png', 1111.00, 'หนังสือและเครื่องเขียน', 2, 'หนังสือและเครื่องเขียน', 'ต้องการแลก');

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
(2, 'earthaniwat@gmail.com', '12345678aa', 'อนิวัตติ์', 'ยานาบัว', 'ชาย', '0936048915', '2003-12-29', '418/1 จัสมินอพาร์เมนต์ ซ.ประชาสงเคราะห์ 29 แขวงดินแดง เขตดินแดง 10400', 'หอการค้า', 'user', 'user.png'),
(10, 'admin@gmail.com', 'admin123', 'tester', 'admin', 'อื่นๆ', '0936048915', '2025-01-01', '..', '..', 'admin', NULL),
(14, 'test@gmail.com', '12345678aa', 'test111', 'test123', 'ชาย', '08888888', '2025-03-01', 'desdes', 'dedes', 'user', NULL),
(15, 'chadarat@gmail.com', '12345678aa', 'chadar11', 'chadarat', 'หญิง', '08888888', '2025-03-01', 'aaaa', 'aaaa', 'user', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `matchs_id` (`matchs_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `matchs_id` (`matchs_id`),
  ADD KEY `product_owner_id` (`product_owner_id`),
  ADD KEY `interested_user_id` (`interested_user_id`),
  ADD KEY `product_owner_product_id` (`product_owner_product_id`),
  ADD KEY `interested_user_product_id` (`interested_user_product_id`);

--
-- Indexes for table `interested`
--
ALTER TABLE `interested`
  ADD PRIMARY KEY (`interested_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `selected_product_id` (`selected_product_id`);

--
-- Indexes for table `matchs`
--
ALTER TABLE `matchs`
  ADD PRIMARY KEY (`matchs_id`),
  ADD KEY `product_owner_id` (`product_owner_id`),
  ADD KEY `product_owner_product_id` (`product_owner_product_id`),
  ADD KEY `interested_user_id` (`interested_user_id`),
  ADD KEY `interested_user_product_id` (`interested_user_product_id`);

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
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interested`
--
ALTER TABLE `interested`
  MODIFY `interested_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `matchs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`matchs_id`) REFERENCES `matchs` (`matchs_id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `chats_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`matchs_id`) REFERENCES `matchs` (`matchs_id`),
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`product_owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `history_ibfk_3` FOREIGN KEY (`interested_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `history_ibfk_4` FOREIGN KEY (`product_owner_product_id`) REFERENCES `product` (`product_Id`),
  ADD CONSTRAINT `history_ibfk_5` FOREIGN KEY (`interested_user_product_id`) REFERENCES `product` (`product_Id`);

--
-- Constraints for table `interested`
--
ALTER TABLE `interested`
  ADD CONSTRAINT `interested_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_Id`),
  ADD CONSTRAINT `interested_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `interested_ibfk_3` FOREIGN KEY (`selected_product_id`) REFERENCES `product` (`product_Id`);

--
-- Constraints for table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `matchs_ibfk_1` FOREIGN KEY (`product_owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `matchs_ibfk_2` FOREIGN KEY (`product_owner_product_id`) REFERENCES `product` (`product_Id`),
  ADD CONSTRAINT `matchs_ibfk_3` FOREIGN KEY (`interested_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `matchs_ibfk_4` FOREIGN KEY (`interested_user_product_id`) REFERENCES `product` (`product_Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
