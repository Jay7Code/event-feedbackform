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

$sql = "SELECT ef.*, e.event_name, e.event_date, e.event_time, e.location,
               a.attendee_name, a.email, a.contact_no
        FROM event_feedbacks ef
        JOIN attendees a ON ef.attendee_id = a.id
        JOIN events e ON a.event_id = e.id
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
        .glass-card { background: rgba(255,255,255,0.05); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.10); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
    </style>
</head>
<body class="font-sans min-h-screen text-white">
    <!-- Nav -->
    <nav class="border-b border-white/10 bg-black/20 backdrop-blur-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="font-serif text-xl font-bold text-white/90">Feedback #<?= $id ?></h1>
                <p class="text-white/40 text-xs">Submitted <?= date("M d, Y g:i A", strtotime($fb["created_at"])) ?></p>
            </div>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm text-gold-400/70 hover:text-gold-400 transition-colors font-medium">← Back</a>
                <a href="logout.php" class="text-sm text-white/40 hover:text-red-400 transition-colors">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        <!-- Event Overview -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Event Overview</h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Event Name</p>
                    <p class="text-white/80"><?= htmlspecialchars($fb["event_name"]) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Location</p>
                    <p class="text-white/80"><?= htmlspecialchars($fb["location"]) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Date</p>
                    <p class="text-white/80"><?= $fb["event_date"] ? date("F d, Y", strtotime($fb["event_date"])) : "—" ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Time</p>
                    <p class="text-white/80"><?= $fb["event_time"] ? date("g:i A", strtotime($fb["event_time"])) : "—" ?></p>
                </div>
            </div>
        </div>

        <!-- Ratings -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Ratings</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($ratingCategories as $key => $label): ?>
                        <div class="flex items-center justify-between py-2 border-b border-white/[0.04] last:border-b-0">
                            <span class="text-sm text-white/60"><?= $label ?></span>
                            <div class="flex items-center gap-2">
                                <?php $val = intval($fb[$key]); ?>
                                <div class="flex gap-0.5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="w-2.5 h-2.5 rounded-full <?= $i <= $val ? 'bg-gold-400' : 'bg-white/10' ?>"></span>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm font-bold <?= $val >= 4 ? 'text-green-400' : ($val >= 3 ? 'text-yellow-400' : 'text-red-400') ?>">
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
                <div class="mt-5 pt-4 border-t border-white/[0.08] flex items-center justify-between">
                    <span class="text-sm font-bold text-gold-400 uppercase tracking-wider">Overall Average</span>
                    <span class="text-2xl font-bold <?= $overallAvg >= 4 ? 'text-green-400' : ($overallAvg >= 3 ? 'text-yellow-400' : 'text-red-400') ?>">
                        <?= $overallAvg ?>/5
                    </span>
                </div>
            </div>
        </div>

        <!-- Open-ended Responses -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Responses</h2>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-2">Most Effective Aspects</p>
                    <p class="text-white/70 text-sm leading-relaxed"><?= !empty($fb["effective_aspects"]) ? nl2br(htmlspecialchars($fb["effective_aspects"])) : '<span class="text-white/30 italic">No response</span>' ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-2">Improvement Suggestions</p>
                    <p class="text-white/70 text-sm leading-relaxed"><?= !empty($fb["improvement_suggestions"]) ? nl2br(htmlspecialchars($fb["improvement_suggestions"])) : '<span class="text-white/30 italic">No response</span>' ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-2">Participate in Future Events?</p>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                        <?= $fb['participate_future'] === 'Yes' ? 'bg-green-500/10 text-green-400' : ($fb['participate_future'] === 'Maybe' ? 'bg-yellow-500/10 text-yellow-400' : 'bg-red-500/10 text-red-400') ?>">
                        <?= htmlspecialchars($fb["participate_future"]) ?>
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-2">Additional Feedback</p>
                    <p class="text-white/70 text-sm leading-relaxed"><?= !empty($fb["additional_feedback"]) ? nl2br(htmlspecialchars($fb["additional_feedback"])) : '<span class="text-white/30 italic">No response</span>' ?></p>
                </div>
            </div>
        </div>

        <!-- Attendee Info -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/[0.06]">
                <h2 class="font-serif text-lg text-white/80">Attendee Information</h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Group Name</p>
                    <p class="text-white/80"><?= htmlspecialchars($fb["attendee_name"]) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Gmail</p>
                    <p class="text-white/80"><?= htmlspecialchars($fb["email"]) ?></p>
                </div>
                <div>
                    <p class="text-xs text-gold-400 uppercase tracking-wider font-bold mb-1">Contact Number</p>
                    <p class="text-white/80"><?= !empty($fb["contact_no"]) ? htmlspecialchars($fb["contact_no"]) : '—' ?></p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
