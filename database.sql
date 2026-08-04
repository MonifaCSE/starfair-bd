-- SQL Database Schema for STAR FAIR website
-- Hostinger & Local XAMPP/MAMP Compatible

CREATE DATABASE IF NOT EXISTS `starfair_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `starfair_db`;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Registrations Table
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `dob` DATE NOT NULL,
  `father_name` VARCHAR(255) NOT NULL,
  `mother_name` VARCHAR(255) NOT NULL,
  `gender` VARCHAR(50) NOT NULL,
  `blood_group` VARCHAR(10) NOT NULL,
  `nationality` VARCHAR(100) NOT NULL,
  `occupation` VARCHAR(255) NOT NULL,
  `education` VARCHAR(255) NOT NULL,
  `mobile` VARCHAR(20) NOT NULL,
  `alt_mobile` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL,
  `present_address` TEXT NOT NULL,
  `permanent_address` TEXT NOT NULL,
  `guardian_name` VARCHAR(255) NOT NULL,
  `guardian_mobile` VARCHAR(20) NOT NULL,
  `emergency_name` VARCHAR(255) NOT NULL,
  `emergency_relation` VARCHAR(100) NOT NULL,
  `programmes` TEXT NOT NULL, -- Stored as JSON array or comma-separated string
  `events` TEXT NOT NULL,     -- Stored as JSON array or comma-separated string
  `previous_experience` TEXT DEFAULT NULL,
  `medical_conditions` TEXT DEFAULT NULL,
  `special_skills` TEXT DEFAULT NULL,
  `why_join` TEXT NOT NULL,
  `photo_path` VARCHAR(255) NOT NULL,
  `nid_bc_path` VARCHAR(255) NOT NULL,
  `portfolio_path` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Magazines Table
CREATE TABLE IF NOT EXISTS `magazines` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `date_text` VARCHAR(100) NOT NULL,
  `pdf_path` VARCHAR(255) NOT NULL,
  `cover_path` VARCHAR(255) DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
