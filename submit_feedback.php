<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * EVENT FEEDBACK SUBMISSION SCRIPT
 * Processes POST requests from the event evaluation form.
 * Inserts into events → attendees → event_feedbacks tables.
 * ═══════════════════════════════════════════════════════════════
 */
require_once __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$mysqli = getDBConnection();

$data = [
    // Event overview
    "event_name"    => htmlspecialchars(trim($_POST["event_name"] ?? "")),
    "event_date"    => !empty($_POST["event_date"]) ? $_POST["event_date"] : null,
    "event_time"    => !empty($_POST["event_time"]) ? $_POST["event_time"] : null,
    "location"      => ($_POST["location"] ?? "") === "Others"
                        ? htmlspecialchars(trim($_POST["other_location_text"] ?? ""))
                        : htmlspecialchars(trim($_POST["location"] ?? "")),

    // Ratings (1-5)
    "event_planning"         => intval($_POST["event_planning"] ?? 0),
    "speaker_effectiveness"  => intval($_POST["speaker_effectiveness"] ?? 0),
    "venue_setup"            => intval($_POST["venue_setup"] ?? 0),
    "time_management"        => intval($_POST["time_management"] ?? 0),
    "audience_participation" => intval($_POST["audience_participation"] ?? 0),
    "overall_experience"     => intval($_POST["overall_experience"] ?? 0),
    "food_beverages"         => intval($_POST["food_beverages"] ?? 0),
    "technical_support"      => intval($_POST["technical_support"] ?? 0),

    // Open-ended
    "effective_aspects"       => htmlspecialchars(trim($_POST["effective_aspects"] ?? "")),
    "improvement_suggestions" => htmlspecialchars(trim($_POST["improvement_suggestions"] ?? "")),
    "participate_future"      => htmlspecialchars(trim($_POST["participate_future"] ?? "")),
    "additional_feedback"     => htmlspecialchars(trim($_POST["additional_feedback"] ?? "")),

    // Attendee info
    "attendee_name" => htmlspecialchars(trim($_POST["attendee_name"] ?? "")),
    "email"         => htmlspecialchars(trim($_POST["email"] ?? "")),
    "contact_no"    => htmlspecialchars(trim($_POST["contact_no"] ?? "")),
];

$success = false;

try {
    $mysqli->begin_transaction();

    // 1. Insert into events table
    $sqlEvent = "INSERT INTO events (event_name, event_date, event_time, location) VALUES (?, ?, ?, ?)";
    $stmtEvent = $mysqli->prepare($sqlEvent);
    $stmtEvent->bind_param("ssss",
        $data["event_name"],
        $data["event_date"],
        $data["event_time"],
        $data["location"]);
    $stmtEvent->execute();
    $event_id = $mysqli->insert_id;

    // 2. Insert into attendees table
    $sqlAttendee = "INSERT INTO attendees (event_id, attendee_name, email, contact_no) VALUES (?, ?, ?, ?)";
    $stmtAttendee = $mysqli->prepare($sqlAttendee);
    $stmtAttendee->bind_param("isss",
        $event_id,
        $data["attendee_name"],
        $data["email"],
        $data["contact_no"]);
    $stmtAttendee->execute();
    $attendee_id = $mysqli->insert_id;

    // 3. Insert into event_feedbacks table
    $sqlFeedback = "INSERT INTO event_feedbacks (
        attendee_id, event_planning, speaker_effectiveness, venue_setup,
        time_management, audience_participation, overall_experience,
        food_beverages, technical_support, effective_aspects,
        improvement_suggestions, participate_future, additional_feedback
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtFeedback = $mysqli->prepare($sqlFeedback);
    $stmtFeedback->bind_param("iiiiiiiiissss",
        $attendee_id,
        $data["event_planning"],
        $data["speaker_effectiveness"],
        $data["venue_setup"],
        $data["time_management"],
        $data["audience_participation"],
        $data["overall_experience"],
        $data["food_beverages"],
        $data["technical_support"],
        $data["effective_aspects"],
        $data["improvement_suggestions"],
        $data["participate_future"],
        $data["additional_feedback"]);
    $stmtFeedback->execute();

    $mysqli->commit();
    $success = true;

} catch (mysqli_sql_exception $e) {
    $mysqli->rollback();
    error_log("Failed to insert event feedback: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title><?= $success ? "Thank You" : "Error" ?> - Event Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{navy:{800:'#1e2a6e',900:'#1a2255',950:'#0f1333'},gold:{400:'#C9A96E',500:'#b5893a'}},fontFamily:{serif:['"Playfair Display"','serif'],sans:['"Inter"','sans-serif']}}}}</script>
    <style>
        body { background: #0f1333; }
        .scene-bg { position: fixed; inset: 0; z-index: 0; background: radial-gradient(ellipse at 20% 50%, rgba(91, 107, 250, 0.15) 0%, transparent 50%), radial-gradient(ellipse at 80% 20%, rgba(201, 169, 110, 0.08) 0%, transparent 50%), linear-gradient(180deg, #0f1333 0%, #1a2255 40%, #1e2a6e 70%, #0f1333 100%); }
        .glass-card-premium { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(201, 169, 110, 0.2); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        @keyframes checkDraw { 0% { stroke-dashoffset: 48; } 100% { stroke-dashoffset: 0; } }
        .check-animated { stroke-dasharray: 48; stroke-dashoffset: 48; animation: checkDraw .8s .4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes scaleIn { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .scale-animated { animation: scaleIn .6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col text-white relative overflow-x-hidden">
    <div class="scene-bg"></div>

    <header class="relative z-10 text-center py-10 border-b border-white/[0.05]">
        <h1 class="font-serif text-4xl font-bold text-white tracking-tight">Event Intelligence</h1>
        <p class="text-gold-400/60 text-[0.65rem] font-black uppercase tracking-[0.4em] mt-2">Feedback Manifest</p>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-16 relative z-10">
        <div class="max-w-xl w-full text-center">
        <?php if ($success): ?>
            <div class="glass-card-premium rounded-[3rem] p-12 mb-12 scale-animated">
                <div class="w-24 h-24 mx-auto mb-10 rounded-[2rem] flex items-center justify-center bg-gradient-to-br from-gold-400 to-gold-500 shadow-lg shadow-gold-400/20">
                    <svg class="w-12 h-12 text-navy-950" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path class="check-animated" d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                
                <h2 class="font-serif text-4xl font-bold text-white mb-6 fade-up" style="animation-delay: 0.2s">
                    Thank You<?= !empty($data["attendee_name"]) ? ", <span class='text-gold-400'>" . $data["attendee_name"] . "</span>" : "" ?>!
                </h2>
                
                <p class="text-white/60 text-lg leading-relaxed mb-8 fade-up" style="animation-delay: 0.4s">
                    Your contribution has been successfully cataloged. These insights are vital to our continuous strategic refinement.
                </p>

                <div class="flex items-center justify-center gap-4 mb-8 fade-up" style="animation-delay: 0.5s">
                    <span class="w-12 h-px bg-white/10"></span>
                    <span class="w-2 h-2 rounded-full bg-gold-400 shadow-[0_0_10px_rgba(201,169,110,0.5)]"></span>
                    <span class="w-12 h-px bg-white/10"></span>
                </div>

                <p class="text-white/40 text-sm font-medium uppercase tracking-widest fade-up" style="animation-delay: 0.6s">
                    Official Recognition of Participation
                </p>
            </div>

            <a href="index.php" class="inline-flex items-center gap-3 px-8 py-3 rounded-xl border border-gold-400/20 text-gold-400 text-xs font-black uppercase tracking-widest hover:bg-gold-400/10 transition-all fade-up" style="animation-delay: 0.8s">
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
                Return to Evaluation Index
            </a>
        <?php else: ?>
            <div class="glass-card-premium rounded-[3rem] p-12 mb-12 scale-animated">
                <div class="w-24 h-24 mx-auto mb-10 rounded-[2rem] bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="font-serif text-3xl font-bold text-white mb-4">Processing Error</h2>
                <p class="text-white/40 mb-10">The system encountered an anomaly and was unable to finalize your submission.</p>
                <a href="index.php" class="inline-flex items-center justify-center px-10 py-4 rounded-xl font-bold text-xs uppercase tracking-widest bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all">Re-initiate Submission</a>
            </div>
        <?php endif; ?>
        </div>
    </main>

    <footer class="relative z-10 text-center py-12 border-t border-white/[0.05]">
        <p class="text-white/20 text-[0.65rem] font-bold uppercase tracking-[0.5em]">United Performance Systems</p>
    </footer>
</body>
</html>
