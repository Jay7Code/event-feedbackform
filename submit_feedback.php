<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * FEEDBACK SUBMISSION SCRIPT
 * Processes POST requests from the guest feedback form.
 * Captures ratings, comments, and guest details, then inserts
 * them into the normalized database tables.
 * Sends an automated "Thank You" email via PHPMailer on success.
 * ═══════════════════════════════════════════════════════════════
 */
require_once __DIR__ . "/config.php";

// ── PHPMailer ────────────────────────────────────────────────

require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/email/config.php";

function sendThankYouEmail(string $guestName, string $guestEmail): bool
{
    $displayName = !empty($guestName) ? htmlspecialchars($guestName) : 'Valued Attendee';

    $htmlBody = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thank You - Event Intelligence</title>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    </head>
    <body style="margin: 0; padding: 0; background-color: #FCFBFA; font-family: \'Inter\', Arial, sans-serif;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FCFBFA;">
            <tr>
                <td align="center" style="padding: 40px 0;">

                    <!-- Header -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="width: 100%; max-width: 600px;">
                        <tr>
                            <td align="center" style="padding: 20px 40px 20px 40px;">
                                <h1 style="margin: 0 0 6px 0; font-family: \'Playfair Display\', serif; font-size: 28px; font-weight: 700; color: #153A26;">
                                    John Hay Hotels
                                </h1>
                                <p style="margin: 0; font-family: \'Inter\', Arial, sans-serif; font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: #C0A062;">
                                    Forest Wing
                                </p>
                            </td>
                        </tr>
                    </table>

                    <!-- Main Content Card -->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="width: 100%; max-width: 600px;">
                        <tr>
                            <td align="center" style="padding: 20px 40px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #FFFFFF; border-radius: 8px; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05);">
                                    <tr>
                                        <td align="center" style="padding: 40px 30px;">
                                            <!-- Gold Checkmark Circle -->
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 30px;">
                                                <tr>
                                                    <td align="center" style="width: 64px; height: 64px; border-radius: 50%; background: #C0A062;">
                                                        <span style="font-size: 32px; color: #ffffff; line-height: 1;">&#10003;</span>
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Greeting -->
                                            <h2 style="margin: 0 0 20px 0; font-family: \'Playfair Display\', Georgia, serif; font-size: 24px; font-weight: 600; color: #153A26;">
                                                Thank You, ' . $displayName . '!
                                            </h2>

                                            <!-- Main message -->
                                            <p style="margin: 0 0 28px 0; font-family: \'Inter\', sans-serif; font-size: 15px; line-height: 1.6; color: #333333; text-align: center;">
                                                Your feedback is invaluable to us. It helps us continue delivering the exceptional experience you deserve at John Hay Hotels.
                                            </p>

                                            <!-- Divider -->
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 28px;">
                                                <tr>
                                                    <td style="width: 40px; border-top: 1px solid #EAE6DB;"></td>
                                                    <td style="padding: 0 12px;">
                                                        <span style="color: #C0A062; font-size: 8px; line-height: 1;">&#9670;</span>
                                                    </td>
                                                    <td style="width: 40px; border-top: 1px solid #EAE6DB;"></td>
                                                </tr>
                                            </table>

                                            <!-- Closing message -->
                                            <p style="margin: 0; font-family: \'Inter\', sans-serif; font-size: 14px; font-weight: 500; color: #666666; text-transform: uppercase; letter-spacing: 1px; text-align: center;">
                                               We look forward to welcoming you again soon.
                                            </p>
                                            <p style="margin: 0; font-family: \'Inter\', sans-serif; font-size: 12px; font-weight: 400; color: #888888; text-align: center; line-height: 1.5;">
    <strong>Your privacy matters.</strong> The information provided in this form will be used exclusively to process your feedback and improve our services.
</p>

                                        </td>
                                    </tr>   
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- Footer -->
                     <footer class="text-center py-10 border-t border-paper-200/50 bg-paper-100/30">
        <p class="font-serif text-xl text-pine-900 mb-2">John Hay Hotels</p>
        <p class="text-pine-700/60 text-xs font-medium uppercase tracking-[0.2em]">Forest Wing - Camp John Hay - Baguio City, 2600</p>
    </footer>

                </td>
            </tr>
        </table>
    </body>
    </html>';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;
        $mail->setFrom(SMTP_USERNAME, SMTP_FROM_NAME);
        $mail->addAddress($guestEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Thank You for Your Feedback';
        $mail->Body = $htmlBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

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
    "location_raw"  => ($_POST["location"] ?? "") === "Others"
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

    // 1. Resolve location_id from the locations table (insert if new)
    $location_id = null;
    if (!empty($data["location_raw"])) {
        $stmtLoc = $mysqli->prepare("SELECT id FROM locations WHERE location_name = ?");
        $stmtLoc->bind_param("s", $data["location_raw"]);
        $stmtLoc->execute();
        $locResult = $stmtLoc->get_result();
        if ($locRow = $locResult->fetch_assoc()) {
            $location_id = $locRow["id"];
        } else {
            $stmtLocInsert = $mysqli->prepare("INSERT INTO locations (location_name) VALUES (?)");
            $stmtLocInsert->bind_param("s", $data["location_raw"]);
            $stmtLocInsert->execute();
            $location_id = $mysqli->insert_id;
        }
    }

    // 2. Find existing event or insert a new one (deduplicated)
    $stmtFind = $mysqli->prepare(
        "SELECT id FROM events
         WHERE event_name = ?
           AND (event_date = ? OR (event_date IS NULL AND ? IS NULL))
           AND (event_time = ? OR (event_time IS NULL AND ? IS NULL))
           AND (location_id = ? OR (location_id IS NULL AND ? IS NULL))
         LIMIT 1"
    );
    $stmtFind->bind_param("sssssii",
        $data["event_name"],
        $data["event_date"], $data["event_date"],
        $data["event_time"], $data["event_time"],
        $location_id, $location_id);
    $stmtFind->execute();
    $eventResult = $stmtFind->get_result();

    if ($eventRow = $eventResult->fetch_assoc()) {
        $event_id = $eventRow["id"];
    } else {
        $stmtEvent = $mysqli->prepare("INSERT INTO events (event_name, event_date, event_time, location_id) VALUES (?, ?, ?, ?)");
        $stmtEvent->bind_param("sssi",
            $data["event_name"],
            $data["event_date"],
            $data["event_time"],
            $location_id);
        $stmtEvent->execute();
        $event_id = $mysqli->insert_id;
    }

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
    
    if (!empty($data['email'])) {
        sendThankYouEmail($data['attendee_name'], $data['email']);
    }

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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
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

        .glass-card-premium { background: #FFFFFF; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05); }
        @keyframes checkDraw { 0% { stroke-dashoffset: 48; } 100% { stroke-dashoffset: 0; } }
        .check-animated { stroke-dasharray: 48; stroke-dashoffset: 48; animation: checkDraw .8s .4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes scaleIn { 0% { transform: scale(0.8); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .scale-animated { animation: scaleIn .6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="font-sans min-h-screen flex flex-col text-pine-900 relative overflow-x-hidden">
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>

    <header class="relative z-10 text-center py-10 border-b border-paper-200 bg-white/50 backdrop-blur-md">
        <h1 class="font-serif text-4xl font-bold text-pine-900 tracking-tight">John Hay Hotels</h1>
        <p class="text-antique-500 text-[0.65rem] font-black uppercase tracking-[0.4em] mt-2">Forest Wing</p>
    </header>

    <main class="flex-1 flex items-center justify-center px-4 py-16 relative z-10">
        <div class="max-w-xl w-full text-center">
        <?php if ($success): ?>
            <div class="glass-card-premium rounded-[2rem] p-12 mb-12 scale-animated relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-paper-100/50 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="w-24 h-24 mx-auto mb-10 rounded-full flex items-center justify-center bg-gradient-to-br from-antique-400 to-antique-500 shadow-[0_10px_30px_rgba(192,160,98,0.2)]">
                        <svg class="w-12 h-12 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path class="check-animated" d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    
                    <h2 class="font-serif text-4xl font-bold text-pine-900 mb-6 fade-up" style="animation-delay: 0.2s">
                        Thank You<?= !empty($data["attendee_name"]) ? ", <span class='text-antique-500'>" . $data["attendee_name"] . "</span>" : "" ?>!
                    </h2>
                    
                    <p class="text-pine-800/80 text-lg leading-relaxed mb-8 fade-up" style="animation-delay: 0.4s">
                       Your feedback is invaluable to us. It helps us continue delivering the exceptional experience you deserve at John Hay Hotels.
                    </p>

                    <div class="flex items-center justify-center gap-4 mb-8 fade-up" style="animation-delay: 0.5s">
                        <span class="w-12 h-px bg-paper-200"></span>
                        <span class="w-2 h-2 rounded-full bg-antique-500 shadow-[0_0_10px_rgba(192,160,98,0.5)]"></span>
                        <span class="w-12 h-px bg-paper-200"></span>
                    </div>

                    <p class="text-pine-700/60 text-sm font-medium uppercase tracking-widest fade-up" style="animation-delay: 0.6s">
                        We look forward to welcoming you again soon.
                    </p>
                </div>
            </div>

            <a href="index.php" class="inline-flex items-center gap-3 px-8 py-3 rounded-full border border-paper-200 bg-white text-antique-500 text-xs font-black uppercase tracking-widest hover:border-antique-400 hover:text-antique-600 transition-all shadow-sm hover:shadow-md fade-up" style="animation-delay: 0.8s">
                <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
                Return to feedback <form action=""></form>
            </a>
        <?php else: ?>
            <div class="glass-card-premium rounded-[2rem] p-12 mb-12 scale-animated">
                <div class="w-24 h-24 mx-auto mb-10 rounded-full bg-red-50 border border-red-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h2 class="font-serif text-3xl font-bold text-pine-900 mb-4">Processing Error</h2>
                <p class="text-pine-800/70 mb-10">The system encountered an anomaly and was unable to finalize your submission.</p>
                <a href="index.php" class="inline-flex items-center justify-center px-10 py-4 rounded-full font-bold text-xs uppercase tracking-widest bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition-all">Re-initiate Submission</a>
            </div>
        <?php endif; ?>
        </div>
    </main>

    <footer class="text-center py-10 border-t border-paper-200/50 bg-paper-100/30">
        <p class="font-serif text-xl text-pine-900 mb-2">John Hay Hotels</p>
        <p class="text-pine-700/60 text-xs font-medium uppercase tracking-[0.2em]">Forest Wing - Camp John Hay - Baguio City, 2600</p>
    </footer>
</body>
</html>
