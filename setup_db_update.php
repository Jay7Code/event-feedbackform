<?php
require_once "config.php";
$mysqli = getDBConnection();

$sql1 = "ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS full_name varchar(255) DEFAULT NULL;";
$sql2 = "ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS is_active tinyint(1) NOT NULL DEFAULT 1;";
$sql3 = "ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS email varchar(255) DEFAULT NULL;";

$mysqli->query($sql1);
$mysqli->query($sql2);
$mysqli->query($sql3);

// Insert default admin if table is empty
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM admin_users");
$row = $result->fetch_assoc();
if ($row['cnt'] == 0) {
    $hashed = password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO admin_users (username, password, full_name, is_active) VALUES (?, ?, 'Default Admin', 1)");
    $uname = ADMIN_USERNAME;
    $stmt->bind_param("ss", $uname, $hashed);
    $stmt->execute();
}
echo "Core admin updates completed. ";

// ─── Normalization Migration (3NF) ───
$checkLoc = $mysqli->query("SHOW TABLES LIKE 'locations'");
if ($checkLoc->num_rows == 0) {
    echo "Starting normalization migration... ";
    
    // 1. Create locations
    $mysqli->query("CREATE TABLE `locations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `location_name` varchar(255) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_location_name` (`location_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 2. Seed defaults
    $mysqli->query("INSERT IGNORE INTO `locations` (`location_name`) VALUES ('19th T'), ('Adivay Hall'), ('St. Patricks')");

    // 3. Populate from existing data
    $mysqli->query("INSERT IGNORE INTO `locations` (`location_name`) SELECT DISTINCT `location` FROM `events` WHERE `location` IS NOT NULL AND `location` != ''");

    // 4. Add location_id to events
    $mysqli->query("ALTER TABLE `events` ADD COLUMN IF NOT EXISTS `location_id` int(11) DEFAULT NULL");

    // 5. Backfill IDs
    $mysqli->query("UPDATE `events` e JOIN `locations` l ON e.`location` = l.`location_name` SET e.`location_id` = l.`id` WHERE e.`location_id` IS NULL");

    // 6. Deduplicate Events & Update Attendees (Safety check for existing data)
    // Map duplicates to canonical IDs
    $res = $mysqli->query("SELECT e.id, e.event_name, e.event_date, e.event_time, e.location_id FROM events e");
    $seen = [];
    $toDelete = [];
    while ($row = $res->fetch_assoc()) {
        $key = $row['event_name'] . '|' . $row['event_date'] . '|' . $row['event_time'] . '|' . $row['location_id'];
        if (isset($seen[$key])) {
            $canonicalId = $seen[$key];
            $stmt = $mysqli->prepare("UPDATE attendees SET event_id = ? WHERE event_id = ?");
            $stmt->bind_param("ii", $canonicalId, $row['id']);
            $stmt->execute();
            $toDelete[] = $row['id'];
        } else {
            $seen[$key] = $row['id'];
        }
    }
    
    // Delete duplicates
    if (!empty($toDelete)) {
        $ids = implode(",", $toDelete);
        $mysqli->query("DELETE FROM events WHERE id IN ($ids)");
    }

    // 7. Cleanup
    $mysqli->query("ALTER TABLE `events` DROP COLUMN IF EXISTS `location` ");
    $mysqli->query("ALTER TABLE `events` ADD CONSTRAINT `fk_events_location` FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL");
    $mysqli->query("ALTER TABLE `events` ADD UNIQUE KEY `uq_event_identity` (`event_name`, `event_date`, `event_time`, `location_id`)");

    echo "Normalization complete. ";
}

echo "Database updated successfully.";
?>
