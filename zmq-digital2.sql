-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 31, 2025 at 08:27 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zmq-digital2`
--

-- --------------------------------------------------------

--
-- Table structure for table `media_files`
--

CREATE TABLE `media_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_extension` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_files`
--

INSERT INTO `media_files` (`id`, `media_thumbnail`, `title`, `description`, `file_type`, `mime_type`, `file_extension`, `file_size`, `file_url`, `uploaded_by`, `project_id`, `created_at`, `updated_at`) VALUES
(46, '', 'title sample 22', 'image description', 'image', 'image/jpeg', 'jpg', 1770, 'storage/media/Generic_Banner_final_v065.jpg', 1, 26, '2025-06-23 02:37:48', '2025-06-23 05:45:08'),
(47, '', 'media 2', 'media 2 000', 'image', 'image/png', 'png', 1454, 'storage/media/Slider-Two_C.png', 1, 26, '2025-06-23 05:45:08', '2025-06-23 05:45:08'),
(48, '', 'media mp4', 'media mp4', 'video', 'video/mp4', 'mp4', 22694, 'storage/media/01-Story-of-Radha1.mp4', 1, 27, '2025-06-23 06:29:21', '2025-06-30 05:50:58'),
(50, '', 'mp3', 'mp3', 'audio', 'audio/mpeg', 'mp3', 655, 'storage/media/azan11.mp3', 1, 27, '2025-06-23 06:34:35', '2025-06-30 05:50:58'),
(51, '', 'doc', 'doc', 'document', 'application/msword', 'doc', 98, 'storage/media/file-sample_100kB1.doc', 1, 27, '2025-06-23 06:39:17', '2025-06-30 05:50:58'),
(52, '', 'ppt', 'ppt', 'presentation', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'pptx', 404, 'storage/media/samplepptx1.pptx', 1, 27, '2025-06-23 06:48:13', '2025-06-30 05:50:58'),
(53, '', 'ms-excel', 'ms excel', 'spreadsheet', 'application/vnd.ms-excel', 'xls', 137, 'storage/media/file_example_XLS_10001.xls', 1, 27, '2025-06-23 06:55:54', '2025-06-30 05:50:58'),
(54, '', 'media mp4', 'media mp4', 'image', 'image/png', 'png', 1401, 'storage/media/Slider-Two_B2.png', 1, 29, '2025-06-24 05:49:56', '2025-06-24 05:58:35'),
(55, '', 'media mp4', 'media mp4', 'video', 'video/mp4', 'mp4', 1031, 'storage/media/big_buck_bunny_720p_1mb1.mp4', 1, 29, '2025-06-24 05:50:18', '2025-06-24 05:58:35'),
(56, '', 'csv', 'csv', 'text', 'text/plain', 'csv', 0, 'storage/media/Aasif-s3_accessKeys2.csv', 1, 29, '2025-06-24 05:58:35', '2025-06-24 05:58:35'),
(62, '', 'das', 'asd', 'apk', 'application/java-archive', 'apk', 10708, 'storage/media/apkmirror_(1)5.apk', 1, 44, '2025-06-25 05:56:48', '2025-06-25 05:56:48'),
(65, '', 'csv', 'csv testing', 'text', 'text/plain', 'csv', 0, 'storage/media/Aasif-s3_credentials1.csv', 1, 35, '2025-06-26 06:23:03', '2025-06-30 02:03:22'),
(66, '', 'Media title', 'Media description', 'image', 'image/jpeg', 'jpg', 186, 'storage/media/Agenda_00001_(1)2.jpg', 1, 46, '2025-06-30 02:17:28', '2025-06-30 02:20:33'),
(68, '', 'Media title - pdf', 'Media Description - pdf', 'pdf', 'application/pdf', 'pdf', 2, 'storage/media/13.pdf', 1, 46, '2025-06-30 02:18:20', '2025-06-30 02:20:33'),
(69, '', 'media title - apk', 'media description - apk ', 'apk', 'application/zip', 'apk', 19895, 'storage/media/APKPure1.apk', 1, 46, '2025-06-30 02:20:33', '2025-06-30 02:20:33'),
(70, '', 'Lungs image', 'Lungs image for drtb', 'image', 'image/jpeg', 'jpeg', 9, 'storage/media/images1.jpeg', 1, 39, '2025-06-30 05:54:46', '2025-06-30 05:54:46'),
(71, '', 'azaan audio', 'azaan audio description', 'audio', 'audio/mpeg', 'mp3', 655, 'storage/media/azan13.mp3', 1, 48, '2025-06-30 07:22:05', '2025-07-29 11:55:41'),
(72, '', 'Image file', 'image file description', 'image', 'image/png', 'png', 127, 'storage/media/91.png', 1, 48, '2025-06-30 07:22:05', '2025-07-29 11:55:41'),
(73, '', 'pdf ', 'pdf description', 'pdf', 'application/pdf', 'pdf', 104, 'storage/media/who11.pdf', 1, 48, '2025-06-30 07:22:05', '2025-07-29 11:55:41'),
(74, '', 'video movie', 'video movie description', 'video', 'video/mp4', 'mp4', 22694, 'storage/media/01_Story_of_Radha1.mp4', 1, 48, '2025-06-30 07:22:05', '2025-07-29 11:55:41'),
(75, '', 'android app ', 'android app apk description', 'apk', 'application/java-archive', 'apk', 10708, 'storage/media/apkmirror3.apk', 1, 48, '2025-06-30 07:22:05', '2025-07-29 11:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_long_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_short_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_of_publish` date DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `project_thumbnail`, `project_long_description`, `project_short_description`, `language`, `year_of_publish`, `created_by`, `created_at`, `updated_at`) VALUES
(26, 'Demo Project', 'storage/thumbnails/Qaff_logo_nw1.jpg', 'Unable to access an error message corresponding to your field name Media Titles.(required_with)', 'Demo Project', 'English', '2025-06-24', 1, '2025-06-23 02:37:48', '2025-06-23 05:45:08'),
(27, 'Demo Project 2', 'storage/thumbnails/EventLoop_raw3.png', 'Demo Project 33', 'Demo Project 33', 'English', '2025-06-19', 1, '2025-06-23 06:15:13', '2025-06-30 05:50:57'),
(29, 'Demo Projectwe33', 'storage/thumbnails/Slider-Two_B2.png', 'Demo Project 33', 'Demo Project 33', 'English', '2025-06-24', 1, '2025-06-24 05:43:57', '2025-06-24 05:58:35'),
(35, 'Test apk 2', 'storage/thumbnails/1_main_page8.png', 'asddsa', 'Demo Project short Description', 'asdasd', '2025-06-26', 1, '2025-06-25 03:46:35', '2025-06-30 02:03:22'),
(39, 'Demo Project', 'storage/thumbnails/798-min2.JPG', 'asdasd', 'asddas', 'dasasd', '2025-06-27', 1, '2025-06-25 04:16:31', '2025-06-30 05:54:46'),
(44, 'ads', 'storage/thumbnails/1_main_page.png', 'asd', 'asdasd', 'asd', '2025-06-25', 1, '2025-06-25 05:56:48', '2025-06-25 05:56:48'),
(46, 'Project Mira', 'storage/thumbnails/15015848.jpg', 'Project Description - Long', 'Project Description - short', 'English', '2025-06-30', 1, '2025-06-30 02:17:28', '2025-06-30 02:20:33'),
(48, 'Test Media Type', 'storage/thumbnails/cd98bd21c837bfe0a905d670501f3a8783b0c16cd1c4c06d9ae1c6008de7a926.jpeg', 'Test Media Type - Long - description', 'Test Media Type - short description', 'English', '2025-06-30', 1, '2025-06-30 07:22:05', '2025-07-29 11:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(2) NOT NULL,
  `role_name` varchar(10) NOT NULL,
  `role_id` int(2) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `role_id`) VALUES
(1, 'Admin', 1),
(2, 'User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(2) NOT NULL,
  `is_active` int(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Aasif', 'admin', 'admin@zmq.in', 'aasif', 1, 1, NULL, NULL),
(5, 'Danish', 'danish009', 'danish@zmq.in', '12345', 2, 1, NULL, NULL),
(6, 'Rahim Ali', 'rahim004', 'rahim@zmq.in', '12345', 2, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `media_files`
--
ALTER TABLE `media_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_files_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `media_files_project_id_foreign` (`project_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_created_by_foreign` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `media_files`
--
ALTER TABLE `media_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `media_files`
--
ALTER TABLE `media_files`
  ADD CONSTRAINT `media_files_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `media_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
