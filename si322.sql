-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 10:15 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `history_id` int(11) NOT NULL,
  `matchs_id` int(11) DEFAULT NULL,
  `product_owner_id` int(11) DEFAULT NULL,
  `interested_user_id` int(11) DEFAULT NULL,
  `product_owner_product_id` int(11) DEFAULT NULL,
  `interested_user_product_id` int(11) DEFAULT NULL,
  `exchange_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`history_id`, `matchs_id`, `product_owner_id`, `interested_user_id`, `product_owner_product_id`, `interested_user_product_id`, `exchange_date`) VALUES
(29, 51, 18, 14, 90, 87, '2025-04-14 08:57:54'),
(31, 54, 18, 2, 89, 91, '2025-04-14 09:39:36');

-- --------------------------------------------------------

--
-- Table structure for table `interested`
--

CREATE TABLE `interested` (
  `interested_id` int(111) NOT NULL,
  `product_id` int(111) DEFAULT NULL,
  `user_id` int(111) DEFAULT NULL,
  `interested_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `selected_product_id` int(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interested`
--

INSERT INTO `interested` (`interested_id`, `product_id`, `user_id`, `interested_date`, `status`, `selected_product_id`) VALUES
(74, 89, 14, '2025-04-14 09:33:13', '', NULL),
(75, 89, 2, '2025-04-14 09:33:55', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `matchs`
--

CREATE TABLE `matchs` (
  `matchs_id` int(111) NOT NULL,
  `interested_id` int(111) DEFAULT NULL,
  `product_owner_id` int(111) DEFAULT NULL,
  `product_owner_product_id` int(111) DEFAULT NULL,
  `interested_user_id` int(111) DEFAULT NULL,
  `interested_user_product_id` int(111) DEFAULT NULL,
  `match_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active',
  `owner_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `interested_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `product_owner_confirm` tinyint(4) DEFAULT 0,
  `interested_user_confirm` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matchs`
--

INSERT INTO `matchs` (`matchs_id`, `interested_id`, `product_owner_id`, `product_owner_product_id`, `interested_user_id`, `interested_user_product_id`, `match_date`, `status`, `owner_confirmed`, `interested_confirmed`, `product_owner_confirm`, `interested_user_confirm`) VALUES
(51, 70, 18, 90, 14, 87, '2025-04-14 08:57:35', 'completed', 1, 1, 0, 0),
(54, 74, 18, 89, 2, 91, '2025-04-14 09:36:01', 'completed', 1, 1, 0, 0);

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
(86, 'ไมโครเวฟ', 'ไมโครเวฟ', 'microwave.jpg', 3000.00, 'เครื่องใช้ในบ้าน', 2, 'เครื่องใช้ในบ้าน', 'ต้องการแลก'),
(87, 'หม้อหุ้งข้าว', 'หม้อหุ้งข้าว', 'ricecooker.jpg', 3000.00, 'เครื่องใช้ในบ้าน', 14, 'เครื่องใช้ในบ้าน', 'ต้องการแลก'),
(88, 'ดหดหด', 'ดหำดหำด', 'kallus.jpg', 2345.00, 'อุปกรณ์อิเล็กทรอนิกส์', 2, 'อุปกรณ์อิเล็กทรอนิกส์', 'ต้องการแลก'),
(89, 'grgrd', 'gdrgdrg', 'tag.png', 2345.00, 'อุปกรณ์อิเล็กทรอนิกส์', 18, 'อุปกรณ์อิเล็กทรอนิกส์', 'ต้องการแลก'),
(90, 'กไฟกฟไ', 'ดำดำหด', 'xamppp.png', 2322.00, 'เครื่องใช้ในบ้าน', 18, 'เครื่องใช้ในบ้าน', 'ต้องการแลก'),
(91, 'lkjhgf', 'jhgf', 'vacuumcleaner.jpg', 3455.00, 'เครื่องใช้ในบ้าน', 14, 'เครื่องใช้ในบ้าน', 'ต้องการแลก'),
(93, '3434567', 'wertyui', 'chair.jpg', 9876.00, 'เครื่องใช้ในบ้าน', 14, 'เครื่องใช้ในบ้าน', 'ต้องการแลก');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewed_user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `history_id`, `reviewer_id`, `reviewed_user_id`, `comment`, `review_date`) VALUES
(3, 29, 14, 18, 'fefsefes5555', '2025-04-15 04:08:28'),
(4, 29, 18, 14, 'dadafesfesfse', '2025-04-15 04:08:42');

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
(14, 'test@gmail.com', '12345678aa', 'test111', 'test123', 'ชาย', '08888้4586', '2025-03-01', 'desdes', 'dedes', 'user', NULL),
(16, 'test11@gmail.com', '12345678aa', 'test112', 'test113', 'ชาย', '08888888', '2025-03-14', 'aaaaaa', 'bbbbbb', 'user', NULL),
(18, 'earth@gmail.com', '11111111aa', 'earth', 'aaaaaa', 'ชาย', '0895487578', '2025-04-09', 'หอพัก', '', 'user', NULL);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_review` (`history_id`,`reviewer_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_user_id` (`reviewed_user_id`);

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
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `interested`
--
ALTER TABLE `interested`
  MODIFY `interested_id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `matchs`
--
ALTER TABLE `matchs`
  MODIFY `matchs_id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `history` (`history_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`reviewed_user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
