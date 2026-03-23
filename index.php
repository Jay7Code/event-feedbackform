<!--
  ═══════════════════════════════════════════════════════════════
  EVENT EVALUATION FORM (FRONTEND)
  Event Feedback System – Clean, paper-like design
  styled with Tailwind CSS.
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
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f4ff',
                            100: '#dbe3ff',
                            200: '#bfccff',
                            300: '#93a8ff',
                            400: '#5b6bfa',
                            500: '#3b4edb',
                            600: '#2d3baf',
                            700: '#1e2a6e',
                            800: '#1a2255',
                            900: '#0f1333',
                        },
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
        body {
            background: #f5f5f5;
        }

        /* ── CUSTOM RADIO BUTTONS ── */
        .custom-radio input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .custom-radio .radio-mark {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            cursor: pointer;
            background: #fff;
        }

        .custom-radio .radio-mark::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #1e2a6e;
            transform: scale(0);
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .custom-radio input[type="radio"]:checked+.radio-mark {
            border-color: #1e2a6e;
            background: #f0f4ff;
        }

        .custom-radio input[type="radio"]:checked+.radio-mark::after {
            transform: scale(1);
        }

        .custom-radio:hover .radio-mark {
            border-color: #93a8ff;
        }

        /* ── RATING CIRCLE BUTTONS (no numbers) ── */
        .rating-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
        }

        .rating-circle:hover {
            border-color: #1e2a6e;
            background: #f0f4ff;
        }

        .rating-radio:checked+.rating-circle {
            background: #1e2a6e;
            border-color: #1e2a6e;
            box-shadow: 0 2px 8px rgba(30, 42, 110, 0.25);
        }

        /* ── FORM INPUTS ── */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: #fff;
            color: #1f2937;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:hover {
            border-color: #93a8ff;
        }

        .form-input:focus {
            border-color: #1e2a6e;
            box-shadow: 0 0 0 3px rgba(30, 42, 110, 0.08);
        }

        select.form-input {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20'%3E%3Cpath fill='%236b7280' d='M7 7l3 3 3-3' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        textarea.form-input {
            resize: none;
        }

        /* ── SCROLL REVEAL ── */
        .reveal-section {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .reveal-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── LOADING SPINNER ── */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner {
            animation: spin 0.8s linear infinite;
        }

        /* ── PRINT-FRIENDLY TABLE STYLE ── */
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-800 min-h-screen">

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 md:py-12">

        <!-- ═══ HEADER ═══ -->
        <header class="text-center mb-10 reveal-section">
            <!-- Top accent line -->
            <div class="w-16 h-1 bg-brand-700 mx-auto mb-8 rounded-full"></div>

            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl text-brand-900 font-bold mb-3">
                Event Evaluation Form
            </h1>

            <p class="text-gray-500 text-sm md:text-base max-w-lg mx-auto leading-relaxed">
                Thank you for attending our event! Your feedback helps us improve future events
                and deliver exceptional experiences.
            </p>

            <!-- Divider -->
            <div class="flex items-center justify-center gap-3 mt-6">
                <span class="w-12 h-px bg-gray-300"></span>
                <span class="w-1.5 h-1.5 rotate-45 bg-brand-700"></span>
                <span class="w-12 h-px bg-gray-300"></span>
            </div>
        </header>

        <!-- ═══ FEEDBACK FORM ═══ -->
        <form id="feedbackForm" action="submit_feedback.php" method="POST" class="space-y-6">

            <!-- ═══ SECTION 1: EVENT OVERVIEW ═══ -->
            <section class="reveal-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-gray-800 text-lg font-semibold">Event Overview</h2>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Event Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="event_name" placeholder="e.g. Annual Conference 2026" required
                                class="form-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Event Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="event_date" required class="form-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Event Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="event_time" required class="form-input">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <select name="location" id="location_dropdown" onchange="toggleOtherLocation()" required class="form-input">
                                <option value="" disabled selected>Select a location</option>
                                <option value="19th T">19th T</option>
                                <option value="Adivay Hall">Adivay Hall</option>
                                <option value="St. Patricks">St. Patricks</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" id="other_location" name="other_location_text"
                                placeholder="Please specify the location" class="form-input mt-2 hidden">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 2: EVENT RATINGS (1–5) ═══ -->
            <section class="reveal-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-gray-800 text-lg font-semibold">Rate the Event</h2>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-gray-500 mb-5 text-center">
                        Rate the event based on the following criteria
                        <span class="font-medium text-gray-600">(1 = Poor, 5 = Excellent)</span>
                    </p>

                    <!-- Column headers (desktop) -->
                    <div class="hidden md:grid grid-cols-[1fr_repeat(5,44px)] gap-2 mb-2 px-2">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Category</span>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="text-center text-xs font-bold text-gray-400"><?= $i ?></span>
                        <?php endfor; ?>
                    </div>

                    <!-- Divider -->
                    <div class="hidden md:block h-px bg-gray-200 mb-1"></div>

                    <!-- Rating rows -->
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
                        <div
                            class="grid grid-cols-1 md:grid-cols-[1fr_repeat(5,44px)] gap-2 items-center py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 rounded-lg px-2 transition-colors">
                            <span class="font-medium text-sm text-gray-700 text-center md:text-left"><?= $label ?></span>
                            <div class="flex md:contents justify-center gap-5 md:gap-0">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-[0.6rem] text-gray-400 font-medium md:hidden"><?= $i ?></span>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="<?= $name ?>" value="<?= $i ?>"
                                                <?= $i === 1 ? "required" : "" ?> class="rating-radio sr-only">
                                            <span class="rating-circle"></span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Scale legend -->
                    <div class="flex justify-between mt-4 px-2">
                        <span class="text-xs text-gray-400 font-medium">1 = Poor</span>
                        <span class="text-xs text-gray-400 font-medium">5 = Excellent</span>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 3: OPEN-ENDED QUESTIONS ═══ -->
            <section class="reveal-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-gray-800 text-lg font-semibold">Your Thoughts</h2>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                            What aspects of the event did you find most effective?
                        </label>
                        <textarea name="effective_aspects" rows="3"
                            placeholder="Share what worked well..."
                            class="form-input"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                            What improvements would you suggest for future events?
                        </label>
                        <textarea name="improvement_suggestions" rows="3"
                            placeholder="Your suggestions for improvement..."
                            class="form-input"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 4: FUTURE PARTICIPATION & ADDITIONAL FEEDBACK ═══ -->
            <section class="reveal-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-gray-800 text-lg font-semibold">Future Participation</h2>
                </div>
                <div class="px-6 py-5 space-y-5">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-3">
                            Would you like to participate in future events? <span class="text-red-500">*</span>
                        </p>
                        <div class="flex gap-8 flex-wrap">
                            <?php
                            $futureOptions = ['Yes', 'No', 'Maybe'];
                            foreach ($futureOptions as $opt): ?>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <div class="custom-radio">
                                        <input type="radio" name="participate_future" value="<?= $opt ?>" required>
                                        <span class="radio-mark"></span>
                                    </div>
                                    <span class="text-sm text-gray-600 group-hover:text-brand-700 transition-colors"><?= $opt ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                            Additional Feedback
                        </label>
                        <textarea name="additional_feedback" rows="4"
                            placeholder="Any other thoughts or comments..."
                            class="form-input"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 5: ATTENDEE INFORMATION ═══ -->
            <section class="reveal-section bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="font-serif text-gray-800 text-lg font-semibold">Your Information</h2>
                </div>
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Group Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="attendee_name" placeholder="Enter your group name" required
                                class="form-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Gmail <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" placeholder="yourgroup@gmail.com" required
                                class="form-input">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="contact_no" placeholder="+63 917 123 4567" required
                                class="form-input">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SUBMIT BUTTON ═══ -->
            <div class="reveal-section text-center pt-4 pb-8">
                <p class="text-gray-500 text-sm mb-6 italic">
                    Thank you for taking the time to share your feedback.
                </p>
                <button type="submit" id="submitBtn"
                    class="group relative inline-flex items-center justify-center gap-3 px-10 py-3.5 rounded-lg font-semibold text-sm uppercase tracking-wider transition-all duration-300 w-full sm:w-auto sm:min-w-[260px] bg-brand-700 text-white hover:bg-brand-800 hover:shadow-lg hover:shadow-brand-700/20 active:scale-[0.98]">
                    <div id="btnLoader" class="hidden">
                        <svg class="w-5 h-5 spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </div>
                    <span id="btnText">Submit Feedback</span>
                    <svg id="btnArrow" class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>

        </form>
    </div>

    <!-- ═══ FOOTER ═══ -->
    <footer class="text-center py-8 border-t border-gray-200 bg-white">
        <p class="font-serif text-lg text-brand-700 font-bold mb-1">Event Evaluation Form</p>
        <p class="text-gray-400 text-xs">Your feedback makes a difference</p>
    </footer>

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
            b.disabled = true; b.style.opacity = "0.6"; b.style.cursor = "not-allowed";
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
