<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * SUPER ADMIN LOGIN PAGE
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
require_once __DIR__ . "/../config.php";

if (isset($_SESSION["superadmin_logged_in"]) && $_SESSION["superadmin_logged_in"] === true) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === SUPERADMIN_USERNAME && $password === SUPERADMIN_PASSWORD) {
        $_SESSION["superadmin_logged_in"] = true;
        $_SESSION["superadmin_username"] = $username;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid superadmin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Super Admin Login - Event Feedback</title>
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
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .fade-in { animation: fadeIn 0.8s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen flex items-center justify-center text-pine-900 p-6 relative overflow-hidden">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <div class="w-full max-w-[420px] relative z-10 fade-in">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-antique-400 to-antique-500 mb-6 shadow-xl shadow-antique-400/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h1 class="font-serif text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-b from-pine-900 to-pine-800/60 mb-2">Super Admin Space</h1>
            <p class="text-antique-400/50 text-[0.65rem] uppercase tracking-[0.3em] font-black">Authorized Personnel Only</p>
        </div>

        <div class="glass-card-premium rounded-[2.5rem] p-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-antique-400/40 to-transparent"></div>
            
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 rounded-2xl px-5 py-4 mb-8 flex items-center gap-3 text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><?= $error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-8">
                <div class="space-y-2">
                    <label class="block text-[0.65rem] font-black text-antique-400/80 uppercase tracking-widest ml-1">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-5 py-4 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 placeholder-white/20 focus:border-antique-400/40 focus:outline-none transition-all duration-300"
                        placeholder="superadmin_id">
                </div>
                <div class="space-y-2">
                    <label class="block text-[0.65rem] font-black text-antique-400/80 uppercase tracking-widest ml-1">Key Password</label>
                    <input type="password" name="password" required
                        class="w-full px-5 py-4 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 placeholder-white/20 focus:border-antique-400/40 focus:outline-none transition-all duration-300"
                        placeholder="••••••••">
                </div>
                
                <button type="submit"
                    class="group relative w-full py-4 rounded-2xl font-black text-[0.7rem] uppercase tracking-[0.2em] text-white transition-all duration-500 hover:scale-[1.02] active:scale-[0.98] overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-antique-400 to-antique-500 group-hover:from-antique-500 group-hover:to-antique-400 transition-all duration-500"></div>
                    <span class="relative">Initialize Session</span>
                </button>
            </form>
        </div>
        <p class="text-center mt-10 text-pine-700/40 text-xs font-medium">Event Feedback System &copy; <?= date('Y') ?></p>
    </div>
</body>
</html>
