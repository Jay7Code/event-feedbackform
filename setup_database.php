<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * DATABASE SETUP SCRIPT
 * Run once to create the event_feedback_db and its tables.
 * ═══════════════════════════════════════════════════════════════
 */

$host = "localhost";
$user = "root";
$pass = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $user, $pass);
    $mysqli->set_charset("utf8mb4");

    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/event_feedback_db.sql');
    $mysqli->multi_query($sql);

    // Consume all results
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->next_result());

    echo "<h2 style='color:green;font-family:sans-serif;'>&#10003; Database 'event_feedback_db' and all tables created successfully!</h2>";
    echo "<p style='font-family:sans-serif;'><a href='index.php'>Go to Event Feedback Form &rarr;</a></p>";

} catch (mysqli_sql_exception $e) {
    echo "<h2 style='color:red;font-family:sans-serif;'>&#10007; Error:</h2>";
    echo "<pre style='font-family:monospace;color:#c00;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
