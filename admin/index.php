<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ADMIN DASHBOARD
 * Lists all event feedback submissions with search/filter.
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../config.php";
$mysqli = getDBConnection();

// ─── Filtering ───
$search = trim($_GET["search"] ?? "");
$dateFrom = $_GET["date_from"] ?? "";
$dateTo = $_GET["date_to"] ?? "";

$where = [];
$params = [];
$types = "";

if ($search !== "") {
    $where[] = "(e.event_name LIKE ? OR a.attendee_name LIKE ? OR a.email LIKE ?)";
    $s = "%$search%";
    $params[] = &$s;
    $params[] = &$s;
    $params[] = &$s;
    $types .= "sss";
}
if ($dateFrom !== "") {
    $where[] = "e.event_date >= ?";
    $params[] = &$dateFrom;
    $types .= "s";
}
if ($dateTo !== "") {
    $where[] = "e.event_date <= ?";
    $params[] = &$dateTo;
    $types .= "s";
}

$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT ef.id, e.event_name, e.event_date, e.location,
               a.attendee_name, a.email,
               ef.event_planning, ef.speaker_effectiveness, ef.venue_setup,
               ef.time_management, ef.audience_participation, ef.overall_experience,
               ef.food_beverages, ef.technical_support, ef.participate_future,
               ef.created_at
        FROM event_feedbacks ef
        JOIN attendees a ON ef.attendee_id = a.id
        JOIN events e ON a.event_id = e.id
        $whereSQL
        ORDER BY ef.created_at DESC";

$stmt = $mysqli->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);

// Calculate average rating for each row
function avgRating($row) {
    $cols = ['event_planning','speaker_effectiveness','venue_setup','time_management',
             'audience_participation','overall_experience','food_beverages','technical_support'];
    $sum = 0; $count = 0;
    foreach ($cols as $c) {
        if (isset($row[$c]) && $row[$c] > 0) { $sum += $row[$c]; $count++; }
    }
    return $count > 0 ? round($sum / $count, 1) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Admin Dashboard - Event Feedback</title>
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
    </style>
</head>
<body class="font-sans min-h-screen text-white">
    <!-- Nav -->
    <nav class="border-b border-white/10 bg-black/20 backdrop-blur-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-xl font-bold text-white/90">Event Feedback</h1>
                <p class="text-white/40 text-xs">Admin Dashboard</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm text-gold-400 hover:text-gold-300 transition-colors font-medium border-b border-gold-400 pb-1">Dashboard</a>
                <a href="analytics.php" class="text-sm text-white/50 hover:text-white transition-colors">Analytics</a>
                <a href="reports.php" class="text-sm text-white/50 hover:text-white transition-colors">Reports</a>
                <span class="text-white/20">|</span>
                <a href="logout.php" class="text-sm text-red-400 hover:text-red-300 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- Filter bar -->
        <div class="glass-card rounded-2xl p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gold-400 uppercase tracking-wider mb-2">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="Event name, attendee, email..."
                        class="w-full px-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/10 text-white text-sm placeholder-white/30 focus:border-gold-400 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gold-400 uppercase tracking-wider mb-2">Date From</label>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                        class="w-full px-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/10 text-white text-sm focus:border-gold-400 focus:outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gold-400 uppercase tracking-wider mb-2">Date To</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                        class="w-full px-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/10 text-white text-sm focus:border-gold-400 focus:outline-none transition">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 py-2.5 rounded-xl font-semibold text-sm uppercase tracking-wider transition-all"
                        style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">
                        Filter
                    </button>
                    <a href="index.php"
                        class="px-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/10 text-white/60 text-sm hover:text-white hover:border-white/20 transition flex items-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-gold-400"><?= count($feedbacks) ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Total Responses</p>
            </div>
            <?php
            $avgAll = 0;
            if (count($feedbacks) > 0) {
                $sum = array_sum(array_map('avgRating', $feedbacks));
                $avgAll = round($sum / count($feedbacks), 1);
            }

            $yesCount = count(array_filter($feedbacks, fn($f) => $f['participate_future'] === 'Yes'));
            $maybeCount = count(array_filter($feedbacks, fn($f) => $f['participate_future'] === 'Maybe'));
            ?>
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-gold-400"><?= $avgAll ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Avg Rating</p>
            </div>
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-green-400"><?= $yesCount ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Would Return</p>
            </div>
            <div class="glass-card rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-yellow-400"><?= $maybeCount ?></p>
                <p class="text-xs text-white/40 uppercase tracking-wider mt-1">Maybe Return</p>
            </div>
        </div>

        <?php
        $limit = 15;
        $totalRecords = count($feedbacks);
        $totalPages = ceil($totalRecords / $limit);
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages;
        $offset = ($page - 1) * $limit;
        $paginatedFeedbacks = array_slice($feedbacks, $offset, $limit);
        ?>
        <!-- Feedback table -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Submissions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/[0.06]">
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Event</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Group</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gold-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gold-400 uppercase tracking-wider">Avg Rating</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gold-400 uppercase tracking-wider">Future</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gold-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paginatedFeedbacks)): ?>
                            <tr><td colspan="7" class="px-6 py-12 text-center text-white/30">No feedback submissions found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($paginatedFeedbacks as $i => $fb): ?>
                                <tr class="border-b border-white/[0.03] hover:bg-white/[0.03] transition-colors">
                                    <td class="px-6 py-4 text-white/40"><?= $offset + $i + 1 ?></td>
                                    <td class="px-6 py-4">
                                        <p class="text-white/80 font-medium"><?= htmlspecialchars($fb["event_name"]) ?></p>
                                        <p class="text-white/30 text-xs"><?= htmlspecialchars($fb["location"]) ?></p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-white/80"><?= htmlspecialchars($fb["attendee_name"]) ?></p>
                                        <p class="text-white/30 text-xs"><?= htmlspecialchars($fb["email"]) ?></p>
                                    </td>
                                    <td class="px-6 py-4 text-white/60"><?= $fb["event_date"] ? date("M d, Y", strtotime($fb["event_date"])) : "—" ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php $avg = avgRating($fb); ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold
                                            <?= $avg >= 4 ? 'bg-green-500/10 text-green-400' : ($avg >= 3 ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') ?>">
                                            <?= $avg ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-xs font-semibold
                                            <?= $fb['participate_future'] === 'Yes' ? 'text-green-400' : ($fb['participate_future'] === 'Maybe' ? 'text-yellow-400' : 'text-red-400') ?>">
                                            <?= htmlspecialchars($fb["participate_future"]) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="view.php?id=<?= $fb["id"] ?>"
                                            class="text-gold-400/70 hover:text-gold-400 text-xs font-semibold uppercase tracking-wider transition-colors">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-white/[0.06] flex items-center justify-between">
                    <p class="text-xs text-white/40">Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalRecords) ?> of <?= $totalRecords ?> submissions</p>
                    <div class="flex gap-2">
                        <?php
                        $qs = $_GET;
                        $qs['page'] = $page - 1;
                        $prevUrl = '?' . http_build_query($qs);
                        $qs['page'] = $page + 1;
                        $nextUrl = '?' . http_build_query($qs);
                        ?>
                        <?php if ($page > 1): ?>
                            <a href="<?= htmlspecialchars($prevUrl) ?>" class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white/70 text-xs font-semibold transition-colors border border-white/10">Prev</a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 rounded-lg bg-white/5 text-white/30 text-xs font-semibold border border-white/5 cursor-not-allowed">Prev</span>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= htmlspecialchars($nextUrl) ?>" class="px-3 py-1.5 rounded-lg bg-gold-400/10 hover:bg-gold-400/20 text-gold-400 border border-gold-400/20 text-xs font-semibold transition-colors">Next</a>
                        <?php else: ?>
                            <span class="px-3 py-1.5 rounded-lg bg-white/5 text-white/30 text-xs font-semibold border border-white/5 cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
