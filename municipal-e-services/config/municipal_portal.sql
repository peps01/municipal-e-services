-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 02:19 PM
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
-- Database: `municipal_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `app_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `current_status` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `extra_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`app_id`, `user_id`, `type`, `purpose`, `current_status`, `remarks`, `submitted_at`, `updated_at`, `extra_data`) VALUES
(1, 17, 'Business Permit / Mayor’s Permit', 'qweqwe', 'Filed', '', '2025-07-14 19:30:09', '2025-07-14 19:30:09', '{\"business_name\":\"Snack\",\"business_address\":\"Capoocan,Leyte\"}'),
(2, 17, 'Business Permit / Mayor’s Permit', 'none', 'Filed', '', '2025-07-14 20:06:10', '2025-07-14 20:06:10', '{\"business_name\":\"Snack\",\"business_address\":\"Capoocan,Leyte\",\"nature_of_business\":\"Food\",\"number_of_employees\":\"25\"}'),
(3, 17, 'Residency Certificate', 'none', 'Filed', '', '2025-07-14 20:07:04', '2025-07-14 20:07:04', '{\"address\":\"Capoocan,Leyte\",\"years_of_residency\":\"22\"}'),
(4, 17, 'Permit to Hold an Event / Parade / Rally', 'sdas', 'Filed', '', '2025-07-14 20:26:16', '2025-07-14 20:26:16', '{\"event_name\":\"asd\",\"event_date\":\"2025-07-25\",\"venue\":\"asdasd\",\"organizer_name\":\"asd\",\"contact_number\":\"asd\"}'),
(5, 17, 'Barangay Clearance', 'asd', 'Filed', '', '2025-07-14 20:28:11', '2025-07-14 20:28:11', '{\"address\":\"asas\"}'),
(6, 17, 'Barangay Clearance', 'asd', 'Filed', '', '2025-07-14 20:33:05', '2025-07-14 20:33:05', '{\"address\":\"sad\"}'),
(7, 17, 'Barangay Clearance', 'asd', 'Filed', '', '2025-07-14 20:34:17', '2025-07-14 20:34:17', '{\"address\":\"ad\"}'),
(8, 17, 'Residency Certificate', 'sdf', 'Filed', '', '2025-07-14 20:44:52', '2025-07-14 20:44:52', '{\"address\":\"sdsfd\",\"years_of_residency\":\"33\"}'),
(9, 17, 'Building Permit', 'nothing', 'Filed', '', '2025-07-15 19:56:57', '2025-07-15 19:56:57', '{\"project_name\":\"J&F\",\"construction_type\":\"Mall\",\"project_location\":\"Tacloban,Leyte\",\"estimated_cost\":\"20000000\",\"contractor_name\":\"John Loyd\"}'),
(10, 17, 'Barangay Clearance', 'asda', 'Filed', '', '2025-07-15 23:00:25', '2025-07-15 23:00:25', '{\"address\":\"adsa\"}'),
(11, 17, 'Residency Certificate', 'asd', 'Filed', '', '2025-07-15 23:00:59', '2025-07-15 23:00:59', '{\"address\":\"ad\",\"years_of_residency\":\"22\"}'),
(12, 17, 'Indigency Certificate', 'asd', 'Filed', '', '2025-07-15 23:01:53', '2025-07-15 23:01:53', '{\"address\":\"ad\"}'),
(13, 17, 'Business Permit / Mayor’s Permit', 'asd', 'Filed', '', '2025-07-16 17:47:26', '2025-07-16 17:47:26', '{\"business_name\":\"asd\",\"business_address\":\"asd\",\"nature_of_business\":\"asd\",\"number_of_employees\":\"23\"}'),
(14, 17, 'Residency Certificate', 'kjlk', 'Filed', '', '2025-07-16 18:15:06', '2025-07-16 18:15:06', '{\"address\":\"lk\",\"years_of_residency\":\"9\"}'),
(15, 17, 'Indigency Certificate', '232', 'Filed', '', '2025-07-16 18:15:35', '2025-07-16 18:15:35', '{\"address\":\"lm\"}'),
(16, 18, 'Indigency Certificate', 'Indigency Certificate', 'Filed', '', '2025-07-16 18:51:46', '2025-07-16 18:51:46', '{\"address\":\"Capoocan,Leyte\"}');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`doc_id`, `app_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 1, 'EganoMarkJohnWAD1A.jpg', '../uploads/1752492609_EganoMarkJohnWAD1A.jpg', 'image/jpeg', '2025-07-14 19:30:09'),
(2, 2, 'EganoMarkJohnWAD1A.jpg', '../uploads/1752494770_EganoMarkJohnWAD1A.jpg', 'image/jpeg', '2025-07-14 20:06:10'),
(3, 3, '5094bfb24c3fb5bf72ccf5e4c4b8bb7e.jpg', '../uploads/1752494824_5094bfb24c3fb5bf72ccf5e4c4b8bb7e.jpg', 'image/jpeg', '2025-07-14 20:07:04'),
(4, 4, '9e7c9a2520941e9933821b091b4a6584.jpg', '../uploads/1752495976_9e7c9a2520941e9933821b091b4a6584.jpg', 'image/jpeg', '2025-07-14 20:26:16'),
(5, 5, 'controller.PNG', '../uploads/1752496091_controller.PNG', 'image/png', '2025-07-14 20:28:11'),
(6, 6, '1752496385_0_example mvvc.PNG', '../uploads/1752496385_0_example mvvc.PNG', 'image/png', '2025-07-14 20:33:05'),
(7, 7, '1752496457_0_9e7c9a2520941e9933821b091b4a6584.jpg', '../uploads/1752496457_0_9e7c9a2520941e9933821b091b4a6584.jpg', 'image/jpeg', '2025-07-14 20:34:17'),
(8, 8, '1.PNG', '../uploads/1752497092_1.PNG', 'image/png', '2025-07-14 20:44:52'),
(9, 8, 'LIANZA PS copy.jpg', '../uploads/1752497092_LIANZA PS copy.jpg', 'image/jpeg', '2025-07-14 20:44:52'),
(10, 9, '1.PNG', '../uploads/1752580617_1.PNG', 'image/png', '2025-07-15 19:56:57'),
(11, 9, 'EganoMarkJohnWAD1A.jpg', '../uploads/1752580617_EganoMarkJohnWAD1A.jpg', 'image/jpeg', '2025-07-15 19:56:57'),
(12, 10, '1.docx', '../uploads/1752591625_1.docx', 'application/vnd.openxmlformats-officedocument.word', '2025-07-15 23:00:25'),
(13, 11, '4d6d31e0dcc3a1bac983536f6c159221.jpg', '../uploads/1752591659_4d6d31e0dcc3a1bac983536f6c159221.jpg', 'image/jpeg', '2025-07-15 23:00:59'),
(14, 12, '1.PNG', '../uploads/1752591713_1.PNG', 'image/png', '2025-07-15 23:01:53'),
(15, 13, 'benefits of mvc.PNG', '../uploads/1752659246_benefits of mvc.PNG', 'image/png', '2025-07-16 17:47:26'),
(16, 15, '2.PNG', '../uploads/1752660935_2.PNG', 'image/png', '2025-07-16 18:15:36'),
(17, 16, '9e7c9a2520941e9933821b091b4a6584.jpg', '../uploads/1752663106_9e7c9a2520941e9933821b091b4a6584.jpg', 'image/jpeg', '2025-07-16 18:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `performed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `seen` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `user_id`, `message`, `seen`, `created_at`) VALUES
(12, 17, 'werwerwer', 1, '2025-07-15 22:07:21'),
(13, 17, 'yuuygujh', 1, '2025-07-15 22:08:26'),
(14, 17, 'asdasdas', 1, '2025-07-15 22:14:23'),
(15, 17, 'mlm,', 1, '2025-07-15 22:29:46'),
(16, 17, 'dfsfd', 1, '2025-07-15 22:40:23'),
(17, 17, 'dfsdf', 1, '2025-07-15 22:48:57'),
(18, 17, 'lml,ml', 1, '2025-07-15 22:56:46'),
(19, 17, 'hi', 1, '2025-07-16 18:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qr_codes`
--

CREATE TABLE `qr_codes` (
  `qr_id` int(11) NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `qr_value` text DEFAULT NULL,
  `generated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Staff'),
(3, 'Resident');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `status_id` int(11) NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `phone`, `password`, `role_id`, `created_at`) VALUES
(1, 'Mark John Egano', 'pepepmjegano@gmail.com', '09123456789', '$2y$10$xThxJ2x0cW6h2B5e4zSEt.ZN1zqgIgzMYS5..RJ35Rx1puHcTRgCu', 1, '2025-07-13 20:56:35'),
(17, 'Jerome Egano', 'jerome@gmail.com', '0987654321', '$2y$10$OYW1mlSyovu6NS3BJuvibOVuiKlEaLNMLLj03cEQP1aw3wJgcUsdu', 3, '2025-07-13 22:12:04'),
(18, 'John Cena', 'pepepmjegano1@gmail.com', '096173742372', '$2y$10$V1c94eczJIFGb2cfkjAMg.qHWhdqtu6pXBhZz36IucsShYvW8C16C', 3, '2025-07-16 17:08:32'),
(19, 'Jenny Dela Cruz', 'jenny@gmail.com', '0987654321', '$2y$10$NSAyyEfzdXRrmGu5JEwAjOYYgIgit1I2CuYNuY0lQzrugyfECloB.', 2, '2025-07-17 17:54:11'),
(20, 'Johnny English', 'john@gmail.com', '09876566778', '$2y$10$2QGB/1BVnCD6qTpOsw8kyeWJ.gGvD3Em6Wziv251do3VfBo7B6hT.', 2, '2025-07-17 18:00:55'),
(21, 'Jerome Egano', 'jenny1@gmail.com', '09617374237', '$2y$10$aIqG3wjOe4.pKVG3ADgT6.vQTrxuqFafUQHQOWA0l8b0w6SIBqz5e', 2, '2025-07-17 18:19:33'),
(22, 'Brock Lesner', 'brock@gmail.co', '0986363748', '$2y$10$jzqQ1dV6nGbSCQKOnoAcsOqy7xmFmJP1HHIa7PJo25NH.UjxByTqG', 3, '2025-07-17 18:28:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`qr_id`),
  ADD KEY `app_id` (`app_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qr_codes`
--
ALTER TABLE `qr_codes`
  MODIFY `qr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `applications` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `applications` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD CONSTRAINT `qr_codes_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `applications` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `statuses`
--
ALTER TABLE `statuses`
  ADD CONSTRAINT `statuses_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `applications` (`app_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statuses_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
