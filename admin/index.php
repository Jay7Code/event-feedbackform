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

$sql = "SELECT ef.id, e.event_name, e.event_date, l.location_name as location,
               a.attendee_name, a.email,
               ef.event_planning, ef.speaker_effectiveness, ef.venue_setup,
               ef.time_management, ef.audience_participation, ef.overall_experience,
               ef.food_beverages, ef.technical_support, ef.participate_future,
               ef.created_at
        FROM ef_event_feedbacks ef
        JOIN ef_attendees a ON ef.attendee_id = a.id
        JOIN ef_events e ON a.event_id = e.id
        LEFT JOIN ef_locations l ON e.location_id = l.id
        $whereSQL
        ORDER BY ef.created_at DESC";

$stmt = $mysqli->prepare($sql);
if ($types !== "") {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);

// ─── CSV Export ───
if (isset($_GET["export_csv"]) && $_GET["export_csv"] == 1) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=event_feedback_export_' . date('Ymd_His') . '.csv');
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, [
        'ID', 'Event Name', 'Event Date', 'Location', 'Attendee Name', 'Attendee Email',
        'Planning', 'Speaker', 'Venue', 'Time Mgmt', 'Participation', 'Overall', 'Food', 'Tech',
        'Future', 'Submitted At'
    ]);
    
    foreach ($feedbacks as $fb) {
        fputcsv($output, [
            $fb['id'], $fb['event_name'], $fb['event_date'], $fb['location'],
            $fb['attendee_name'], $fb['email'],
            $fb['event_planning'], $fb['speaker_effectiveness'], $fb['venue_setup'],
            $fb['time_management'], $fb['audience_participation'], $fb['overall_experience'],
            $fb['food_beverages'], $fb['technical_support'],
            $fb['participate_future'], $fb['created_at']
        ]);
    }
    fclose($output);
    exit();
}

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
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Admin Portal</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-bold">Feedback Dashboard</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden md:flex items-center gap-6">
                    <a href="index.php" class="text-sm text-antique-400 transition-colors font-bold border-b-2 border-antique-400 pb-1">Dashboard</a>
                    <a href="analytics.php" class="text-sm text-pine-700/80 hover:text-antique-400 transition-colors font-medium">Analytics</a>
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
        <!-- Filter bar -->
        <div class="glass-card-premium rounded-3xl p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Search Submissions</label>
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Event, attendee, or email..."
                            class="w-full pl-11 pr-4 py-3 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 text-sm placeholder-white/20 focus:border-antique-400/40 focus:outline-none focus:ring-4 focus:ring-antique-400/5 transition-all">
                        <svg class="w-5 h-5 text-pine-700/40 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Date From</label>
                    <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>"
                        class="w-full px-4 py-3 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 text-sm focus:border-antique-400/40 focus:outline-none focus:ring-4 focus:ring-antique-400/5 transition-all">
                </div>
                <div>
                    <label class="block text-[0.65rem] font-bold text-antique-400 uppercase tracking-widest mb-2.5 ml-1">Date To</label>
                    <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>"
                        class="w-full px-4 py-3 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-900 text-sm focus:border-antique-400/40 focus:outline-none focus:ring-4 focus:ring-antique-400/5 transition-all">
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all duration-300 hover:shadow-lg hover:shadow-antique-400/20 active:scale-95"
                        style="background:#153A26;color:#ffffff">
                        Apply Filter
                    </button>
                    <?php
                    $exportQuery = $_GET;
                    $exportQuery['export_csv'] = 1;
                    $exportUrl = '?' . http_build_query($exportQuery);
                    ?>
                    <a href="<?= htmlspecialchars($exportUrl) ?>"
                        class="px-5 py-3.5 rounded-2xl bg-antique-400/10 border border-antique-400/20 text-antique-400 text-xs font-bold uppercase tracking-widest hover:bg-antique-400 hover:text-white transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Export CSV
                    </a>
                    <a href="index.php"
                        class="px-5 py-3.5 rounded-2xl bg-paper-100/30 border border-paper-200 text-pine-700 text-xs font-bold uppercase tracking-widest hover:text-pine-900 hover:bg-paper-100/30 transition-all flex items-center justify-center">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Stats row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-antique-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest mb-2">Total Responses</p>
                <p class="text-4xl font-serif font-bold text-pine-900"><?= count($feedbacks) ?></p>
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
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-antique-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest mb-2">Avg Satisfaction</p>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-serif font-bold text-pine-900"><?= $avgAll ?></p>
                    <p class="text-antique-400/40 text-sm font-bold mb-1">/ 5.0</p>
                </div>
            </div>
            <?php
            $excellentCount = count(array_filter($feedbacks, function($f) { $avg = avgRating($f); return $avg >= 4; }));
            $goodCount = count(array_filter($feedbacks, function($f) { $avg = avgRating($f); return $avg >= 3 && $avg < 4; }));
            $poorCount = count(array_filter($feedbacks, function($f) { $avg = avgRating($f); return $avg > 0 && $avg < 3; }));
            ?>
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-emerald-400/60 uppercase tracking-widest mb-2">Excellent (4-5)</p>
                <p class="text-4xl font-serif font-bold text-emerald-400"><?= $excellentCount ?></p>
            </div>
            <div class="glass-card-premium rounded-2xl p-6 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-red-400/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150 duration-700"></div>
                <p class="text-[0.65rem] font-bold text-red-400/60 uppercase tracking-widest mb-2">Poor (1-2)</p>
                <p class="text-4xl font-serif font-bold text-red-400"><?= $poorCount ?></p>
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
        <div class="glass-card-premium rounded-3xl overflow-hidden mb-12">
            <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-between bg-paper-100/30">
                <h2 class="font-serif text-xl font-bold text-pine-900">Submissions</h2>
                <span class="px-3 py-1 rounded-full bg-antique-400/10 border border-antique-400/20 text-antique-400 text-[0.65rem] font-bold uppercase tracking-widest">
                    showing latest
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-paper-100/30">
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">#</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Event Detail</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Attendee</th>
                            <th class="px-8 py-4 text-left text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Group Date</th>
                            <th class="px-8 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Avg Rating</th>
                            <th class="px-8 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Future</th>
                            <th class="px-8 py-4 text-center text-[0.65rem] font-bold text-antique-400/60 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-paper-200">
                        <?php if (empty($paginatedFeedbacks)): ?>
                            <tr><td colspan="7" class="px-8 py-20 text-center text-pine-700/40 font-medium italic">No feedback submissions found matching your criteria.</td></tr>
                        <?php else: ?>
                            <?php foreach ($paginatedFeedbacks as $i => $fb): ?>
                                <tr class="hover:bg-paper-100/30 transition-colors group">
                                    <td class="px-8 py-5 text-pine-700/60 font-mono text-xs"><?= str_pad($offset + $i + 1, 2, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-8 py-5">
                                        <p class="text-pine-900 font-bold tracking-tight mb-0.5"><?= htmlspecialchars($fb["event_name"]) ?></p>
                                        <div class="flex items-center gap-1.5 text-pine-700/60 text-[0.7rem] uppercase tracking-wider font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <?= htmlspecialchars($fb["location"]) ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-pine-800 font-semibold"><?= htmlspecialchars($fb["attendee_name"]) ?></p>
                                        <p class="text-pine-700/60 text-xs italic"><?= htmlspecialchars($fb["email"]) ?></p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-pine-700 font-medium"><?= $fb["event_date"] ? date("M d, Y", strtotime($fb["event_date"])) : "—" ?></div>
                                        <p class="text-[0.6rem] uppercase tracking-widest text-pine-700/40 font-bold">Submission Date</p>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <?php $avg = avgRating($fb); ?>
                                        <div class="inline-flex items-center justify-center min-w-[3.5rem] py-1 rounded-full text-[0.7rem] font-black tracking-tighter
                                            <?= $avg >= 4 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($avg >= 3 ? 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20') ?>">
                                            <?= number_format($avg, 1) ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="text-[0.65rem] font-bold uppercase tracking-[0.1em]
                                            <?= $fb['participate_future'] === 'Yes' ? 'text-emerald-400' : ($fb['participate_future'] === 'Maybe' ? 'text-yellow-400' : 'text-red-400') ?>">
                                            <?= htmlspecialchars($fb["participate_future"]) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <a href="view.php?id=<?= $fb["id"] ?>"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-antique-400/5 border border-antique-400/20 text-antique-400 text-xs font-bold uppercase tracking-widest hover:bg-antique-400 hover:text-white transition-all duration-300">
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
                <div class="px-8 py-5 bg-paper-100/30 border-t border-paper-200 flex items-center justify-between">
                    <p class="text-[0.65rem] font-bold text-pine-700/60 uppercase tracking-widest">Showing <?= $offset + 1 ?> — <?= min($offset + $limit, $totalRecords) ?> of <?= $totalRecords ?> records</p>
                    <div class="flex gap-3">
                        <?php
                        $qs = $_GET;
                        $qs['page'] = $page - 1;
                        $prevUrl = '?' . http_build_query($qs);
                        $qs['page'] = $page + 1;
                        $nextUrl = '?' . http_build_query($qs);
                        ?>
                        <?php if ($page > 1): ?>
                            <a href="<?= htmlspecialchars($prevUrl) ?>" class="px-4 py-2 rounded-xl bg-paper-100/50 hover:bg-paper-100/50 text-pine-700 text-xs font-bold uppercase tracking-widest transition-all border border-paper-200">Prev</a>
                        <?php else: ?>
                            <span class="px-4 py-2 rounded-xl bg-paper-100/50 text-pine-700/40 text-xs font-bold uppercase tracking-widest border border-paper-200 cursor-not-allowed">Prev</span>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= htmlspecialchars($nextUrl) ?>" class="px-4 py-2 rounded-xl bg-antique-400/10 hover:bg-antique-400/20 text-antique-400 border border-antique-400/20 text-xs font-bold uppercase tracking-widest transition-all">Next</a>
                        <?php else: ?>
                            <span class="px-4 py-2 rounded-xl bg-paper-100/50 text-pine-700/40 text-xs font-bold uppercase tracking-widest border border-paper-200 cursor-not-allowed">Next</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

    </main>
    <script src="../js/auto_refresh.js"></script>
</body>
</html>
