<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ANALYTICS PAGE
 * Average ratings per category with visual bar charts.
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
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
    <title>Analytics - Event Feedback</title>
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
        .bar-animate { animation: barGrow 0.8s ease-out forwards; }
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Analytics</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-bold">Performance Insights</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-sm text-pine-700/80 hover:text-antique-400 transition-colors font-medium">Dashboard</a>
                    <a href="analytics.php" class="text-sm text-antique-400 transition-colors font-bold border-b-2 border-antique-400 pb-1">Analytics</a>
                    <a href="reports.php" class="text-sm text-pine-700/80 hover:text-antique-400 transition-colors font-medium">Reports</a>
                </div>
                <span class="text-pine-700/20 hidden md:block">|</span>
                <a href="logout.php" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 relative z-10">
        <?php if ($totalCount == 0): ?>
            <div class="glass-card-premium rounded-3xl p-20 text-center">
                <div class="w-20 h-20 rounded-full bg-paper-100/50 flex items-center justify-center mx-auto mb-6 border border-paper-200">
                    <svg class="w-10 h-10 text-pine-700/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="font-serif text-2xl text-pine-800 mb-2">No Data Available</h3>
                <p class="text-pine-700/60 text-sm max-w-sm mx-auto">Analytics will automatically appear once you start receiving feedback submissions.</p>
            </div>
        <?php else: ?>

        <!-- Top stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-antique-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest mb-2">Total Responses</p>
                <p class="text-4xl font-serif font-bold text-pine-900"><?= $totalCount ?></p>
            </div>
            <?php
            $grandAvg = 0; $cnt = 0;
            foreach (array_keys($ratingCategories) as $k) {
                if ($avgs["avg_$k"] > 0) { $grandAvg += $avgs["avg_$k"]; $cnt++; }
            }
            $grandAvg = $cnt > 0 ? round($grandAvg / $cnt, 1) : 0;
            ?>
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-antique-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest mb-2">Grand Average</p>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-serif font-bold <?= $grandAvg >= 4 ? 'text-emerald-400' : ($grandAvg >= 3 ? 'text-yellow-400' : 'text-red-400') ?>"><?= $grandAvg ?></p>
                    <p class="text-pine-700/40 text-sm font-bold mb-1">/ 5.0</p>
                </div>
            </div>
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-emerald-400/60 uppercase tracking-widest mb-2">Would Return</p>
                <p class="text-4xl font-serif font-bold text-emerald-400"><?= $participation["Yes"] ?? 0 ?></p>
            </div>
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-antique-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <?php
                $best = ""; $bestVal = 0;
                foreach (array_keys($ratingCategories) as $k) {
                    if (floatval($avgs["avg_$k"]) > $bestVal) { $bestVal = floatval($avgs["avg_$k"]); $best = $ratingCategories[$k]; }
                }
                ?>
                <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest mb-2">Top Category</p>
                <p class="text-lg font-serif font-bold text-pine-900 truncate"><?= $best ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Category Averages -->
            <div class="glass-card-premium rounded-3xl overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30">
                    <h2 class="font-serif text-xl font-bold text-pine-900">Average by Category</h2>
                </div>
                <div class="px-8 py-8 space-y-6">
                    <?php foreach ($ratingCategories as $key => $label): ?>
                        <?php $val = floatval($avgs["avg_$key"] ?? 0); $pct = ($val / 5) * 100; ?>
                        <div>
                            <div class="flex justify-between mb-2 items-end">
                                <span class="text-xs font-bold text-pine-700 uppercase tracking-wider"><?= $label ?></span>
                                <span class="text-sm font-black <?= $val >= 4 ? 'text-emerald-400' : ($val >= 3 ? 'text-yellow-400' : 'text-red-400') ?>"><?= number_format($val, 1) ?></span>
                            </div>
                            <div class="w-full h-2 bg-paper-100/30 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bar-animate <?= $val >= 4 ? 'bg-emerald-400' : ($val >= 3 ? 'bg-yellow-400' : 'bg-red-400') ?>"
                                     style="width:<?= $pct ?>%; animation-delay:<?= array_search($key, array_keys($ratingCategories)) * 0.1 ?>s"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="glass-card-premium rounded-3xl overflow-hidden flex flex-col">
                <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30">
                    <h2 class="font-serif text-xl font-bold text-pine-900">Rating Distribution</h2>
                </div>
                <div class="px-8 py-8 flex-1 space-y-4">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <?php $cnt = $distribution[$i]; $pct = ($cnt / $maxDist) * 100; ?>
                        <div class="flex items-center gap-4">
                            <span class="w-4 text-xs text-antique-400 font-bold text-right"><?= $i ?></span>
                            <div class="flex-1 h-7 bg-paper-100/30 rounded-xl overflow-hidden relative">
                                <div class="h-full rounded-xl bar-animate flex items-center px-3"
                                     style="width:<?= max($pct, 4) ?>%;background:linear-gradient(90deg,rgba(201,169,110,0.4),rgba(201,169,110,0.1));animation-delay:<?= (5 - $i) * 0.1 ?>s">
                                    <?php if ($cnt > 0): ?>
                                        <span class="text-[0.65rem] font-black text-pine-900"><?= $cnt ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Participation breakdown -->
                <div class="px-8 py-6 border-t border-paper-200 bg-paper-100/30">
                    <h3 class="text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-6 text-center">Future Participation Intent</h3>
                    <div class="flex gap-8 justify-center">
                        <?php
                        $partColors = ['Yes' => 'emerald', 'No' => 'red', 'Maybe' => 'yellow'];
                        foreach (['Yes', 'No', 'Maybe'] as $opt):
                            $cnt = $participation[$opt] ?? 0;
                            $pctPart = $totalCount > 0 ? round(($cnt / $totalCount) * 100) : 0;
                            $color = $partColors[$opt];
                        ?>
                            <div class="text-center group">
                                <div class="w-16 h-16 rounded-2xl bg-<?= $color ?>-500/5 group-hover:bg-<?= $color ?>-500/10 border border-<?= $color ?>-500/10 group-hover:border-<?= $color ?>-500/20 flex flex-col items-center justify-center mx-auto mb-2 transition-all duration-300">
                                    <span class="text-lg font-black text-<?= $color ?>-400"><?= $pctPart ?><span class="text-[0.6rem] opacity-40">%</span></span>
                                </div>
                                <p class="text-[0.6rem] font-bold uppercase tracking-widest text-pine-700/60"><?= $opt ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent submissions -->
        <div class="glass-card-premium rounded-3xl overflow-hidden">
            <div class="px-8 py-5 border-b border-paper-200 bg-paper-100/30">
                <h2 class="font-serif text-xl font-bold text-pine-900">Recent Feedback Activity</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-paper-100/30">
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Event</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Attendee</th>
                            <th class="px-8 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Overall</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Timestamp</th>
                            <th class="px-8 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-paper-200">
                        <?php foreach ($recent as $r): ?>
                            <tr class="hover:bg-paper-100/30 transition-colors group">
                                <td class="px-8 py-4 text-pine-800 font-bold"><?= htmlspecialchars($r["event_name"]) ?></td>
                                <td class="px-8 py-4 text-pine-700/80"><?= htmlspecialchars($r["attendee_name"]) ?></td>
                                <td class="px-8 py-4 text-center">
                                    <?php $oe = intval($r["overall_experience"]); ?>
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[0.7rem] font-black border <?= $oe >= 4 ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($oe >= 3 ? 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20') ?>">
                                        <?= $oe ?>/5
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-pine-700/60 text-[0.7rem] font-medium"><?= date("M d, Y • g:i A", strtotime($r["created_at"])) ?></td>
                                <td class="px-8 py-4 text-center">
                                    <a href="view.php?id=<?= $r["id"] ?>" class="text-[0.65rem] font-bold text-antique-400/60 hover:text-antique-400 uppercase tracking-widest transition-colors">Details</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>
    </main>
    <script src="../js/auto_refresh.js"></script>
</body>
</html>

