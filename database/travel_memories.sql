-- Travel Memories Database Schema
-- Compatible with MySQL 5.7+ & PHP 8+

CREATE DATABASE IF NOT EXISTS `travel_memories` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `travel_memories`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Trips Table
CREATE TABLE IF NOT EXISTS `trips` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Bangladesh',
  `division` VARCHAR(100) DEFAULT NULL,
  `district` VARCHAR(100) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `companions` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `featured` TINYINT(1) DEFAULT 0,
  `published` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Photos Table
CREATE TABLE IF NOT EXISTS `photos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `trip_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `medium_path` VARCHAR(255) DEFAULT NULL,
  `thumbnail_path` VARCHAR(255) DEFAULT NULL,
  `caption` VARCHAR(255) DEFAULT NULL,
  `location` VARCHAR(255) DEFAULT NULL,
  `taken_date` DATE DEFAULT NULL,
  `display_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Seed Data --

-- Default Admin User (Email: admin@example.com, Password: admin123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Traveler Admin', 'admin@example.com', '$2y$10$0ZGd2Updz//fSCs3iSR.AOZy0Cqg7Ke6Gv0HGxtuzyALiu.daPJ2m', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Initial Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'MY TRAVEL MEMORIES'),
('site_subtitle', 'Places I\'ve Been, Moments I\'ve Captured.'),
('author_name', 'Travel Explorer'),
('author_bio', 'Welcome to my travel journal. Here I document my journeys across Bangladesh and beyond, capturing timeless landscapes, hidden trails, and unforgettable cultural experiences.'),
('hero_button_text', 'Explore My Journey')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

-- Demo Trips
INSERT INTO `trips` (`id`, `title`, `slug`, `start_date`, `end_date`, `country`, `division`, `district`, `location`, `companions`, `description`, `cover_image`, `featured`, `published`) VALUES
(1, 'Cox\'s Bazar Coastal Expedition', 'coxs-bazar-coastal-expedition', '2026-09-20', '2026-09-23', 'Bangladesh', 'Chittagong', 'Cox\'s Bazar', 'Laboni & Inani Beach', 'Friends', 'An enchanting 4-day retreat along the world\'s longest natural sea beach. We witnessed mesmerizing ocean sunsets, sampled local coastal seafood, and explored the tranquil marine drive down to Inani.', 'uploads/trips/coxsbazar_cover.jpg', 1, 1),

(2, 'Bandarban Cloud Sanctuary', 'bandarban-cloud-sanctuary', '2026-06-12', '2026-06-15', 'Bangladesh', 'Chittagong', 'Bandarban', 'Nilgiri & Chimbuk Hill', 'Solo', 'Trekking through misty peak trails and cloudscapes in Nilgiri. The breathtaking valleys, indigenous hill culture, and serene mountain air made this trip unforgettable.', 'uploads/trips/bandarban_cover.jpg', 1, 1),

(3, 'Sylhet Emerald Tea Gardens', 'sylhet-emerald-tea-gardens', '2025-11-05', '2025-11-08', 'Bangladesh', 'Sylhet', 'Sylhet', 'Jaflong & Sreemangal', 'Family', 'Wandering through infinite green tea estates in Sreemangal and the crystal clear waters of Jaflong along the Meghalaya border. Pure nature at its best.', 'uploads/trips/sylhet_cover.jpg', 1, 1),

(4, 'Rajshahi Silk & Heritage Walk', 'rajshahi-silk-and-heritage-walk', '2025-04-18', '2025-04-20', 'Bangladesh', 'Rajshahi', 'Rajshahi', 'Padma Riverfront & Varendra Museum', 'Friends', 'A peaceful weekend by the mighty Padma River. We enjoyed evening boat rides, visited the historical Varendra Research Museum, and devoured famous Rajshahi mangoes.', 'uploads/trips/rajshahi_cover.jpg', 0, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Demo Photos
INSERT INTO `photos` (`id`, `trip_id`, `image_path`, `medium_path`, `thumbnail_path`, `caption`, `location`, `taken_date`, `display_order`) VALUES
(1, 1, 'uploads/trips/coxsbazar_cover.jpg', 'uploads/trips/coxsbazar_cover.jpg', 'uploads/trips/coxsbazar_cover.jpg', 'Sunset view at Laboni Point', 'Cox\'s Bazar Beach', '2026-09-21', 1),
(2, 2, 'uploads/trips/bandarban_cover.jpg', 'uploads/trips/bandarban_cover.jpg', 'uploads/trips/bandarban_cover.jpg', 'Morning mist over Nilgiri hills', 'Nilgiri Peak', '2026-06-13', 1),
(3, 3, 'uploads/trips/sylhet_cover.jpg', 'uploads/trips/sylhet_cover.jpg', 'uploads/trips/sylhet_cover.jpg', 'Lush green tea garden rows in morning light', 'Sreemangal', '2025-11-06', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;
