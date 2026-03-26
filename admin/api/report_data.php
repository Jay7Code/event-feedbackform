<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * REPORT DATA API
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
require_once __DIR__ . "/../../config.php";

// Authorize both admin and superadmin
if (
    (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) &&
    (!isset($_SESSION["superadmin_logged_in"]) || $_SESSION["superadmin_logged_in"] !== true)
) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

$mysqli = getDBConnection();

$date_from = $_GET['date_from'] ?? null;
$date_to   = $_GET['date_to']   ?? null;

$whereClause = "1=1";
$params = [];
$types = "";

if ($date_from && $date_to) {
    $whereClause = "DATE(ef.created_at) BETWEEN ? AND ?";
    $params[] = $date_from;
    $params[] = $date_to;
    $types .= "ss";
}

function fetchAllAndFree($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

$response = [
    "date_from" => $date_from,
    "date_to"   => $date_to,
    "summary"   => [],
    "nps_distribution" => [],
    "category_averages" => [],
    "event_breakdown" => [],
    "daily_breakdown" => [],
    "comments"  => []
];

// 1. Summary details
$sqlSummary = "
    SELECT 
        COUNT(*) as total_responses,
        ROUND(AVG(NULLIF(ef.overall_experience, 0)), 1) as avg_nps,
        SUM(CASE WHEN ef.overall_experience IN (1,2) THEN 1 ELSE 0 END) as count_poor,
        SUM(CASE WHEN ef.overall_experience = 3 THEN 1 ELSE 0 END) as count_good,
        SUM(CASE WHEN ef.overall_experience IN (4,5) THEN 1 ELSE 0 END) as count_excellent
    FROM ef_event_feedbacks ef
    WHERE $whereClause
";
$stmt = $mysqli->prepare($sqlSummary);
if ($types) $stmt->bind_param($types, ...$params);
$summaryData = fetchAllAndFree($stmt)[0];

$response["summary"] = [
    "total_responses" => (int)$summaryData['total_responses'],
    "avg_nps"         => $summaryData['avg_nps'] ? (float)$summaryData['avg_nps'] : null,
    "poor"            => (int)$summaryData['count_poor'],
    "good"            => (int)$summaryData['count_good'],
    "excellent"       => (int)$summaryData['count_excellent']
];

// 2. NPS Distribution (1-5)
$sqlDist = "
    SELECT ef.overall_experience as score, COUNT(*) as count
    FROM ef_event_feedbacks ef
    WHERE $whereClause AND ef.overall_experience > 0
    GROUP BY ef.overall_experience
";
$stmt = $mysqli->prepare($sqlDist);
if ($types) $stmt->bind_param($types, ...$params);
$distRows = fetchAllAndFree($stmt);
$distMap = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
foreach ($distRows as $row) {
    $distMap[(int)$row['score']] = (int)$row['count'];
}
$response["nps_distribution"] = $distMap;

// 3. Category Averages
$categories = [
    "event_planning" => "Event Planning",
    "speaker_effectiveness" => "Speaker Effectiveness",
    "venue_setup" => "Venue & Setup",
    "time_management" => "Time Management",
    "audience_participation" => "Audience Participation",
    "food_beverages" => "Food & Beverages",
    "technical_support" => "Technical Support"
];
$catSelects = [];
foreach (array_keys($categories) as $k) {
    $catSelects[] = "ROUND(AVG(NULLIF(ef.$k, 0)), 1) as avg_$k";
}
$sqlCat = "SELECT " . implode(", ", $catSelects) . " FROM ef_event_feedbacks ef WHERE $whereClause";
$stmt = $mysqli->prepare($sqlCat);
if ($types) $stmt->bind_param($types, ...$params);
$catData = fetchAllAndFree($stmt)[0];

foreach ($categories as $k => $label) {
    $response["category_averages"][] = [
        "label" => $label,
        "avg" => $catData["avg_$k"] ? (float)$catData["avg_$k"] : 0
    ];
}

// 4. Event Breakdown (Responses per event)
$sqlEvent = "
    SELECT CONCAT(e.event_name, ' (', COALESCE(l.location_name, 'N/A'), ')') as event_name, COUNT(ef.id) as count
    FROM ef_event_feedbacks ef
    JOIN ef_attendees a ON ef.attendee_id = a.id
    JOIN ef_events e ON e.id = a.event_id
    LEFT JOIN ef_locations l ON e.location_id = l.id
    WHERE $whereClause
    GROUP BY e.id, l.location_name
    ORDER BY count DESC
";
$stmt = $mysqli->prepare($sqlEvent);
if ($types) $stmt->bind_param($types, ...$params);
$response["event_breakdown"] = fetchAllAndFree($stmt);

// 5. Daily Breakdown
$sqlDaily = "
    SELECT DATE(ef.created_at) as date, COUNT(*) as count, ROUND(AVG(NULLIF(ef.overall_experience, 0)), 1) as avg_rating
    FROM ef_event_feedbacks ef
    WHERE $whereClause
    GROUP BY DATE(ef.created_at)
    ORDER BY date ASC
";
$stmt = $mysqli->prepare($sqlDaily);
if ($types) $stmt->bind_param($types, ...$params);
$response["daily_breakdown"] = fetchAllAndFree($stmt);

// 6. Comments
$sqlComments = "
    SELECT 
        CONCAT(e.event_name, ' @ ', COALESCE(l.location_name, 'N/A')) as event,
        a.attendee_name as guest_name,
        ef.overall_experience as overall_rating,
        ef.effective_aspects as effective,
        ef.improvement_suggestions as improvement,
        ef.additional_feedback as additional,
        ef.created_at
    FROM ef_event_feedbacks ef
    JOIN ef_attendees a ON ef.attendee_id = a.id
    JOIN ef_events e ON e.id = a.event_id
    LEFT JOIN ef_locations l ON e.location_id = l.id
    WHERE $whereClause
      AND (ef.effective_aspects != '' OR ef.improvement_suggestions != '' OR ef.additional_feedback != '')
    ORDER BY ef.created_at DESC
";
$stmt = $mysqli->prepare($sqlComments);
if ($types) $stmt->bind_param($types, ...$params);
$response["comments"] = fetchAllAndFree($stmt);

header('Content-Type: application/json');
echo json_encode($response);
?>
