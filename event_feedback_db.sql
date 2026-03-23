CREATE DATABASE IF NOT EXISTS `event_feedback_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `event_feedback_db`;

-- ─── Events table ───
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Attendees table ───
CREATE TABLE IF NOT EXISTS `attendees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `attendee_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Event Feedbacks table ───
CREATE TABLE IF NOT EXISTS `event_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendee_id` int(11) NOT NULL,
  `event_planning` tinyint(4) NOT NULL DEFAULT 0,
  `speaker_effectiveness` tinyint(4) NOT NULL DEFAULT 0,
  `venue_setup` tinyint(4) NOT NULL DEFAULT 0,
  `time_management` tinyint(4) NOT NULL DEFAULT 0,
  `audience_participation` tinyint(4) NOT NULL DEFAULT 0,
  `overall_experience` tinyint(4) NOT NULL DEFAULT 0,
  `food_beverages` tinyint(4) NOT NULL DEFAULT 0,
  `technical_support` tinyint(4) NOT NULL DEFAULT 0,
  `effective_aspects` text DEFAULT NULL,
  `improvement_suggestions` text DEFAULT NULL,
  `participate_future` varchar(10) DEFAULT NULL,
  `additional_feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`attendee_id`) REFERENCES `attendees`(`id`) ON DELETE CASCADE,
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Admin users table ───
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
