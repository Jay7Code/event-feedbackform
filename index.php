<!--
  ═══════════════════════════════════════════════════════════════
  EVENT EVALUATION FORM (FRONTEND)
  Event Feedback System – styled with Tailwind CSS,
  glassmorphism, and custom animations.
  ═══════════════════════════════════════════════════════════════
-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Evaluation Form</title>

    <!-- External Resources -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: { 800: '#1e2a6e', 900: '#1a2255', 950: '#0f1333' },
                        gold: { 400: '#C9A96E', 500: '#b5893a' },
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Custom Styles -->
    <style>
        /* ── BASE ── */
        body { background: #0f1333; }

        /* ── IMMERSIVE BACKGROUND ── */
        .scene-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(91, 107, 250, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(201, 169, 110, 0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(67, 71, 239, 0.10) 0%, transparent 50%),
                        linear-gradient(180deg, #0f1333 0%, #1a2255 40%, #1e2a6e 70%, #0f1333 100%);
        }

        .scene-bg::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse at 30% 20%, rgba(201, 169, 110, 0.04) 0%, transparent 60%),
                        radial-gradient(ellipse at 70% 80%, rgba(15, 19, 51, 0.30) 0%, transparent 60%);
        }

        /* ── WARM VIGNETTE ── */
        .warm-vignette {
            position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0; pointer-events: none;
            background: radial-gradient(ellipse at 50% 0%, rgba(201, 169, 110, 0.03) 0%, transparent 50%);
        }

        /* ── FLOATING PARTICLES ── */
        .particle { position: fixed; border-radius: 50%; pointer-events: none; z-index: 1; }
        .particle-1 { width: 3px; height: 3px; background: rgba(201, 169, 110, 0.45); top: 20%; left: 15%; animation: float1 8s ease-in-out infinite; }
        .particle-2 { width: 2px; height: 2px; background: rgba(91, 107, 250, 0.30); top: 45%; left: 80%; animation: float2 12s ease-in-out infinite; }
        .particle-3 { width: 4px; height: 4px; background: rgba(201, 169, 110, 0.18); top: 70%; left: 25%; animation: float3 10s ease-in-out infinite; }
        .particle-4 { width: 2px; height: 2px; background: rgba(91, 107, 250, 0.35); top: 35%; left: 60%; animation: float1 14s ease-in-out infinite 2s; }

        @keyframes float1 { 0%, 100% { transform: translate(0, 0) scale(1); opacity: 0; } 10% { opacity: 1; } 50% { transform: translate(30px, -40px) scale(1.5); opacity: 0.7; } 90% { opacity: 1; } }
        @keyframes float2 { 0%, 100% { transform: translate(0, 0) scale(1); opacity: 0; } 15% { opacity: 1; } 50% { transform: translate(-20px, -50px) scale(1.3); opacity: 0.5; } 85% { opacity: 1; } }
        @keyframes float3 { 0%, 100% { transform: translate(0, 0) scale(1); opacity: 0; } 20% { opacity: 0.8; } 50% { transform: translate(40px, -30px) scale(1.8); opacity: 0.4; } 80% { opacity: 0.8; } }

        /* ── GLASSMORPHISM CARDS ── */
        .glass-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(45px); -webkit-backdrop-filter: blur(45px);
            border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35), inset 0 2px 2px rgba(255, 255, 255, 0.10);
        }

        .glass-card-warm {
            background: rgba(201, 169, 110, 0.06); backdrop-filter: blur(36px); -webkit-backdrop-filter: blur(36px);
            border: 1px solid rgba(201, 169, 110, 0.18); box-shadow: 0 12px 40px rgba(0, 0, 0, 0.30), inset 0 2px 2px rgba(201, 169, 110, 0.12);
        }

        /* ── CUSTOM RADIO BUTTONS ── */
        .custom-radio input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .custom-radio .radio-mark {
            width: 22px; height: 22px; border-radius: 50%; border: 2px solid rgba(201, 169, 110, 0.35);
            display: flex; align-items: center; justify-content: center; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; background: rgba(255, 255, 255, 0.05);
        }
        .custom-radio .radio-mark::after {
            content: ''; width: 10px; height: 10px; border-radius: 50%; background: #C9A96E; transform: scale(0); transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .custom-radio input[type="radio"]:checked+.radio-mark { border-color: #C9A96E; background: rgba(201, 169, 110, 0.1); box-shadow: 0 0 12px rgba(201, 169, 110, 0.2); }
        .custom-radio input[type="radio"]:checked+.radio-mark::after { transform: scale(1); }
        .custom-radio:hover .radio-mark { border-color: rgba(201, 169, 110, 0.6); background: rgba(201, 169, 110, 0.05); }

        /* ── RATING CIRCLE BUTTONS (no numbers) ── */
        .rating-circle {
            width: 28px; height: 28px; border-radius: 50%; border: 2px solid rgba(201, 169, 110, 0.35);
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: rgba(255, 255, 255, 0.05);
        }
        .rating-circle:hover { border-color: rgba(201, 169, 110, 0.6); background: rgba(201, 169, 110, 0.05); }
        .rating-radio:checked+.rating-circle { background: #C9A96E; border-color: #C9A96E; box-shadow: 0 0 12px rgba(201, 169, 110, 0.25); }

        /* ── LODGE INPUT FIELDS ── */
        .lodge-input {
            width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid rgba(201, 169, 110, 0.2);
            background: rgba(255, 255, 255, 0.06); color: rgba(255, 255, 255, 0.85); font-family: 'Inter', sans-serif; font-size: 0.875rem; transition: all 0.3s ease; outline: none;
        }
        .lodge-input::placeholder { color: rgba(255, 255, 255, 0.3); }
        .lodge-input:hover { border-color: rgba(201, 169, 110, 0.4); }
        .lodge-input:focus { border-color: #C9A96E; background: rgba(255, 255, 255, 0.1); box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.1), 0 4px 16px rgba(0, 0, 0, 0.2); }
        select.lodge-input { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20'%3E%3Cpath fill='%23C9A96E' d='M7 7l3 3 3-3' stroke='%23C9A96E' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; }
        select.lodge-input option { background: #1a2255; color: #fff; }
        textarea.lodge-input { resize: none; }
        input[type="date"].lodge-input, input[type="time"].lodge-input { min-height: 48px; }
        input[type="date"]::-webkit-calendar-picker-indicator, input[type="time"]::-webkit-calendar-picker-indicator { background: rgba(201, 169, 110, 0.2); padding: 4px; border-radius: 4px; cursor: pointer; filter: invert(0.7) sepia(1) saturate(2) hue-rotate(10deg); }

        /* ── SCROLL REVEAL ── */
        .reveal-section { opacity: 0; transform: translateY(40px); transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
        .reveal-section.visible { opacity: 1; transform: translateY(0); }

        /* ── HEADERS ── */
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(30px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-fade-up { opacity: 0; animation: fadeUp 0.8s ease-out forwards; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.8s linear infinite; }
    </style>
</head>

<body class="font-sans antialiased text-white min-h-screen relative">

    <!-- ═══ BACKGROUND LAYERS ═══ -->
    <div class="scene-bg"></div>
    <div class="warm-vignette"></div>

    <!-- Floating particles -->
    <div class="particle particle-1"></div>
    <div class="particle particle-2"></div>
    <div class="particle particle-3"></div>
    <div class="particle particle-4"></div>

    <!-- ═══ MAIN CONTENT WRAPPER ═══ -->
    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 py-12 md:py-20">

        <!-- ═══ HERO HEADER ═══ -->
        <header class="text-center mb-12">
            <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl text-white/90 mb-4 animate-fade-up font-bold drop-shadow-md">
                Event Evaluation
            </h1>
            <p class="font-serif italic text-white/60 text-sm md:text-base leading-relaxed animate-fade-up" style="animation-delay: 0.2s;">
                Thank you for attending our event! Your feedback helps us improve future events
                and deliver exceptional experiences.
            </p>
            <!-- Divider -->
            <div class="flex items-center justify-center gap-4 mt-8 animate-fade-up" style="animation-delay: 0.3s;">
                <span class="w-16 h-px bg-gold-400/20"></span>
                <span class="w-1.5 h-1.5 rotate-45 bg-gold-400"></span>
                <span class="w-16 h-px bg-gold-400/20"></span>
            </div>
        </header>

        <!-- ═══ FEEDBACK FORM ═══ -->
        <form id="feedbackForm" action="submit_feedback.php" method="POST" class="space-y-8">

            <!-- ═══ SECTION 1: EVENT OVERVIEW ═══ -->
            <section class="reveal-section glass-card-warm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold-400/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-white/80 text-lg tracking-wider uppercase">Event Overview</h2>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Event Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="event_name" placeholder="e.g. Annual Conference 2026" required class="lodge-input">
                        </div>
                        <div>
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Event Date <span class="text-red-400">*</span>
                            </label>
                            <input type="date" name="event_date" required class="lodge-input">
                        </div>
                        <div>
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Event Time <span class="text-red-400">*</span>
                            </label>
                            <input type="time" name="event_time" required class="lodge-input">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Location <span class="text-red-400">*</span>
                            </label>
                            <select name="location" id="location_dropdown" onchange="toggleOtherLocation()" required class="lodge-input">
                                <option value="" disabled selected>Select a location</option>
                                <option value="19th T">19th T</option>
                                <option value="Adivay Hall">Adivay Hall</option>
                                <option value="St. Patricks">St. Patricks</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" id="other_location" name="other_location_text"
                                placeholder="Please specify the location" class="lodge-input mt-3 hidden">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 2: EVENT RATINGS ═══ -->
            <section class="reveal-section glass-card-warm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold-400/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-white/80 text-lg tracking-wider uppercase">Rate the Event</h2>
                </div>
                <div class="px-6 py-6">
                    <p class="text-sm text-white/50 mb-6 text-center">
                        Rate the event based on the following criteria <span class="font-medium text-white/70"></span>
                    </p>

                    <!-- Column headers -->
                    <div class="hidden md:grid grid-cols-[1fr_repeat(5,44px)] gap-2 mb-3 px-2">
                        <span></span>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="text-center text-[0.6rem] font-bold text-white uppercase tracking-[0.1em]"><?= $i ?></span>
                        <?php endfor; ?>
                    </div>

                    <!-- Rows -->
                    <?php
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

                    foreach ($ratingCategories as $name => $label): ?>
                        <div class="grid grid-cols-1 md:grid-cols-[1fr_repeat(5,44px)] gap-2 items-center py-3 border-b border-gold-400/10 last:border-b-0 hover:bg-white/[0.04] rounded-lg px-2 transition-colors duration-300">
                            <span class="font-medium text-sm text-gold-400 text-center md:text-left"><?= $label ?></span>
                            <div class="flex md:contents justify-center gap-5 md:gap-0 mt-3 md:mt-0">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-[0.6rem] text-white/40 font-medium md:hidden"><?= $i ?></span>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="<?= $name ?>" value="<?= $i ?>" <?= $i === 1 ? "required" : "" ?> class="rating-radio sr-only">
                                            <span class="rating-circle"></span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Scale legend -->
                    <div class="flex justify-between mt-5 px-2">
                        <span class="text-[0.6rem] text-white/40 uppercase tracking-[0.2em] font-medium">1 = Poor</span>
                        <span class="text-[0.6rem] text-white/40 uppercase tracking-[0.2em] font-medium">5 = Excellent</span>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 3: OPEN-ENDED QUESTIONS ═══ -->
            <section class="reveal-section glass-card-warm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold-400/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-white/80 text-lg tracking-wider uppercase">Your Thoughts</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div>
                        <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                            What aspects of the event did you find most effective?
                        </label>
                        <textarea name="effective_aspects" rows="3" placeholder="Share what worked well..." class="lodge-input"></textarea>
                    </div>
                    <div>
                        <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                            What improvements would you suggest for future events?
                        </label>
                        <textarea name="improvement_suggestions" rows="3" placeholder="Your suggestions for improvement..." class="lodge-input"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 4: FUTURE PARTICIPATION & ADDITIONAL FEEDBACK ═══ -->
            <section class="reveal-section glass-card-warm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold-400/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-white/80 text-lg tracking-wider uppercase">Future Participation</h2>
                </div>
                <div class="px-6 py-6 space-y-6">
                    <div>
                        <p class="text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-4">
                            Would you like to participate in future events? <span class="text-red-400">*</span>
                        </p>
                        <div class="flex gap-8 flex-wrap">
                            <?php foreach (['Yes', 'No', 'Maybe'] as $opt): ?>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="custom-radio">
                                        <input type="radio" name="participate_future" value="<?= $opt ?>" required>
                                        <span class="radio-mark"></span>
                                    </div>
                                    <span class="text-sm text-white/70 group-hover:text-gold-400 transition-colors"><?= $opt ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="border-t border-gold-400/10 pt-6">
                        <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                            Additional Feedback
                        </label>
                        <textarea name="additional_feedback" rows="4" placeholder="Any other thoughts or comments..." class="lodge-input"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 5: ATTENDEE INFORMATION ═══ -->
            <section class="reveal-section glass-card-warm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gold-400/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-white/80 text-lg tracking-wider uppercase">Your Information</h2>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Group Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="attendee_name" placeholder="Enter your group name" required class="lodge-input">
                        </div>
                        <div>
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Gmail <span class="text-red-400">*</span>
                            </label>
                            <input type="email" name="email" placeholder="yourgroup@gmail.com" required class="lodge-input">
                        </div>
                        <div>
                            <label class="block text-[0.75rem] font-bold text-gold-400 uppercase tracking-[0.15em] mb-2 drop-shadow-sm">
                                Contact Number <span class="text-red-400">*</span>
                            </label>
                            <input type="tel" name="contact_no" placeholder="+63 917 123 4567" required class="lodge-input">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SUBMIT BUTTON ═══ -->
            <div class="reveal-section text-center pt-6 pb-4">
                <button type="submit" id="submitBtn"
                    class="group relative inline-flex items-center justify-center gap-3 px-10 py-3.5 rounded-full font-semibold text-[0.8rem] uppercase tracking-[0.2em] transition-all duration-500 w-full sm:w-auto sm:min-w-[260px] overflow-hidden"
                    style="background:linear-gradient(135deg,#C9A96E 0%,#b5893a 50%,#C9A96E 100%);color:#0f1333;box-shadow:0 8px 32px rgba(201,169,110,0.25)">
                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                    <div id="btnLoader" class="hidden relative z-10">
                        <svg class="w-5 h-5 spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </div>
                    <span id="btnText" class="relative z-10">Submit Feedback</span>
                    <svg id="btnArrow" class="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- ═══ JAVASCRIPT ═══ -->
    <script>
        /** Toggle "Other" location text field visibility */
        function toggleOtherLocation() {
            var d = document.getElementById("location_dropdown");
            var o = document.getElementById("other_location");
            if (d.value === "Others") {
                o.classList.remove("hidden"); o.required = true; o.focus();
            } else {
                o.classList.add("hidden"); o.required = false; o.value = "";
            }
        }

        /** Form submit handler: show spinner, disable button */
        document.getElementById("feedbackForm").addEventListener("submit", function (e) {
            if (!this.checkValidity()) return;
            var b = document.getElementById("submitBtn");
            var l = document.getElementById("btnLoader");
            var t = document.getElementById("btnText");
            var a = document.getElementById("btnArrow");
            b.disabled = true; b.style.opacity = "0.7"; b.style.cursor = "not-allowed";
            l.classList.remove("hidden"); a.classList.add("hidden");
            t.innerText = "Submitting...";
        });

        /** IntersectionObserver: fade-in sections on scroll */
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.06, rootMargin: "0px 0px -40px 0px" });

        document.querySelectorAll(".reveal-section").forEach(function (s) {
            observer.observe(s);
        });
    </script>
</body>
</html>
