<?php
/**
 * SUPER ADMIN API
 * Event Feedback System
 * Handles CRUD for admin_users
 */
session_start();
require_once __DIR__ . "/../../config.php";

header('Content-Type: application/json');

if (!isset($_SESSION["superadmin_logged_in"]) || $_SESSION["superadmin_logged_in"] !== true) {
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

$mysqli = getDBConnection();

// GET: Fetch all admin accounts
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $mysqli->query("SELECT id, username, full_name, email, is_active, created_at FROM ef_admin_users ORDER BY created_at DESC");
    $admins = [];
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
    echo json_encode(["success" => true, "admins" => $admins]);
    exit();
}

// POST: Actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        $username = trim($_POST["username"] ?? "");
        $password = trim($_POST["password"] ?? "");
        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if (empty($username) || empty($password) || empty($email)) {
            echo json_encode(["error" => "Username, password and email are required."]);
            exit();
        }

        if (strlen($password) < 6) {
            echo json_encode(["error" => "Password must be at least 6 characters."]);
            exit();
        }

        // Check if username exists
        $stmt = $mysqli->prepare("SELECT id FROM ef_admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            echo json_encode(["error" => "Username already exists."]);
            exit();
        }
        $stmt->close();

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO ef_admin_users (username, password, full_name, email, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->bind_param("ssss", $username, $hashed, $full_name, $email);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Admin account created successfully."]);
        } else {
            echo json_encode(["error" => "Failed to create admin."]);
        }
        $stmt->close();
        exit();
    }

    if ($action === "toggle_status") {
        $admin_id = intval($_POST["admin_id"] ?? 0);
        
        $stmt = $mysqli->prepare("SELECT is_active FROM ef_admin_users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $new_status = $row["is_active"] == 1 ? 0 : 1;
            $stmt->close();
            
            $stmt = $mysqli->prepare("UPDATE ef_admin_users SET is_active = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_status, $admin_id);
            if ($stmt->execute()) {
                $statusText = $new_status == 1 ? "Activated" : "Deactivated";
                echo json_encode(["success" => true, "message" => "Admin account $statusText."]);
            } else {
                echo json_encode(["error" => "Failed to update status."]);
            }
        } else {
            echo json_encode(["error" => "Admin not found."]);
        }
        $stmt->close();
        exit();
    }

    if ($action === "reset_password") {
        $admin_id = intval($_POST["admin_id"] ?? 0);
        $new_password = trim($_POST["new_password"] ?? "");

        if (empty($new_password) || strlen($new_password) < 6) {
            echo json_encode(["error" => "Password must be at least 6 characters."]);
            exit();
        }

        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE ef_admin_users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $admin_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Password reset successfully."]);
        } else {
            echo json_encode(["error" => "Failed to reset password."]);
        }
        $stmt->close();
        exit();
    }

    echo json_encode(["error" => "Invalid action."]);
    exit();
}
?>
