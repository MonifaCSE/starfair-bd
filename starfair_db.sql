-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 17, 2026 at 10:07 AM
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
-- Database: `starfair_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin@starfairbd.com', '$2y$10$Ga5ifFiyYZjBBCnWkimrzehyRYNPnYIImgKDqDHM5QQpvhST38hui', '2026-08-04 11:28:58');

-- --------------------------------------------------------

--
-- Table structure for table `magazines`
--

CREATE TABLE `magazines` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_text` varchar(100) NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `cover_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magazines`
--

INSERT INTO `magazines` (`id`, `title`, `description`, `date_text`, `pdf_path`, `cover_path`, `display_order`, `created_at`, `updated_at`) VALUES
(4, 'Model Edition', 'Modern lifestyle, fashion inspiration and exclusive features.', '5 august 2026', 'issue_0f90819a9be7af1bde4a.pdf', 'cover_81327ee430501e1128ab.jpg', 2, '2026-08-06 07:14:46', '2026-08-06 07:14:46'),
(5, 'Fashion Edition', 'Fashion trends, beauty insights, exclusive interviews and event coverage.', 'January 2026', 'issue_464089fc8abe3602f271.pdf', 'cover_338ebec2a60a5d47a5ef.jpg', 1, '2026-08-06 07:15:51', '2026-08-06 07:15:51');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `blood_group` varchar(10) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `education` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `alt_mobile` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `guardian_name` varchar(255) NOT NULL,
  `guardian_mobile` varchar(20) NOT NULL,
  `emergency_name` varchar(255) NOT NULL,
  `emergency_relation` varchar(100) NOT NULL,
  `programmes` text NOT NULL,
  `events` text NOT NULL,
  `previous_experience` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `special_skills` text DEFAULT NULL,
  `why_join` text NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `nid_bc_path` varchar(255) NOT NULL,
  `portfolio_path` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `name`, `dob`, `father_name`, `mother_name`, `gender`, `blood_group`, `nationality`, `occupation`, `education`, `mobile`, `alt_mobile`, `email`, `present_address`, `permanent_address`, `guardian_name`, `guardian_mobile`, `emergency_name`, `emergency_relation`, `programmes`, `events`, `previous_experience`, `medical_conditions`, `special_skills`, `why_join`, `photo_path`, `nid_bc_path`, `portfolio_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'John Doe Test', '2000-01-01', 'Father Test', 'Mother Test', 'male', 'O+', 'Bangladeshi', 'Student', 'HSC', '01712345678', '01812345678', 'john.doe@test.com', 'Dhaka, Bangladesh', 'Dhaka, Bangladesh', 'Guardian Test', '01912345678', 'Emergency Test', 'Brother', '\"Acting\"', '\"Fashion Show\"', NULL, NULL, NULL, 'Interested in modeling.', '/photo_ab10f6a334cda6f6.jpg', '/nid_bc_c85694a8605fafb6.pdf', NULL, 'pending', '2026-08-04 11:42:35', '2026-08-04 11:42:35'),
(2, 'John Doe Test', '2000-01-01', 'Father Test', 'Mother Test', 'male', 'O+', 'Bangladeshi', 'Student', 'HSC', '01712345678', '01812345678', 'john.doe@test.com', 'Dhaka, Bangladesh', 'Dhaka, Bangladesh', 'Guardian Test', '01912345678', 'Emergency Test', 'Brother', '\"Acting\"', '\"Fashion Show\"', NULL, NULL, NULL, 'Interested in modeling.', '175e58cd091ae7e9c12c35a1/photo_ba28ee09be3bed53.jpg', '175e58cd091ae7e9c12c35a1/nid_bc_6278a139d3476abc.pdf', NULL, 'pending', '2026-08-04 11:42:50', '2026-08-04 11:42:50'),
(3, 'Monifa Sultana', '1998-12-10', 'Nurul Alam', 'Mariam Sultana', 'female', 'O+', 'Bangladeshi', 'educator', 'B.sc in CSE', '01791662433', '01791662418', 'monifasultana5637@gmail.com', 'Bakolia, Chittagong', 'chittagong', 'Mariam Sultana', '01791662418', 'Mariam Sultana', 'Mother', '[\"Communication\"]', '[]', 'nnn', 'nn', 'nn', 'nnn', 'e402c068ffc717a3c32c6a6f/photo_a77ef45a74bb50e7.png', 'e402c068ffc717a3c32c6a6f/nid_bc_3bc1e05b3f8bacd5.pdf', 'e402c068ffc717a3c32c6a6f/portfolio_d86cd64020e102c2.pdf', 'pending', '2026-08-04 11:44:54', '2026-08-04 11:44:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `magazines`
--
ALTER TABLE `magazines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `magazines`
--
ALTER TABLE `magazines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
