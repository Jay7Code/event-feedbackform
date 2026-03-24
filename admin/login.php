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

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $mysqli = getDBConnection();
        $stmt = $mysqli->prepare("SELECT id, username, password, is_active FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($user['is_active'] == 1) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION["admin_logged_in"] = true;
                    $_SESSION["admin_username"] = $user['username'];
                    $_SESSION["admin_id"] = $user['id'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "This account has been deactivated. Please contact the super admin.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
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
                        pine: {
                            50: '#f2f7f4', 100: '#e1efe6', 200: '#c4dfcf', 300: '#9bc6b0',
                            400: '#6ba889', 500: '#488c6b', 600: '#356f53', 700: '#2b5843',
                            800: '#1b3a2a', 900: '#153A26',
                        },
                        antique: { 400: '#D4AF37', 500: '#C0A062', 600: '#9E824A' },
                        paper: { DEFAULT: '#FCFBFA', 100: '#F5F3ED', 200: '#EAE6DB' }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    },
                    boxShadow: {
                        'elegant': '0 10px 40px -10px rgba(21, 58, 38, 0.08)',
                    }
                }
            }
        }
    </script>

    
    <style>
        body {
            background-color: #FCFBFA;
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0C17.909 0 0 17.909 0 40c0 22.091 17.909 40 40 40 22.091 0 40-17.909 40-40C80 17.909 62.091 0 40 0zm0 3.2c20.324 0 36.8 16.476 36.8 36.8 0 20.324-16.476 36.8-36.8 36.8-20.324 0-36.8-16.476-36.8-36.8C3.2 19.676 19.676 3.2 40 3.2zm0 14.4c12.371 0 22.4 10.029 22.4 22.4 0 12.371-10.029 22.4-22.4 22.4-12.371 0-22.4-10.029-22.4-22.4 0-12.371 10.029-22.4 22.4-22.4zm0 3.2c-10.604 0-19.2 8.596-19.2 19.2 0 10.604 8.596 19.2 19.2 19.2 10.604 0 19.2-8.596 19.2-19.2 0-10.604-8.596-19.2-19.2-19.2z' fill='%23C0A062' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            color: #333;
        }
        .corner-decor {
            position: fixed; width: 250px; height: 250px; z-index: 0; pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1.5' stroke-opacity='0.3' d='M0,0 C100,0 200,100 200,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1' stroke-opacity='0.2' d='M0,20 C80,20 180,120 180,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='0.5' stroke-opacity='0.15' d='M0,40 C60,40 160,140 160,200'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
        }
        .corner-tl { top: 0; left: 0; transform: rotate(0deg); }
        .corner-tr { top: 0; right: 0; transform: rotate(90deg); }
        .corner-br { bottom: 0; right: 0; transform: rotate(180deg); }
        .corner-bl { bottom: 0; left: 0; transform: rotate(270deg); }

        .hotel-section {
            background: #FFFFFF;
            border: 1px solid #EAE6DB;
            border-top: 4px solid #153A26;
            box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05);
        }
        .nav-glass {
            background: rgba(252, 251, 250, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #EAE6DB;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        
        
        .glass-card-premium { background: #FFFFFF; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05); }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center text-pine-900 px-4 relative overflow-hidden">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    
    <div class="w-full max-w-md relative z-10 fade-up">
        <div class="text-center mb-10">
            <h1 class="font-serif text-4xl font-bold text-pine-900 mb-3 tracking-tight">Admin Portal</h1>
            <div class="flex items-center justify-center gap-3">
                <span class="w-8 h-px bg-antique-400/40"></span>
                <span class="text-antique-400/80 text-[0.65rem] font-semibold tracking-[0.3em] uppercase">Event Feedback System</span>
                <span class="w-8 h-px bg-antique-400/40"></span>
            </div>
        </div>
        <div class="glass-card-premium rounded-3xl p-8 md:p-10">
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-300 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?= $error ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-5 py-3.5 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 placeholder-white/20 focus:border-antique-400/50 focus:outline-none focus:ring-4 focus:ring-antique-400/5 transition-all duration-300"
                        placeholder="Enter username">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-[0.15em] mb-2.5 ml-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-5 py-3.5 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 placeholder-white/20 focus:border-antique-400/50 focus:outline-none focus:ring-4 focus:ring-antique-400/5 transition-all duration-300"
                        placeholder="Enter password">
                </div>
                <button type="submit"
                    class="w-full py-4 rounded-2xl font-bold text-sm uppercase tracking-widest transition-all duration-500 hover:shadow-2xl hover:shadow-antique-400/20 hover:-translate-y-0.5 active:scale-[0.98]"
                    style="background:#153A26;color:#ffffff">
                    Sign In
                </button>
            </form>
        </div>
        <p class="text-center mt-8 text-pine-700/60 text-xs font-medium uppercase tracking-[0.2em]">&copy; <?= date("Y") ?> Event Feedback System</p>
    </div>
</body>
</html>

