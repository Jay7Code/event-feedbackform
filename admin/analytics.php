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
                        navy: { 800: '#1e2a6e', 900: '#1a2255', 950: '#0f1333' },
                        gold: { 400: '#C9A96E', 500: '#b5893a' }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"','serif'],
                        sans: ['"Inter"','sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #0f1333; }
        .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.10); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        @keyframes barGrow { from { width: 0; } }
        .bar-animate { animation: barGrow 0.8s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen text-white">
    <!-- Nav -->
    <nav class="border-b border-white/10 bg-black/20 backdrop-blur-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-xl font-bold text-white/90">Analytics</h1>
                <p class="text-white/40 text-xs">Event Feedback System</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm text-gold-400/70 hover:text-gold-400 transition-colors font-medium">← Dashboard</a>
                <a href="logout.php" class="text-sm text-white/40 hover:text-red-400 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <?php if ($totalCount == 0): ?>
            <div class="glass-card rounded-2xl p-12 text-center">
                <p class="text-white/30 text-lg">No feedback data yet. Analytics will appear once submissions are received.</p>
            </div>
        <?php else: ?>

        <!-- Top stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-gold-400"><?= $totalCount ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Total Responses</p>
            </div>
            <?php
            $grandAvg = 0; $cnt = 0;
            foreach (array_keys($ratingCategories) as $k) {
                if ($avgs["avg_$k"] > 0) { $grandAvg += $avgs["avg_$k"]; $cnt++; }
            }
            $grandAvg = $cnt > 0 ? round($grandAvg / $cnt, 1) : 0;
            ?>
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold <?= $grandAvg >= 4 ? 'text-green-400' : ($grandAvg >= 3 ? 'text-yellow-400' : 'text-red-400') ?>"><?= $grandAvg ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Grand Average</p>
            </div>
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-green-400"><?= $participation["Yes"] ?? 0 ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Would Return</p>
            </div>
            <div class="glass-card rounded-xl p-5 text-center">
                <?php
                $best = ""; $bestVal = 0;
                foreach (array_keys($ratingCategories) as $k) {
                    if (floatval($avgs["avg_$k"]) > $bestVal) { $bestVal = floatval($avgs["avg_$k"]); $best = $ratingCategories[$k]; }
                }
                ?>
                <p class="text-lg font-bold text-gold-400 leading-tight"><?= $best ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Top Category</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Category Averages -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06]">
                    <h2 class="font-serif text-lg text-white/80">Average by Category</h2>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <?php foreach ($ratingCategories as $key => $label): ?>
                        <?php $val = floatval($avgs["avg_$key"] ?? 0); $pct = ($val / 5) * 100; ?>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-white/60"><?= $label ?></span>
                                <span class="text-sm font-bold <?= $val >= 4 ? 'text-green-400' : ($val >= 3 ? 'text-yellow-400' : 'text-red-400') ?>"><?= number_format($val, 1) ?></span>
                            </div>
                            <div class="w-full h-2.5 bg-white/[0.06] rounded-full overflow-hidden">
                                <div class="h-full rounded-full bar-animate <?= $val >= 4 ? 'bg-green-400' : ($val >= 3 ? 'bg-yellow-400' : 'bg-red-400') ?>"
                                     style="width:<?= $pct ?>%; animation-delay:<?= array_search($key, array_keys($ratingCategories)) * 0.1 ?>s"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06]">
                    <h2 class="font-serif text-lg text-white/80">Rating Distribution</h2>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <?php $cnt = $distribution[$i]; $pct = ($cnt / $maxDist) * 100; ?>
                        <div class="flex items-center gap-3">
                            <span class="w-4 text-sm text-gold-400 font-bold text-right"><?= $i ?></span>
                            <div class="flex-1 h-6 bg-white/[0.04] rounded-lg overflow-hidden">
                                <div class="h-full rounded-lg bar-animate flex items-center px-2"
                                     style="width:<?= max($pct, 2) ?>%;background:linear-gradient(90deg,rgba(201,169,110,0.6),rgba(201,169,110,0.3));animation-delay:<?= (5 - $i) * 0.1 ?>s">
                                    <?php if ($cnt > 0): ?>
                                        <span class="text-xs font-bold text-white/80"><?= $cnt ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Participation pie (simple text breakdown) -->
                <div class="px-6 py-5 border-t border-white/[0.06]">
                    <h3 class="text-sm font-bold text-gold-400 uppercase tracking-wider mb-4">Future Participation</h3>
                    <div class="flex gap-4 justify-center">
                        <?php
                        $partColors = ['Yes' => 'bg-green-500', 'No' => 'bg-red-500', 'Maybe' => 'bg-yellow-500'];
                        foreach (['Yes', 'No', 'Maybe'] as $opt):
                            $cnt = $participation[$opt] ?? 0;
                            $pctPart = $totalCount > 0 ? round(($cnt / $totalCount) * 100) : 0;
                        ?>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-full <?= $partColors[$opt] ?>/10 flex items-center justify-center mx-auto mb-2">
                                    <span class="text-lg font-bold <?= str_replace('bg-', 'text-', $partColors[$opt]) ?>/80"><?= $pctPart ?>%</span>
                                </div>
                                <p class="text-xs text-white/40"><?= $opt ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent submissions -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Recent Submissions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Attendee</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gold-400 uppercase tracking-wider">Overall</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gold-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $r): ?>
                            <tr class="border-b border-white/[0.03] hover:bg-white/[0.03] transition-colors">
                                <td class="px-6 py-3 text-white/80"><?= htmlspecialchars($r["event_name"]) ?></td>
                                <td class="px-6 py-3 text-white/60"><?= htmlspecialchars($r["attendee_name"]) ?></td>
                                <td class="px-6 py-3 text-center">
                                    <?php $oe = intval($r["overall_experience"]); ?>
                                    <span class="font-bold <?= $oe >= 4 ? 'text-green-400' : ($oe >= 3 ? 'text-yellow-400' : 'text-red-400') ?>"><?= $oe ?>/5</span>
                                </td>
                                <td class="px-6 py-3 text-white/40 text-xs"><?= date("M d, Y g:i A", strtotime($r["created_at"])) ?></td>
                                <td class="px-6 py-3 text-center">
                                    <a href="view.php?id=<?= $r["id"] ?>" class="text-gold-400/70 hover:text-gold-400 text-xs font-semibold uppercase tracking-wider">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>
    </main>
</body>
</html>
