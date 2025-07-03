-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 03, 2025 at 03:27 AM
-- Server version: 10.6.22-MariaDB-cll-lve
-- PHP Version: 8.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bevgftgnri_dream_apkstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `app_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(255) NOT NULL,
  `apk_file` varchar(255) NOT NULL,
  `screenshots` text DEFAULT NULL,
  `downloads` int(11) DEFAULT 0,
  `category` varchar(100) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `apps`
--

INSERT INTO `apps` (`id`, `user_id`, `app_name`, `description`, `logo`, `apk_file`, `screenshots`, `downloads`, `category`, `tags`, `created_at`) VALUES
(1, 1, 'TestApp', 'Hzbzbxb', 'uploads/6865d5bd99c6d_Screenshot_20250630-223429.jpg', 'uploads/6865d5bda4be6_MpingoTV.apk', '[\"uploads\\/6865d5bda8164_1_Screenshot_20250630-223429.jpg\",\"uploads\\/6865d5bda832a_2_Screenshot_20250629-090712.jpg\"]', 12, 'Sport', 'Sports', '2025-07-03 00:58:37');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `app_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, '🥸 ubaya ubwela siba Nguvu Moya by Mo', '2025-07-03 01:00:11'),
(2, 1, 3, '😆🤣', '2025-07-03 01:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','assistant','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `active`) VALUES
(1, 'kisekason', 'basanzietech@gmail.com', '$2y$10$UFfr9Wt8UQ.hUF.9/6vxvuM6k5etFIOJgOBWU1cwLveUXlZT6narO', 'admin', '2025-07-03 00:52:33', 1),
(2, 'benjamini', 'benjaminkiseka80@gmail.com', '$2y$10$OVHyeRv5H7T2fOKiQrxP6OpyobRkHiYBsCOI92psAVQW9HQEXhMIi', 'user', '2025-07-03 01:03:00', 1),
(3, 'admin', 'admin@gmail.com', '$2y$10$Sqk4RnZNIkRL.22mpv/N0uoGXh9H3K7lDZL.Bkpv77Ho4ewDQ9tG2', 'assistant', '2025-07-03 01:05:10', 1),
(4, 'Snaki', 'support@ekilie.com', '$2y$10$Ev6KPyzuESD09fwzTe4EdO5FZROjwdRfdmDdFt0u6YZDgVkShcqde', 'user', '2025-07-03 02:26:04', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_name` (`app_name`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_id` (`app_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apps`
--
ALTER TABLE `apps`
  ADD CONSTRAINT `apps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `apps` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
