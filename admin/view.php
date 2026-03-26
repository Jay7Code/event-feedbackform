<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * VIEW SINGLE FEEDBACK SUBMISSION
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../config.php";
$mysqli = getDBConnection();

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) { header("Location: index.php"); exit(); }

$sql = "SELECT ef.*, e.event_name, e.event_date, e.event_time, l.location_name as location,
               a.attendee_name, a.email, a.contact_no
        FROM ef_event_feedbacks ef
        JOIN ef_attendees a ON ef.attendee_id = a.id
        JOIN ef_events e ON a.event_id = e.id
        LEFT JOIN ef_locations l ON e.location_id = l.id
        WHERE ef.id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$fb = $stmt->get_result()->fetch_assoc();

if (!$fb) { header("Location: index.php"); exit(); }

$ratingCategories = [
    "event_planning" => "Event Planning",
    "speaker_effectiveness" => "Speaker Effectiveness",
    "venue_setup" => "Venue & Setup",
    "time_management" => "Time Management",
    "audience_participation" => "Audience Participation",
    "overall_experience" => "Overall Experience",
    "food_beverages" => "Food & Beverages",
    "technical_support" => "Technical Support",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>View Feedback #<?= $id ?> - Event Feedback</title>
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
<body class="font-sans min-h-screen text-pine-900 relative">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <!-- Nav -->
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Feedback #<?= $id ?></h1>
                    <p class="text-antique-400/60 text-[0.6rem] uppercase tracking-widest font-bold">Submitted <?= date("M d, Y g:i A", strtotime($fb["created_at"])) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-xs font-bold uppercase tracking-widest text-antique-400/70 hover:text-antique-400 transition-colors">← Back</a>
                <span class="text-pine-700/20">|</span>
                <a href="logout.php" class="px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-[0.65rem] font-bold uppercase tracking-widest">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 space-y-8 relative z-10">
        <!-- Event Overview -->
        <div class="glass-card-premium rounded-3xl overflow-hidden fade-up" style="animation-delay: 0.1s">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30 flex items-center justify-between">
                <h2 class="font-serif text-xl font-bold text-pine-900">Event Context</h2>
                <span class="px-3 py-1 rounded-full bg-antique-400/10 border border-antique-400/20 text-antique-400 text-[0.65rem] font-black uppercase tracking-widest">Metadata</span>
            </div>
            <div class="px-8 py-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-1">
                    <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Event Session</p>
                    <p class="text-pine-900 text-lg font-medium"><?= htmlspecialchars($fb["event_name"]) ?></p>
                </div>
                <div class="space-y-1">
                    <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Location</p>
                    <p class="text-pine-900 text-lg font-medium"><?= htmlspecialchars($fb["location"]) ?></p>
                </div>
                <div class="space-y-1">
                    <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Date</p>
                    <p class="text-pine-900 text-lg font-medium"><?= $fb["event_date"] ? date("F d, Y", strtotime($fb["event_date"])) : "—" ?></p>
                </div>
                <div class="space-y-1">
                    <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Time</p>
                    <p class="text-pine-900 text-lg font-medium"><?= $fb["event_time"] ? date("g:i A", strtotime($fb["event_time"])) : "—" ?></p>
                </div>
            </div>
        </div>

        <!-- Ratings -->
        <div class="glass-card-premium rounded-3xl overflow-hidden fade-up" style="animation-delay: 0.2s">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30 flex items-center justify-between">
                <h2 class="font-serif text-xl font-bold text-pine-900">Experience Ratings</h2>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-pine-700/70 text-[0.65rem] font-bold uppercase tracking-widest">Score Data</span>
                </div>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                    <?php foreach ($ratingCategories as $key => $label): ?>
                        <div class="flex items-center justify-between py-3 border-b border-paper-200 last:border-b-0 hover:bg-paper-100/30 px-2 rounded-lg transition-colors group">
                            <span class="text-sm text-pine-700 group-hover:text-pine-900 transition-colors"><?= $label ?></span>
                            <div class="flex items-center gap-4">
                                <div class="flex gap-1">
                                    <?php $val = intval($fb[$key]); ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <div class="w-2.5 h-2.5 rounded-full <?= $i <= $val ? 'bg-antique-400 shadow-[0_0_8px_rgba(201,169,110,0.4)]' : 'bg-paper-100/50' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm font-black tracking-tight <?= $val >= 4 ? 'text-emerald-400' : ($val >= 3 ? 'text-yellow-400' : 'text-red-400') ?>">
                                    <?= $val ?>/5
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php
                $ratingVals = array_map(fn($k) => intval($fb[$k]), array_keys($ratingCategories));
                $rated = array_filter($ratingVals, fn($v) => $v > 0);
                $overallAvg = count($rated) > 0 ? round(array_sum($rated) / count($rated), 1) : 0;
                ?>
                <div class="mt-8 pt-8 border-t border-paper-200 flex items-center justify-between px-2">
                    <div>
                        <p class="text-[0.65rem] font-black text-antique-400 uppercase tracking-[0.2em] mb-1">Weighted Overall Performance</p>
                        <p class="text-3xl font-serif font-bold text-pine-900">Attendee Satisfaction</p>
                    </div>
                    <div class="text-right">
                        <span class="text-5xl font-serif font-black <?= $overallAvg >= 4 ? 'text-emerald-400' : ($overallAvg >= 3 ? 'text-yellow-400' : 'text-red-400') ?>">
                            <?= $overallAvg ?>
                        </span>
                        <span class="text-pine-700/40 text-xl font-bold ml-1">/ 5.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Qualitative Insights -->
        <div class="glass-card-premium rounded-3xl overflow-hidden fade-up" style="animation-delay: 0.3s">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30">
                <h2 class="font-serif text-xl font-bold text-pine-900">Qualitative Insights</h2>
            </div>
            <div class="p-8 space-y-10">
                <div class="relative pl-6 border-l-2 border-antique-400/20">
                    <p class="text-[0.65rem] font-black text-antique-400 uppercase tracking-widest mb-3">Effective Aspects</p>
                    <div class="text-pine-700 text-sm leading-relaxed italic bg-paper-100/30 p-5 rounded-2xl border border-paper-200">
                        <?= !empty($fb["effective_aspects"]) ? nl2br(htmlspecialchars($fb["effective_aspects"])) : '<span class="opacity-30">No response provided.</span>' ?>
                    </div>
                </div>
                <div class="relative pl-6 border-l-2 border-antique-400/20">
                    <p class="text-[0.65rem] font-black text-antique-400 uppercase tracking-widest mb-3">Suggested Improvements</p>
                    <div class="text-pine-700 text-sm leading-relaxed italic bg-paper-100/30 p-5 rounded-2xl border border-paper-200">
                        <?= !empty($fb["improvement_suggestions"]) ? nl2br(htmlspecialchars($fb["improvement_suggestions"])) : '<span class="opacity-30">No response provided.</span>' ?>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div class="bg-paper-100/30 p-6 rounded-2xl border border-paper-200">
                        <p class="text-[0.65rem] font-black text-antique-400 uppercase tracking-widest mb-3">Future Participation Intent</p>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full <?= $fb['participate_future'] === 'Yes' ? 'bg-emerald-400' : ($fb['participate_future'] === 'Maybe' ? 'bg-yellow-400' : 'bg-red-400') ?>"></span>
                            <span class="text-lg font-bold text-pine-900"><?= htmlspecialchars($fb["participate_future"]) ?></span>
                        </div>
                    </div>
                    <div class="bg-paper-100/30 p-6 rounded-2xl border border-paper-200">
                        <p class="text-[0.65rem] font-black text-antique-400 uppercase tracking-widest mb-3">Additional Comments</p>
                        <p class="text-pine-700 text-sm truncate"><?= !empty($fb["additional_feedback"]) ? htmlspecialchars($fb["additional_feedback"]) : '—' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendee Info -->
        <div class="glass-card-premium rounded-3xl overflow-hidden fade-up" style="animation-delay: 0.4s">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30">
                <h2 class="font-serif text-xl font-bold text-pine-900">Attendee Profile</h2>
            </div>
            <div class="px-8 py-10 grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-paper-100/50 border border-paper-200 flex items-center justify-center text-antique-400/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black text-antique-400/40 uppercase tracking-widest mb-0.5">Full Name</p>
                        <p class="text-pine-900 font-bold"><?= htmlspecialchars($fb["attendee_name"]) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-paper-100/50 border border-paper-200 flex items-center justify-center text-antique-400/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black text-antique-400/40 uppercase tracking-widest mb-0.5">Email Interface</p>
                        <p class="text-pine-900 font-bold"><?= htmlspecialchars($fb["email"]) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-paper-100/50 border border-paper-200 flex items-center justify-center text-antique-400/40">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black text-antique-400/40 uppercase tracking-widest mb-0.5">Phone Number</p>
                        <p class="text-pine-900 font-bold"><?= !empty($fb["contact_no"]) ? htmlspecialchars($fb["contact_no"]) : '—' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="../js/auto_refresh.js"></script>
</body>
</html>
