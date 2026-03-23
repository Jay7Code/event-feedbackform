<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ADMIN LOGIN PAGE
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
require_once __DIR__ . "/../config.php";

if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Login - Event Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1e2a6e', 900: '#1a2255', 950: '#0f1333' },
                        gold: { 400: '#C9A96E', 500: '#b5893a' }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0f1333; }
        .glass-card { background: rgba(255,255,255,0.06); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 12px 40px rgba(0,0,0,0.4); }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center text-white px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <h1 class="font-serif text-3xl font-bold text-white/90 mb-2">Admin Panel</h1>
            <p class="text-white/40 text-sm">Event Feedback System</p>
        </div>
        <div class="glass-card rounded-2xl p-8">
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/30 text-red-300 rounded-lg px-4 py-3 mb-6 text-sm">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gold-400 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-3 rounded-xl bg-white/[0.06] border border-white/10 text-white placeholder-white/30 focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/20 transition"
                        placeholder="Enter username">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gold-400 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl bg-white/[0.06] border border-white/10 text-white placeholder-white/30 focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/20 transition"
                        placeholder="Enter password">
                </div>
                <button type="submit"
                    class="w-full py-3 rounded-xl font-semibold text-sm uppercase tracking-wider transition-all duration-300 hover:shadow-lg hover:shadow-gold-400/20"
                    style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>
</html>
