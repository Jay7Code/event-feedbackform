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
    <script>tailwind.config={theme:{extend:{colors:{navy:{800:'#1e2a6e',900:'#1a2255',950:'#0f1333'},gold:{400:'#C9A96E'}},fontFamily:{script:['"Great Vibes"','cursive'],serif:['"Playfair Display"','serif'],sans:['"Inter"','sans-serif']}}}}</script>
    <style>
        body{background:#0f1333}
        .scene-bg{position:fixed;inset:0;z-index:0;background:radial-gradient(ellipse at 20% 50%,rgba(91,107,250,0.15) 0%,transparent 50%),radial-gradient(ellipse at 80% 20%,rgba(201,169,110,0.08) 0%,transparent 50%),linear-gradient(180deg,#0f1333 0%,#1a2255 40%,#1e2a6e 70%,#0f1333 100%)}
        @keyframes checkDraw{0%{stroke-dashoffset:48}100%{stroke-dashoffset:0}}
        .check-animated{stroke-dasharray:48;stroke-dashoffset:48;animation:checkDraw .6s .4s ease-out forwards}
        @keyframes scaleIn{0%{transform:scale(0)}100%{transform:scale(1)}}
        .scale-animated{animation:scaleIn .4s cubic-bezier(.34,1.56,.64,1) forwards}
        @keyframes fadeUp{0%{opacity:0;transform:translateY(20px)}100%{opacity:1;transform:translateY(0)}}
        .fade-up{opacity:0;animation:fadeUp .6s ease-out forwards}
        .glass-card-warm{background:rgba(201,169,110,0.06);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid rgba(201,169,110,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.25),inset 0 1px 0 rgba(201,169,110,0.10)}
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col text-white relative">
    <div class="scene-bg"></div>
    <header class="relative z-10 text-center py-6 border-b border-white/[0.05]">
        <h1 class="font-serif text-3xl text-white/80 mb-1 font-bold">Event Evaluation</h1>
        <div class="flex items-center justify-center gap-3">
            <span class="w-8 h-px bg-gold-400/40"></span>
            <span class="text-gold-400/80 text-[0.65rem] font-semibold tracking-[0.3em] uppercase">Feedback Form</span>
            <span class="w-8 h-px bg-gold-400/40"></span>
        </div>
    </header>
    <main class="flex-1 flex items-center justify-center px-4 py-16 relative z-10">
        <div class="max-w-lg w-full text-center">
        <?php if ($success): ?>
            <div class="glass-card-warm rounded-2xl px-8 py-10 max-w-2xl mx-auto mb-10 fade-up">
                <div class="w-24 h-24 mx-auto mb-8 rounded-full flex items-center justify-center scale-animated" style="background:linear-gradient(135deg,#C9A96E,#b5893a)">
                    <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none">
                        <path class="check-animated" d="M5 13l4 4L19 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h2 class="font-serif text-3xl md:text-4xl text-white/90 mb-4 fade-up" style="animation-delay:.3s">Thank You<?= !empty($data["attendee_name"]) ? ", " . $data["attendee_name"] : "" ?>!</h2>
                <p class="font-serif italic text-white/90 text-lg md:text-xl mb-10 drop-shadow-lg font-medium leading-relaxed fade-up" style="animation-delay:.5s">Your feedback is invaluable to us. It helps us plan and deliver better events in the future.</p>
                <div class="flex items-center justify-center gap-3 mb-8 fade-up" style="animation-delay:.6s">
                    <span class="w-12 h-px bg-gold-400/30"></span>
                    <span class="w-2 h-2 rotate-45 bg-gold-400/40"></span>
                    <span class="w-12 h-px bg-gold-400/30"></span>
                </div>
                <p class="font-serif italic text-white/90 text-lg md:text-xl mb-6 drop-shadow-lg font-medium leading-relaxed fade-up" style="animation-delay:.7s">We hope to see you at our future events!</p>
            </div>
            <a href="index.php" class="inline-flex items-center gap-2 text-sm font-semibold text-gold-400/70 uppercase tracking-wider hover:text-gold-400 transition-colors duration-300 fade-up" style="animation-delay:.8s">
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
                Back to Feedback Form
            </a>
        <?php else: ?>
            <div class="w-24 h-24 mx-auto mb-8 rounded-full bg-red-900/30 flex items-center justify-center scale-animated">
                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="font-serif text-3xl text-white/80 mb-4">Something Went Wrong</h2>
            <p class="text-white/40 mb-8">We could not process your feedback. Please try again.</p>
            <a href="index.php" class="inline-flex items-center gap-2 px-8 py-3 rounded-full font-semibold text-sm uppercase tracking-wider transition-colors" style="background:linear-gradient(135deg,#C9A96E,#b5893a);color:#0f1333">Try Again</a>
        <?php endif; ?>
        </div>
    </main>
    <footer class="relative z-10 text-center py-12 border-t border-white/10 bg-black/20 backdrop-blur-sm">
        <p class="font-serif text-2xl text-gold-400/90 mb-3 drop-shadow-md font-bold">Event Evaluation Form</p>
        <p class="text-white/40 text-[0.7rem] font-medium uppercase tracking-[0.3em]">Your feedback makes a difference</p>
    </footer>
</body>
</html>
