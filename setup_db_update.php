<?php
require_once "config.php";
$mysqli = getDBConnection();

$sql1 = "ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS full_name varchar(255) DEFAULT NULL;";
$sql2 = "ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS is_active tinyint(1) NOT NULL DEFAULT 1;";

$mysqli->query($sql1);
$mysqli->query($sql2);

// Insert default admin if table is empty
$result = $mysqli->query("SELECT COUNT(*) as cnt FROM admin_users");
$row = $result->fetch_assoc();
if ($row['cnt'] == 0) {
    $hashed = password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO admin_users (username, password, full_name, is_active) VALUES (?, ?, 'Default Admin', 1)");
    $uname = ADMIN_USERNAME;
    $stmt->bind_param("ss", $uname, $hashed);
    $stmt->execute();
    echo "Default admin inserted. ";
}

echo "Database updated successfully.";
?>
