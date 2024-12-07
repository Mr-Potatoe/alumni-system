-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2024 at 03:26 PM
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
-- Database: `alumni_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` enum('Super Admin','Department Head','Staff') DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `last_login`, `is_active`, `department_id`) VALUES
(1, 'admin', 'admin@yourdomain.com', '$2y$10$KWfXAi.ZnNM16SzaLIvd7OQyrhgot1sjY.HMi7aSNglDcfIYeoMHG', 'System', 'Administrator', 'Super Admin', '2024-12-07 12:48:10', 1, NULL),
(2, 'ynsns', 'james12375123@gmail.com', '$2y$10$FRwznL30IvaVQ/ltGCipiu0Tx2U8jbVaiHTw90wvfnehpO.eNw9Z2', 'huhu', 'Bonds', 'Department Head', NULL, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `communicationlog`
--

CREATE TABLE `communicationlog` (
  `log_id` int(11) NOT NULL,
  `sender_admin_id` int(11) DEFAULT NULL,
  `communication_type` enum('Announcement','Newsletter','Event Invitation','Individual Email') DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message_body` text DEFAULT NULL,
  `sent_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipient_group` enum('All Alumni','Specific Graduation Year','Filtered Group') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `communicationlog`
--

INSERT INTO `communicationlog` (`log_id`, `sender_admin_id`, `communication_type`, `subject`, `message_body`, `sent_timestamp`, `recipient_group`) VALUES
(1, 1, 'Announcement', 'sad', 'asd', '2024-12-07 12:28:58', 'All Alumni'),
(2, 1, 'Announcement', 'huhu', 'sadsadhsahdsa', '2024-12-07 13:06:23', 'All Alumni'),
(3, 1, 'Announcement', 'huhu', 'sadsadhsahdsa', '2024-12-07 13:08:02', 'All Alumni'),
(4, 2, 'Announcement', 'come', 'here noii', '2024-12-07 13:45:07', '');

-- --------------------------------------------------------

--
-- Table structure for table `communicationrecipients`
--

CREATE TABLE `communicationrecipients` (
  `recipient_id` int(11) NOT NULL,
  `log_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `delivery_status` enum('Sent','Delivered','Read','Failed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `communicationrecipients`
--

INSERT INTO `communicationrecipients` (`recipient_id`, `log_id`, `user_id`, `delivery_status`) VALUES
(1, 1, 1, 'Sent'),
(2, 1, 4, 'Sent'),
(3, 2, 1, 'Sent'),
(4, 2, 4, 'Sent'),
(5, 3, 1, 'Sent'),
(6, 3, 4, 'Sent'),
(7, 4, 5, 'Sent');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `created_at`) VALUES
(1, 'Computer Science', '2024-12-07 13:22:41'),
(2, 'Information Technology', '2024-12-07 13:22:41'),
(3, 'Business Administration', '2024-12-07 13:22:41'),
(4, 'Engineering', '2024-12-07 13:22:41'),
(5, 'Mathematics', '2024-12-07 13:22:41'),
(6, 'Marine Engineering', '2024-12-07 14:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `employmenthistory`
--

CREATE TABLE `employmenthistory` (
  `employment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `employment_type` enum('Full-time','Part-time','Freelance','Contract','Self-employed') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `industry` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employmenthistory`
--

INSERT INTO `employmenthistory` (`employment_id`, `user_id`, `company_name`, `position_title`, `employment_type`, `start_date`, `end_date`, `is_current`, `industry`, `location`) VALUES
(3, 4, 'sad company', 'head hshs', 'Freelance', '2024-12-13', NULL, 1, 'sehehe', 'norththt'),
(7, 5, 'sad company', 'head', 'Part-time', '2024-12-07', NULL, 1, 'invidia', 'japan');

-- --------------------------------------------------------

--
-- Table structure for table `systemauditlog`
--

CREATE TABLE `systemauditlog` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `action_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `systemauditlog`
--

INSERT INTO `systemauditlog` (`audit_id`, `user_id`, `action_type`, `action_details`, `ip_address`, `timestamp`) VALUES
(1, NULL, 'ADMIN_CREATED', 'Default admin account created (ID: 1)', 'SYSTEM', '2024-12-07 11:11:14'),
(2, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 11:15:05'),
(3, NULL, 'ADMIN_SETUP', 'Default admin account created', 'SYSTEM', '2024-12-07 11:17:23'),
(4, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 2', '::1', '2024-12-07 11:35:40'),
(5, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 3', '::1', '2024-12-07 11:40:03'),
(6, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 1', '::1', '2024-12-07 11:40:28'),
(7, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 1', '::1', '2024-12-07 11:45:38'),
(8, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 1', '::1', '2024-12-07 11:45:58'),
(9, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 11:58:25'),
(10, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 11:58:34'),
(11, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 11:58:50'),
(12, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 11:58:59'),
(13, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 11:59:11'),
(14, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 12:00:12'),
(15, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 12:03:02'),
(16, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 12:03:14'),
(17, NULL, 'LOGIN_FAILED', 'Failed login attempt for username: admin', '::1', '2024-12-07 12:03:25'),
(18, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 12:03:49'),
(19, 1, 'USER_APPROVAL', 'Approved user ID: 4', '::1', '2024-12-07 12:06:57'),
(20, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 12:12:42'),
(21, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 12:22:15'),
(22, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 4', '::1', '2024-12-07 12:40:06'),
(23, 1, 'ALUMNI_UPDATE', 'Updated alumni profile for user ID: 1', '::1', '2024-12-07 12:40:21'),
(24, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 12:48:10'),
(25, 1, 'Send Announcement', 'Sent announcement: huhu to All Alumni', '::1', '2024-12-07 13:06:23'),
(26, 1, 'Send Announcement', 'Sent announcement: huhu to All Alumni', '::1', '2024-12-07 13:08:02'),
(27, 1, 'Create Department Head', 'Created Department Head account for ynsns', '::1', '2024-12-07 13:25:37'),
(28, 2, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 13:27:14'),
(29, 2, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 13:30:54'),
(30, 2, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 13:44:46'),
(31, 2, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 13:56:46'),
(32, 2, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 14:01:36'),
(33, 1, 'LOGIN', 'Admin login successful', '::1', '2024-12-07 14:06:03'),
(34, 1, 'USER_APPROVAL', 'Approved user ID: 5', '::1', '2024-12-07 14:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `systemsettings`
--

CREATE TABLE `systemsettings` (
  `id` int(11) NOT NULL,
  `auto_approve` tinyint(1) DEFAULT 0,
  `email_notifications` tinyint(1) DEFAULT 1,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `systemsettings`
--

INSERT INTO `systemsettings` (`id`, `auto_approve`, `email_notifications`, `maintenance_mode`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, '2024-12-07 11:17:23', '2024-12-07 14:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `useraddresses`
--

CREATE TABLE `useraddresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address_type` enum('Recent','Present','Permanent') DEFAULT NULL,
  `street_address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraddresses`
--

INSERT INTO `useraddresses` (`address_id`, `user_id`, `address_type`, `street_address`, `city`, `state`, `country`, `postal_code`, `is_current`) VALUES
(1, 1, 'Present', 'qwe', NULL, NULL, NULL, NULL, 1),
(2, 4, 'Present', 'norht', NULL, NULL, NULL, NULL, 0),
(3, 4, 'Permanent', 'sad', 'cebu', 'sdaad', 'philippines', '7020', 0),
(4, 4, 'Present', 'hahdsa', 'asd', 'sdaad', 'asd', '9239', 1),
(5, 5, 'Present', 'libertad', 'cebu', 'manila', 'philippines', '7020', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `registration_status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `graduation_year` int(11) NOT NULL,
  `graduation_semester` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified` tinyint(1) DEFAULT 0,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `registration_status`, `registration_date`, `last_login`, `first_name`, `last_name`, `middle_name`, `contact_number`, `birth_date`, `gender`, `graduation_year`, `graduation_semester`, `is_active`, `email_verified`, `department_id`) VALUES
(1, 'john.doe@example.com', '$2y$10$GwK603agXYFwrOUVlXaiveJ7ZyROBupBmSiKH61gWm.Hqb70bKa26', 'Approved', '2024-12-07 10:40:41', NULL, 'John', 'Doe', 'sdsadsa', '09776445', NULL, '', 2020, 'First', 1, 1, NULL),
(4, 'zakmadicc@gmail.com', '$2y$10$bqufP5Kme2VB9uu3p.Pu.u2/uL0Py6oUslyRfOVTozRSENy5Eg7Ee', 'Approved', '2024-12-07 11:56:13', NULL, 'James', 'asd', '', '', NULL, NULL, 2004, '', 1, 0, NULL),
(5, 'jaymeajarns@gmail.com', '$2y$10$BSRGXWxVoz73ohJlf3.eG.cdBcG0zCC66lt4AfF2dfVqUaUhypl2S', 'Approved', '2024-12-07 13:42:22', NULL, 'jayme ', 'aj', 'orqqq', '0971710673', '2024-12-11', 'Male', 2024, 'Second', 1, 0, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `communicationlog`
--
ALTER TABLE `communicationlog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `sender_admin_id` (`sender_admin_id`);

--
-- Indexes for table `communicationrecipients`
--
ALTER TABLE `communicationrecipients`
  ADD PRIMARY KEY (`recipient_id`),
  ADD KEY `log_id` (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `employmenthistory`
--
ALTER TABLE `employmenthistory`
  ADD PRIMARY KEY (`employment_id`),
  ADD KEY `idx_employment_user` (`user_id`);

--
-- Indexes for table `systemauditlog`
--
ALTER TABLE `systemauditlog`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `systemsettings`
--
ALTER TABLE `systemsettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `useraddresses`
--
ALTER TABLE `useraddresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_addresses_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_graduation_year` (`graduation_year`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `communicationlog`
--
ALTER TABLE `communicationlog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `communicationrecipients`
--
ALTER TABLE `communicationrecipients`
  MODIFY `recipient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employmenthistory`
--
ALTER TABLE `employmenthistory`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `systemauditlog`
--
ALTER TABLE `systemauditlog`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `systemsettings`
--
ALTER TABLE `systemsettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `useraddresses`
--
ALTER TABLE `useraddresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `admins_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `communicationlog`
--
ALTER TABLE `communicationlog`
  ADD CONSTRAINT `communicationlog_ibfk_1` FOREIGN KEY (`sender_admin_id`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `communicationrecipients`
--
ALTER TABLE `communicationrecipients`
  ADD CONSTRAINT `communicationrecipients_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `communicationlog` (`log_id`),
  ADD CONSTRAINT `communicationrecipients_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `employmenthistory`
--
ALTER TABLE `employmenthistory`
  ADD CONSTRAINT `employmenthistory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `useraddresses`
--
ALTER TABLE `useraddresses`
  ADD CONSTRAINT `useraddresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
