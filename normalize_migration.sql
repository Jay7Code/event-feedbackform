-- ═══════════════════════════════════════════════════════════════
-- NORMALIZATION MIGRATION SCRIPT
-- Event Feedback System — Migrate to 3NF
-- 
-- IMPORTANT: Back up your database before running this script!
-- Run this in phpMyAdmin or MySQL CLI against event_feedback_db.
-- ═══════════════════════════════════════════════════════════════

USE `event_feedback_db`;

-- ─── Step 1: Create the `locations` table ───
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_location_name` (`location_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── Step 2: Seed default dropdown locations ───
INSERT IGNORE INTO `locations` (`location_name`) VALUES
  ('19th T'),
  ('Adivay Hall'),
  ('St. Patricks');

-- ─── Step 3: Populate locations from any existing events data ───
INSERT IGNORE INTO `locations` (`location_name`)
  SELECT DISTINCT `location`
  FROM `events`
  WHERE `location` IS NOT NULL
    AND `location` != ''
    AND `location` NOT IN (SELECT `location_name` FROM `locations`);

-- ─── Step 4: Add location_id column to events ───
ALTER TABLE `events`
  ADD COLUMN IF NOT EXISTS `location_id` int(11) DEFAULT NULL;

-- ─── Step 5: Backfill location_id from the locations lookup ───
UPDATE `events` e
  JOIN `locations` l ON e.`location` = l.`location_name`
SET e.`location_id` = l.`id`
WHERE e.`location_id` IS NULL;

-- ─── Step 6: Deduplicate events ───
-- Create a temp table mapping each duplicate event row to the 
-- "canonical" (earliest) event id for that unique combination.
CREATE TEMPORARY TABLE `_event_dedup` AS
SELECT
  e.`id` AS `old_id`,
  (SELECT MIN(e2.`id`)
   FROM `events` e2
   WHERE e2.`event_name` = e.`event_name`
     AND (e2.`event_date` = e.`event_date` OR (e2.`event_date` IS NULL AND e.`event_date` IS NULL))
     AND (e2.`event_time` = e.`event_time` OR (e2.`event_time` IS NULL AND e.`event_time` IS NULL))
     AND (e2.`location_id` = e.`location_id` OR (e2.`location_id` IS NULL AND e.`location_id` IS NULL))
  ) AS `canonical_id`
FROM `events` e;

-- ─── Step 7: Re-link attendees to the canonical event IDs ───
UPDATE `attendees` a
  JOIN `_event_dedup` d ON a.`event_id` = d.`old_id`
SET a.`event_id` = d.`canonical_id`
WHERE d.`old_id` != d.`canonical_id`;

-- ─── Step 8: Delete the duplicate event rows ───
DELETE e FROM `events` e
  JOIN `_event_dedup` d ON e.`id` = d.`old_id`
WHERE d.`old_id` != d.`canonical_id`;

DROP TEMPORARY TABLE `_event_dedup`;

-- ─── Step 9: Drop the old location varchar column ───
ALTER TABLE `events` DROP COLUMN IF EXISTS `location`;

-- ─── Step 10: Add foreign key for location_id ───
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_location`
  FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`)
  ON DELETE SET NULL;

-- ─── Step 11: Add unique constraint to prevent future duplicates ───
ALTER TABLE `events`
  ADD UNIQUE KEY `uq_event_identity` (`event_name`, `event_date`, `event_time`, `location_id`);

-- ═══════════════════════════════════════════════════════════════
-- Migration complete! The schema is now in 3NF.
-- ═══════════════════════════════════════════════════════════════
