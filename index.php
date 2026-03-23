<!--
  ═══════════════════════════════════════════════════════════════
  EVENT EVALUATION FORM (FRONTEND)
  Event Feedback System – Elegant Hotel Theme (John Hay Inspired)
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pine: {
                            50: '#f2f7f4',
                            100: '#e1efe6',
                            200: '#c4dfcf',
                            300: '#9bc6b0',
                            400: '#6ba889',
                            500: '#488c6b',
                            600: '#356f53',
                            700: '#2b5843',
                            800: '#1b3a2a',  /* Deep Forest Green */
                            900: '#153A26',  /* John Hay Green */
                        },
                        antique: {
                            400: '#D4AF37',
                            500: '#C0A062',  /* Antique Gold */
                            600: '#9E824A',
                        },
                        paper: {
                            DEFAULT: '#FCFBFA', /* Elegant warm white */
                            100: '#F5F3ED',
                            200: '#EAE6DB',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', 'serif'],
                        sans: ['"Inter"', 'sans-serif'],
                    },
                    boxShadow: {
                        'elegant': '0 10px 40px -10px rgba(21, 58, 38, 0.08)',
                    }
                },
            },
        }
    </script>

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #FCFBFA;
            /* Subtle damask/floral watermark background pattern */
            background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0C17.909 0 0 17.909 0 40c0 22.091 17.909 40 40 40 22.091 0 40-17.909 40-40C80 17.909 62.091 0 40 0zm0 3.2c20.324 0 36.8 16.476 36.8 36.8 0 20.324-16.476 36.8-36.8 36.8-20.324 0-36.8-16.476-36.8-36.8C3.2 19.676 19.676 3.2 40 3.2zm0 14.4c12.371 0 22.4 10.029 22.4 22.4 0 12.371-10.029 22.4-22.4 22.4-12.371 0-22.4-10.029-22.4-22.4 0-12.371 10.029-22.4 22.4-22.4zm0 3.2c-10.604 0-19.2 8.596-19.2 19.2 0 10.604 8.596 19.2 19.2 19.2 10.604 0 19.2-8.596 19.2-19.2 0-10.604-8.596-19.2-19.2-19.2z' fill='%23C0A062' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            color: #333;
        }

        /* ── ELEGANT CORNER SPIRALS ── */
        .corner-decor {
            position: fixed;
            width: 250px;
            height: 250px;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1.5' stroke-opacity='0.3' d='M0,0 C100,0 200,100 200,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='1' stroke-opacity='0.2' d='M0,20 C80,20 180,120 180,200'/%3E%3Cpath fill='none' stroke='%23C0A062' stroke-width='0.5' stroke-opacity='0.15' d='M0,40 C60,40 160,140 160,200'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            z-index: 0;
            pointer-events: none;
        }
        .corner-tl { top: 0; left: 0; transform: rotate(0deg); }
        .corner-tr { top: 0; right: 0; transform: rotate(90deg); }
        .corner-br { bottom: 0; right: 0; transform: rotate(180deg); }
        .corner-bl { bottom: 0; left: 0; transform: rotate(270deg); }

        /* ── CUSTOM RADIO BUTTONS ── */
        .custom-radio input[type="radio"] { position: absolute; opacity: 0; width: 0; height: 0; }
        .custom-radio .radio-mark {
            width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #C0A062;
            display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; cursor: pointer; background: #fff;
        }
        .custom-radio .radio-mark::after {
            content: ''; width: 10px; height: 10px; border-radius: 50%; background: #153A26; transform: scale(0); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .custom-radio input[type="radio"]:checked+.radio-mark { border-color: #153A26; background: #f2f7f4; }
        .custom-radio input[type="radio"]:checked+.radio-mark::after { transform: scale(1); }
        .custom-radio:hover .radio-mark { border-color: #153A26; }

        /* ── RATING CIRCLE BUTTONS (no numbers) ── */
        .rating-circle {
            width: 28px; height: 28px; border-radius: 50%; border: 1.5px solid #EAE6DB;
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; background: #fff;
        }
        .rating-circle:hover { border-color: #C0A062; background: #FDFBF8; }
        .rating-radio:checked ~ .rating-circle { background: #153A26; border-color: #C0A062; box-shadow: 0 4px 10px rgba(21, 58, 38, 0.2); }
        .rating-radio:checked ~ .rating-number { color: #153A26; font-weight: 800; }
        .rating-radio:focus ~ .rating-circle { box-shadow: 0 0 0 3px rgba(192, 160, 98, 0.2); } /* focus ring */

        /* ── ELEGANT FORM INPUTS ── */
        .elegant-input {
            width: 100%; padding: 12px 0; border: none; border-bottom: 1.5px solid #EAE6DB;
            background: transparent; color: #153A26; font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 500; transition: border-color 0.3s ease, box-shadow 0.3s ease; outline: none;
        }
        .elegant-input::placeholder { color: #A8A39C; font-weight: 400; font-style: italic; }
        .elegant-input:hover { border-bottom-color: #C0A062; }
        .elegant-input:focus { border-bottom-color: #153A26; }

        select.elegant-input {
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20'%3E%3Cpath fill='%23C0A062' d='M7 7l3 3 3-3' stroke='%23C0A062' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0 center;
        }

        textarea.elegant-input { resize: none; border: 1.5px solid #EAE6DB; padding: 12px 16px; border-radius: 4px; }
        textarea.elegant-input:hover { border-color: #C0A062; }
        textarea.elegant-input:focus { border-color: #153A26; border-bottom: 1.5px solid #153A26; }

        /* Calendar and Time picker icons */
        input[type="date"]::-webkit-calendar-picker-indicator, input[type="time"]::-webkit-calendar-picker-indicator { cursor: pointer; filter: sepia(100%) hue-rotate(10deg) saturate(200%) opacity(0.6); }

        /* ── SECTION STYLING ── */
        .hotel-section {
            background: #FFFFFF;
            border: 1px solid #EAE6DB;
            border-top: 4px solid #153A26; /* Deep green top border */
            box-shadow: 0 10px 40px -10px rgba(21, 58, 38, 0.05);
            position: relative;
        }

        /* ── ANIMATIONS ── */
        .reveal-section { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .reveal-section.visible { opacity: 1; transform: translateY(0); }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.8s linear infinite; }
        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-fade-up { opacity: 0; animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>

<body class="font-sans antialiased min-h-screen relative">

    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 py-12 md:py-16">

        <!-- ═══ HEADER ═══ -->
        <header class="text-center mb-14">
            <!-- Logo placeholder or decorative line -->
            <div class="w-12 h-1 bg-antique-500 mx-auto mb-6"></div>

            <h1 class="font-serif text-4xl sm:text-5xl md:text-5xl text-pine-900 mb-4 animate-fade-up tracking-wide">
                Pre and post feedback-form
            </h1>
            
            <p class="font-serif italic text-pine-700/80 text-sm md:text-[1rem] max-w-lg mx-auto leading-relaxed animate-fade-up" style="animation-delay: 0.1s;">
                We are honored to have hosted you. Your perspective is invaluable in refining our pursuit of excellence.
            </p>

            <!-- Decorative Divider -->
            <div class="flex items-center justify-center gap-3 mt-8 animate-fade-up" style="animation-delay: 0.2s;">
                <span class="w-16 h-[1px] bg-antique-500/40"></span>
                <span class="w-2 h-2 rotate-45 border border-antique-500 text-antique-500"></span>
                <span class="w-16 h-[1px] bg-antique-500/40"></span>
            </div>
        </header>

        <!-- ═══ FEEDBACK FORM ═══ -->
        <form id="feedbackForm" action="submit_feedback.php" method="POST" class="space-y-10">

            <!-- ═══ SECTION 1: EVENT OVERVIEW ═══ -->
            <section class="reveal-section hotel-section overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-center bg-paper-100/50">
                    <h2 class="font-serif text-pine-900 text-lg sm:text-xl tracking-[0.15em] uppercase">Event Details</h2>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-2">
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Event Name <span class="text-antique-500">*</span>
                            </label>
                            <input type="text" name="event_name" placeholder="e.g. Annual Gala 2026" required class="elegant-input">
                        </div>
                        <div>
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Event Date <span class="text-antique-500">*</span>
                            </label>
                            <input type="date" name="event_date" required class="elegant-input">
                        </div>
                        <div>
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Event Time <span class="text-antique-500">*</span>
                            </label>
                            <input type="time" name="event_time" required class="elegant-input">
                        </div>
                        <div class="md:col-span-2 mt-2">
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Location <span class="text-antique-500">*</span>
                            </label>
                            <select name="location" id="location_dropdown" onchange="toggleOtherLocation()" required class="elegant-input">
                                <option value="" disabled selected>Select an extraordinary venue</option>
                                <option value="19th T">19th T</option>
                                <option value="Adivay Hall">Adivay Hall</option>
                                <option value="St. Patricks">St. Patricks</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" id="other_location" name="other_location_text"
                                placeholder="Please specify the exact location" class="elegant-input mt-4 hidden">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 2: EVENT RATINGS ═══ -->
            <section class="reveal-section hotel-section overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-center bg-paper-100/50">
                    <h2 class="font-serif text-pine-900 text-lg sm:text-xl tracking-[0.15em] uppercase">Service & Experience</h2>
                </div>
                <div class="px-8 py-8">
                    <p class="text-sm font-serif italic text-pine-700/70 mb-8 text-center">
                        Kindly rate your experience based on the following criteria<br>
                        <span class="text-[0.7rem] font-sans font-medium uppercase tracking-widest not-italic mt-2 block">(1 = Needs Improvement, 5 = Exceptional)</span>
                    </p>

                    <!-- Row iterations -->
                    <?php
                    $ratingCategories = [
                        "event_planning" => "Event Planning",
                        "speaker_effectiveness" => "Speaker Effectiveness",
                        "venue_setup" => "Venue Ambiance & Setup",
                        "time_management" => "Time Management",
                        "audience_participation" => "Audience Participation",
                        "overall_experience" => "Overall Experience",
                        "food_beverages" => "Food & Beverages",
                        "technical_support" => "Technical Support",
                    ];

                    foreach ($ratingCategories as $name => $label): ?>
                        <div class="flex flex-col md:flex-row md:items-center justify-between py-4 border-b border-paper-200/60 last:border-b-0 hover:bg-paper-100/30 transition-colors px-2">
                            <span class="font-serif text-[1.05rem] text-pine-900 mb-3 md:mb-0"><?= $label ?></span>
                            <div class="flex items-center gap-4 sm:gap-6">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div class="flex flex-col items-center">
                                        <label class="cursor-pointer group flex flex-col items-center">
                                            <input type="radio" name="<?= $name ?>" value="<?= $i ?>" <?= $i === 1 ? "required" : "" ?> class="rating-radio sr-only">
                                            <span class="text-[0.7rem] font-bold text-pine-700/60 mb-1 transition-all duration-300 rating-number group-hover:text-pine-900"><?= $i ?></span>
                                            <span class="rating-circle"></span>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Scale legend -->
                    <div class="flex justify-between mt-6 px-2">
                        <span class="text-[0.6rem] font-bold text-antique-500 uppercase tracking-widest">1 = Needs Impr.</span>
                        <span class="text-[0.6rem] font-bold text-antique-500 uppercase tracking-widest">5 = Exceptional</span>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 3: OPEN-ENDED QUESTIONS ═══ -->
            <section class="reveal-section hotel-section overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-center bg-paper-100/50">
                    <h2 class="font-serif text-pine-900 text-lg sm:text-xl tracking-[0.15em] uppercase">Personal Sentiments</h2>
                </div>
                <div class="px-8 py-8 space-y-8">
                    <div>
                        <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-3">
                            What aspects of the event were most remarkable?
                        </label>
                        <textarea name="effective_aspects" rows="3" placeholder="We would love to hear what delighted you..." class="elegant-input shadow-inner bg-paper-100/30"></textarea>
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-3">
                            How might we elevate future experiences?
                        </label>
                        <textarea name="improvement_suggestions" rows="3" placeholder="Share your suggestions for our refinement..." class="elegant-input shadow-inner bg-paper-100/30"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 4: FUTURE PARTICIPATION & ADDITIONAL FEEDBACK ═══ -->
            <section class="reveal-section hotel-section overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-center bg-paper-100/50">
                    <h2 class="font-serif text-pine-900 text-lg sm:text-xl tracking-[0.15em] uppercase">Looking Ahead</h2>
                </div>
                <div class="px-8 py-8 space-y-8">
                    <div>
                        <p class="text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-4">
                            Would you grace us with your presence at future events? <span class="text-antique-500">*</span>
                        </p>
                        <div class="flex gap-10 flex-wrap">
                            <?php foreach (['Yes', 'No', 'Maybe'] as $opt): ?>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="custom-radio">
                                        <input type="radio" name="participate_future" value="<?= $opt ?>" required>
                                        <span class="radio-mark"></span>
                                    </div>
                                    <span class="text-[0.95rem] font-medium text-pine-800 transition-colors"><?= $opt ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="border-t border-paper-200 pt-8">
                        <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-3">
                            Additional Reflections
                        </label>
                        <textarea name="additional_feedback" rows="3" placeholder="Any final thoughts you wish to bestow..." class="elegant-input shadow-inner bg-paper-100/30"></textarea>
                    </div>
                </div>
            </section>

            <!-- ═══ SECTION 5: ATTENDEE INFORMATION ═══ -->
            <section class="reveal-section hotel-section overflow-hidden">
                <div class="px-8 py-5 border-b border-paper-200 flex items-center justify-center bg-paper-100/50">
                    <h2 class="font-serif text-pine-900 text-lg sm:text-xl tracking-[0.15em] uppercase">Guest Registry</h2>
                </div>
                <div class="px-8 py-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="md:col-span-2">
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Group Name <span class="text-antique-500">*</span>
                            </label>
                            <input type="text" name="attendee_name" placeholder="Enter the name of your esteemed group" required class="elegant-input">
                        </div>
                        <div>
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Gmail <span class="text-antique-500">*</span>
                            </label>
                            <input type="email" name="email" placeholder="yourgroup@gmail.com" required class="elegant-input">
                        </div>
                        <div>
                            <label class="block text-[0.65rem] font-bold text-pine-700 uppercase tracking-widest mb-1">
                                Contact Number <span class="text-antique-500">*</span>
                            </label>
                            <input type="tel" name="contact_no" placeholder="+63 917 123 4567" required class="elegant-input">
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══ SUBMIT BUTTON ═══ -->
            <div class="reveal-section text-center pt-4 pb-12">
                <button type="submit" id="submitBtn"
                    class="group relative inline-flex items-center justify-center gap-3 px-10 py-4 font-serif text-[0.9rem] uppercase tracking-widest border border-pine-900 transition-all duration-500 w-full sm:w-auto sm:min-w-[280px] bg-pine-900 text-white hover:bg-white hover:text-pine-900 hover:border-antique-500 overflow-hidden shadow-elegant">
                    <span class="absolute inset-0 bg-antique-500/10 -translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out z-0"></span>
                    <div id="btnLoader" class="hidden relative z-10">
                        <svg class="w-5 h-5 spinner text-current" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                    </div>
                    <span id="btnText" class="relative z-10">Submit Evaluation</span>
                    <svg id="btnArrow" class="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>

        </form>
    </div>

    <!-- ═══ FOOTER ═══ -->
    <footer class="text-center py-10 border-t border-paper-200/50 bg-paper-100/30">
        <p class="font-serif text-xl text-pine-900 mb-2">Event Evaluation System</p>
        <p class="text-pine-700/60 text-xs font-medium uppercase tracking-[0.2em]">Excellence is an Art</p>
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
        }, { threshold: 0.05, rootMargin: "0px 0px -50px 0px" });

        document.querySelectorAll(".reveal-section").forEach(function (s) {
            observer.observe(s);
        });
    </script>
</body>

</html>
