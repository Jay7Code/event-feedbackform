<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * DATABASE CONFIGURATION
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */

// ─── Database credentials ───
define("DB_HOST", "localhost");
define("DB_NAME", "event_feedback_db");
define("DB_USER", "root");
define("DB_PASS", "");

// ─── Admin credentials (fallback / initial setup) ───
define("ADMIN_USERNAME", "admin");
define("ADMIN_PASSWORD", "admin123");

// ─── Superadmin credentials ───
define("SUPERADMIN_USERNAME", "superadmin");
define("SUPERADMIN_PASSWORD", "superadmin123");

/**
 * Get a mysqli database connection.
 * Uses UTF-8 encoding.
 *
 * @return mysqli
 */
function getDBConnection()
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $mysqli->set_charset("utf8mb4");
        return $mysqli;
    } catch (mysqli_sql_exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Database connection failed. Please contact the administrator.");
    }
}
?>
