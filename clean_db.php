<?php
require_once __DIR__ . "/config.php";
$mysqli = getDBConnection();

$tables = [
    'ef_locations' => ['location_name'],
    'ef_events' => ['event_name'],
    'ef_attendees' => ['attendee_name', 'email', 'contact_no'],
    'ef_event_feedbacks' => ['effective_aspects', 'improvement_suggestions', 'participate_future', 'additional_feedback']
];

foreach ($tables as $table => $columns) {
    $result = $mysqli->query("SELECT * FROM $table");
    while ($row = $result->fetch_assoc()) {
        $updates = [];
        $types = "";
        $params = [];
        $changed = false;
        
        foreach ($columns as $col) {
            if ($row[$col] !== null) {
                $decoded = htmlspecialchars_decode($row[$col], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
                // Also check if they were encoded without quotes previously
                $decoded = htmlspecialchars_decode($decoded);
                
                if ($decoded !== $row[$col]) {
                    $updates[] = "$col = ?";
                    $types .= "s";
                    $params[] = $decoded;
                    $changed = true;
                }
            }
        }
        
        if ($changed) {
            $types .= "i";
            $params[] = $row['id'];
            
            $sql = "UPDATE $table SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
        }
    }
}

// Special case: clean up duplicated "St. Patrick's" locations if they exist
$result = $mysqli->query("SELECT id FROM ef_locations WHERE location_name = 'St. Patrick''s'");
$ids = [];
while ($row = $result->fetch_assoc()) {
    $ids[] = $row['id'];
}
if (count($ids) > 1) {
    $first_id = $ids[0];
    for ($i = 1; $i < count($ids); $i++) {
        $mysqli->query("UPDATE ef_events SET location_id = $first_id WHERE location_id = {$ids[$i]}");
        $mysqli->query("DELETE FROM ef_locations WHERE id = {$ids[$i]}");
    }
}
echo "Database cleaned successfully.\n";
