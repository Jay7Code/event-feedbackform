<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * SUPER ADMIN ANALYTICS
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
if (!isset($_SESSION["superadmin_logged_in"]) || $_SESSION["superadmin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../config.php";
$mysqli = getDBConnection();

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

// Get totals
$totalResult = $mysqli->query("SELECT COUNT(*) as cnt FROM event_feedbacks");
$totalCount = $totalResult->fetch_assoc()["cnt"];

// Get averages
$avgCols = implode(", ", array_map(fn($k) => "ROUND(AVG(NULLIF($k, 0)), 2) as avg_$k", array_keys($ratingCategories)));
$avgResult = $mysqli->query("SELECT $avgCols FROM event_feedbacks");
$avgs = $avgResult->fetch_assoc();

// Participation breakdown
$partResult = $mysqli->query("SELECT participate_future, COUNT(*) as cnt FROM event_feedbacks GROUP BY participate_future");
$participation = [];
while ($row = $partResult->fetch_assoc()) {
    $participation[$row["participate_future"]] = intval($row["cnt"]);
}

// Rating distribution (1-5) for all categories combined
$distResult = $mysqli->query("
    SELECT val, COUNT(*) as cnt FROM (
        SELECT event_planning as val FROM event_feedbacks WHERE event_planning > 0
        UNION ALL SELECT speaker_effectiveness FROM event_feedbacks WHERE speaker_effectiveness > 0
        UNION ALL SELECT venue_setup FROM event_feedbacks WHERE venue_setup > 0
        UNION ALL SELECT time_management FROM event_feedbacks WHERE time_management > 0
        UNION ALL SELECT audience_participation FROM event_feedbacks WHERE audience_participation > 0
        UNION ALL SELECT overall_experience FROM event_feedbacks WHERE overall_experience > 0
        UNION ALL SELECT food_beverages FROM event_feedbacks WHERE food_beverages > 0
        UNION ALL SELECT technical_support FROM event_feedbacks WHERE technical_support > 0
    ) as all_ratings GROUP BY val ORDER BY val
");
$distribution = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
while ($row = $distResult->fetch_assoc()) {
    $distribution[intval($row["val"])] = intval($row["cnt"]);
}
$maxDist = max($distribution) ?: 1;

// Recent feedbacks (last 5)
$recentResult = $mysqli->query("
    SELECT ef.id, e.event_name, a.attendee_name, ef.created_at,
           ef.overall_experience
    FROM event_feedbacks ef
    JOIN attendees a ON ef.attendee_id = a.id
    JOIN events e ON a.event_id = e.id
    ORDER BY ef.created_at DESC LIMIT 5
");
$recent = $recentResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Analytics - Super Admin</title>
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
        
        @keyframes barGrow { from { width: 0; } }
        .bar-animate { animation: barGrow 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen text-pine-900 relative overflow-x-hidden">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <!-- Nav -->
    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Super Admin Panel</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-black">Performance Intelligence</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="index.php" class="text-xs font-black uppercase tracking-widest text-pine-700/70 hover:text-pine-900 transition-colors">Manage Admins</a>
                <a href="analytics.php" class="text-xs font-black uppercase tracking-widest text-antique-400 border-b-2 border-antique-400 pb-1">Analytics</a>
                <a href="reports.php" class="text-xs font-black uppercase tracking-widest text-pine-700/70 hover:text-pine-900 transition-colors">Reports</a>
                <span class="text-pine-700/20">|</span>
                <a href="logout.php" class="px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-[0.65rem] font-bold uppercase tracking-widest">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12 relative z-10">
        <?php if ($totalCount == 0): ?>
            <div class="glass-card-premium rounded-[2.5rem] p-20 text-center fade-up">
                <div class="w-16 h-16 rounded-2xl bg-paper-100/50 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-pine-700/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-serif text-2xl font-bold text-pine-800 mb-2">Insufficient Data Repository</h3>
                <p class="text-pine-700/60 text-sm max-w-sm mx-auto">Analytics engine is awaiting primary feedback submissions to initialize intelligence processing.</p>
            </div>
        <?php else: ?>

        <!-- Top Intelligence Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12 fade-up">
            <div class="glass-card-premium rounded-[2rem] p-8 relative overflow-hidden group hover:border-antique-400/40 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <p class="text-4xl font-serif font-bold text-antique-400"><?= $totalCount ?></p>
                <p class="text-[0.65rem] font-black text-pine-700/60 uppercase tracking-[0.2em] mt-3">Total Responses</p>
            </div>
            <?php
            $grandAvg = 0; $cnt = 0;
            foreach (array_keys($ratingCategories) as $k) {
                if ($avgs["avg_$k"] > 0) { $grandAvg += $avgs["avg_$k"]; $cnt++; }
            }
            $grandAvg = $cnt > 0 ? round($grandAvg / $cnt, 1) : 0;
            $avgColor = $grandAvg >= 4 ? 'emerald' : ($grandAvg >= 3 ? 'gold' : 'red');
            ?>
            <div class="glass-card-premium rounded-[2rem] p-8 relative overflow-hidden group hover:border-<?= $avgColor ?>-400/40 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-<?= $avgColor ?>-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <p class="text-4xl font-serif font-bold text-<?= $avgColor ?>-400"><?= number_format($grandAvg, 1) ?></p>
                <p class="text-[0.65rem] font-black text-pine-700/60 uppercase tracking-[0.2em] mt-3">Grand Average</p>
            </div>
            <div class="glass-card-premium rounded-[2rem] p-8 relative overflow-hidden group hover:border-emerald-400/40 transition-all duration-500">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-emerald-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <p class="text-4xl font-serif font-bold text-emerald-400"><?= $participation["Yes"] ?? 0 ?></p>
                <p class="text-[0.65rem] font-black text-pine-700/60 uppercase tracking-[0.2em] mt-3">Retention Intent</p>
            </div>
            <div class="glass-card-premium rounded-[2rem] p-8 relative overflow-hidden group hover:border-antique-400/40 transition-all duration-500">
                <?php
                $best = ""; $bestVal = 0;
                foreach (array_keys($ratingCategories) as $k) {
                    if (floatval($avgs["avg_$k"]) > $bestVal) { $bestVal = floatval($avgs["avg_$k"]); $best = $ratingCategories[$k]; }
                }
                ?>
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity text-antique-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <p class="text-xl font-bold text-pine-900 truncate pr-6 h-10 flex items-end"><?= $best ?></p>
                <p class="text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em] mt-3">Peak Performance</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Strategic Performance Breakdown -->
            <div class="glass-card-premium rounded-[2.5rem] overflow-hidden fade-up" style="animation-delay: 0.2s">
                <div class="px-10 py-8 border-b border-paper-200 bg-paper-100/30">
                    <h2 class="font-serif text-xl font-bold text-pine-900">Strategic Performance</h2>
                    <p class="text-pine-700/60 text-[0.65rem] uppercase tracking-widest mt-1 font-bold">Category Vector Analysis</p>
                </div>
                <div class="p-10 space-y-8">
                    <?php 
                    $idx = 0;
                    foreach ($ratingCategories as $key => $label): 
                        $val = floatval($avgs["avg_$key"] ?? 0); 
                        $pct = ($val / 5) * 100; 
                        $barColor = $val >= 4 ? 'emerald' : ($val >= 3 ? 'gold' : 'red');
                    ?>
                        <div class="group">
                            <div class="flex justify-between items-end mb-3">
                                <span class="text-xs font-bold text-pine-700 uppercase tracking-wider group-hover:text-pine-900 transition-colors"><?= $label ?></span>
                                <span class="text-sm font-black text-<?= $barColor ?>-400"><?= number_format($val, 1) ?> <span class="text-[0.6rem] opacity-40">/ 5.0</span></span>
                            </div>
                            <div class="w-full h-3 bg-paper-100/30 rounded-full p-0.5 border border-paper-200">
                                <div class="h-full rounded-full bar-animate bg-<?= $barColor ?>-400 shadow-[0_0_15px_rgba(var(--tw-color-<?= $barColor ?>-400),0.3)]"
                                     style="width:<?= $pct ?>%; animation-delay:<?= $idx++ * 0.1 ?>s"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Sentiment & Distribution -->
            <div class="space-y-8 fade-up" style="animation-delay: 0.3s">
                <div class="glass-card-premium rounded-[2.5rem] overflow-hidden">
                    <div class="px-10 py-8 border-b border-paper-200 bg-paper-100/30">
                        <h2 class="font-serif text-xl font-bold text-pine-900">Rating Distribution</h2>
                        <p class="text-pine-700/60 text-[0.65rem] uppercase tracking-widest mt-1 font-bold">Frequency Spectrum</p>
                    </div>
                    <div class="p-10 space-y-5">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <?php $cnt = $distribution[$i]; $pct = ($cnt / ($maxDist ?: 1)) * 100; ?>
                            <div class="flex items-center gap-5">
                                <div class="w-8 flex flex-col items-center">
                                    <span class="text-xs font-black text-antique-400"><?= $i ?></span>
                                    <svg class="w-3 h-3 text-antique-400/20 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                </div>
                                <div class="flex-1 h-8 bg-paper-100/30 rounded-xl overflow-hidden border border-paper-200">
                                    <div class="h-full rounded-xl bar-animate flex items-center justify-end px-4 group relative"
                                         style="width:<?= max($pct, 2) ?>%; background: linear-gradient(90deg, rgba(201,169,110,0.4), rgba(201,169,110,0.1)); animation-delay:<?= (5 - $i) * 0.1 ?>s">
                                         <?php if ($cnt > 0): ?>
                                             <span class="text-[0.6rem] font-black text-pine-700/80"><?= $cnt ?></span>
                                         <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Retention Metrics -->
                <div class="glass-card-premium rounded-[2.5rem] p-10">
                    <h3 class="text-[0.65rem] font-black text-antique-400/80 uppercase tracking-[0.2em] mb-8 text-center">Engagement Sentiment</h3>
                    <div class="flex gap-8 justify-around">
                        <?php
                        $metrics = [
                            'Yes' => ['color' => 'emerald', 'label' => 'Probable'],
                            'Maybe' => ['color' => 'gold', 'label' => 'Uncertain'],
                            'No' => ['color' => 'red', 'label' => 'Improbable']
                        ];
                        foreach ($metrics as $opt => $cfg):
                            $cnt = $participation[$opt] ?? 0;
                            $pctPart = $totalCount > 0 ? round(($cnt / $totalCount) * 100) : 0;
                            $col = $cfg['color'];
                        ?>
                            <div class="text-center group">
                                <div class="w-20 h-20 rounded-[2rem] bg-<?= $col ?>-500/5 flex flex-col items-center justify-center mx-auto mb-4 border border-<?= $col ?>-500/20 group-hover:scale-105 transition-transform duration-500">
                                    <span class="text-2xl font-serif font-bold text-<?= $col ?>-400"><?= $pctPart ?>%</span>
                                </div>
                                <p class="text-[0.6rem] font-black uppercase tracking-widest text-pine-700/60 group-hover:text-pine-700 transition-colors"><?= $cfg['label'] ?></p>
                                <p class="text-[0.5rem] font-bold text-<?= $col ?>-400/50 mt-1"><?= $cnt ?> Units</p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
