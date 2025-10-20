-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2025 at 08:47 AM
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
-- Database: `face`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `user_id`, `login_time`, `logout_time`) VALUES
(2, 5, '2025-10-12 17:52:36', '2025-10-12 20:21:12'),
(3, 6, '2025-10-12 20:16:44', '2025-10-12 20:21:31'),
(4, 6, '2025-10-12 21:19:34', '2025-10-12 21:49:50'),
(5, 5, '2025-10-12 21:19:40', '2025-10-12 21:21:12'),
(6, 5, '2025-10-12 21:21:27', '2025-10-12 21:50:08'),
(7, 4, '2025-10-12 21:36:49', NULL),
(8, 6, '2025-10-12 21:50:01', '2025-10-12 21:51:06'),
(9, 5, '2025-10-12 21:50:29', '2025-10-12 21:50:50'),
(10, 6, '2025-10-13 02:47:33', '2025-10-13 06:26:57'),
(11, 5, '2025-10-13 02:47:42', '2025-10-13 02:48:07'),
(12, 5, '2025-10-13 06:26:36', '2025-10-13 06:31:41'),
(13, 6, '2025-10-13 06:31:46', '2025-10-13 06:33:35'),
(14, 7, '2025-10-13 06:43:42', '2025-10-13 06:44:25'),
(15, 7, '2025-10-13 06:48:03', '2025-10-13 06:49:22'),
(16, 7, '2025-10-13 20:27:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_users`
--

CREATE TABLE `attendance_users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `face_token` varchar(255) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fingerprints`
--

CREATE TABLE `fingerprints` (
  `id` int(11) NOT NULL,
  `finger_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fingerprints`
--

INSERT INTO `fingerprints` (`id`, `finger_id`, `user_id`, `name`, `created_at`) VALUES
(1, 1, NULL, NULL, '2025-10-12 09:03:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `face_image` varchar(255) NOT NULL,
  `face_token` varchar(255) DEFAULT NULL,
  `faceset_id` varchar(100) DEFAULT NULL,
  `finger_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `contact_number`, `course`, `face_image`, `face_token`, `faceset_id`, `finger_id`, `created_at`) VALUES
(4, 'Msitery', 'SAMPLE', '091231231231231231', 'BSIT', 'faces/1759126104_photo_2025-04-24_14-25-05-e1745480471238.jpg', '80a93e04e6f2d337d044507b0ed3a3ab', NULL, 5, '2025-09-29 06:08:28'),
(5, 'james ', 'zone3b', '09959157959', 'bscs', 'faces/1759137061_ffaa.JPG', '06fcbb9c2efb27cac37dc5969aba71d3', NULL, 1, '2025-09-29 09:11:09'),
(6, 'nerio pascual', 'gensan', '12312', 'bsie', 'faces/1759284930_doc2.jpg', '6fdbec95c556b82c3279fc2d3a4155ab', NULL, 2, '2025-10-01 02:15:38'),
(7, 'test', 'z2', '143', 'cs', 'faces/1760308987_557738182_10235982553642784_6785983538436360637_n.jpg', '042f9ef608d6a4af7a12d2d049af0a9f', NULL, 3, '2025-10-12 22:43:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `attendance_users`
--
ALTER TABLE `attendance_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `face_token` (`face_token`);

--
-- Indexes for table `fingerprints`
--
ALTER TABLE `fingerprints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `finger_id` (`finger_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `attendance_users`
--
ALTER TABLE `attendance_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fingerprints`
--
ALTER TABLE `fingerprints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
