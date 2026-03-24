<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * ADMIN PRINTABLE REPORTS
 * Event Feedback System
 * ═══════════════════════════════════════════════════════════════
 */
session_start();
require_once __DIR__ . "/../config.php";

if (!isset($_SESSION["superadmin_logged_in"]) || $_SESSION["superadmin_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Super Admin</title>
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
        
        .lodge-input { width: 100%; padding: 12px 16px; border-radius: 4px; border: 1px solid #EAE6DB; background: transparent; color: #153A26; font-family: 'Inter', sans-serif; font-size: 0.95rem; transition: border-color 0.3s ease; outline: none; }
        .lodge-input::placeholder { color: #A8A39C; font-style: italic; }
        .lodge-input:focus { border-color: #153A26; background: #fff; box-shadow: 0 2px 8px rgba(21,58,38,0.05); }
        .lodge-input:focus { border-color: #C9A96E; background: rgba(255, 255, 255, 0.06); box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1); }
        .lodge-input::placeholder { color: rgba(255, 255, 255, 0.2); }
        select.lodge-input option { background-color: #0f1333; color: #ffffff; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(15, 19, 51, 0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(201, 169, 110, 0.4); border-radius: 99px; }

        @keyframes fadeUp { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }
        .fade-up { opacity: 0; animation: fadeUp 0.6s ease-out forwards; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { width: 32px; height: 32px; border: 3px solid rgba(201, 169, 110, 0.1); border-top-color: #C9A96E; border-radius: 50%; animation: spin 0.8s linear infinite; }

        /* ═══ PRINT STYLES ═══ */
        @media print {
            * { color: #000 !important; background: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            body { background: #fff !important; font-size: 10pt; font-family: 'Inter', sans-serif; }
            nav, .no-print, footer, #controlsPanel, .corner-decor { display: none !important; }
            .glass-card-premium { background: #fff !important; border: 1px solid #ddd !important; border-top: 1px solid #ddd !important; box-shadow: none !important; }
            #reportContent { display: block !important; position: static !important; }
            
            .print-header { display: block !important; text-align: center; margin-bottom: 30px; border-bottom: none; }
            .print-header img { height: 80px; margin: 0 auto 20px auto; display: block; }
            .print-header h1 { font-family: 'Playfair Display', serif; font-size: 18pt; color: #153A26 !important; margin: 0 0 10px 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; }
            .print-header p { font-size: 10pt; color: #666 !important; margin: 4px 0; }
            
            .report-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            .report-table th { background: #153A26 !important; color: #fff !important; padding: 8px 12px; text-align: left; font-size: 9pt; text-transform: uppercase; letter-spacing: 0.05em; }
            .report-table td { padding: 6px 12px; border-bottom: 1px solid #eee; font-size: 9pt; }
            .report-table tr:nth-child(even) td { background: #f9f9f9 !important; }
            
            .report-section { page-break-inside: avoid; margin-bottom: 30px; }
            .report-section h3 { font-family: 'Playfair Display', serif; font-size: 13pt; color: #153A26 !important; border-bottom: 2px solid #C9A96E; padding-bottom: 8px; margin-bottom: 12px; text-align: left; font-weight: 700; }
            
            .stat-box { display: inline-block; width: 18%; text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin: 0 0.5%; }
            .stat-box .stat-val { font-size: 20pt; font-weight: 700; color: #153A26 !important; font-family: 'Playfair Display', serif; }
            .stat-box .stat-label { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.1em; color: #999 !important; }
            
            .score-excellent { color: #059669 !important; font-weight: 600; }
            .score-good { color: #d97706 !important; font-weight: 600; }
            .score-poor { color: #dc2626 !important; font-weight: 600; }
            
            .comment-card { border: 1px solid #eee; padding: 12px 16px; margin-bottom: 12px; border-radius: 4px; page-break-inside: avoid; border-left: 3px solid #C9A96E; }
            .comment-card .guest-info { font-size: 8pt; color: #999 !important; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px; }
            .comment-card .comment-text { font-size: 9pt; font-style: italic; color: #333 !important; }
            .comment-page { display: block !important; }
            
            .print-footer { text-align: center; font-size: 7pt; color: #aaa !important; margin-top: 30px; padding-top: 10px; border-top: 1px solid #eee; display: block !important; }
            .print-only-table { display: block !important; margin-top: 10px; }
            #reportModal { display: none !important; }
        }

        /* ═══ SCREEN-ONLY REPORT STYLES ═══ */
        @media screen {
            .print-header { display: none; }
            .report-table { width: 100%; border-collapse: separate; border-spacing: 0; }
            .report-table th { background: rgba(242, 247, 244, 1); color: #153A26; padding: 16px 24px; text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.2em; font-weight: 900; border-bottom: 1px solid #EAE6DB; }
            .report-table td { padding: 16px 24px; border-bottom: 1px solid #EAE6DB; color: #153A26; font-size: 0.9rem; }
            .report-table tr:hover td { background: rgba(245, 243, 237, 1); }
            .report-section h3 { font-family: 'Playfair Display', serif; color: #153A26; font-size: 1.5rem; font-weight: 700; border-bottom: 1px solid rgba(201, 169, 110, 0.2); padding-bottom: 12px; margin-bottom: 24px; }
            .stat-box { text-align: center; padding: 24px; background: #FFFFFF; border: 1px solid #EAE6DB; border-radius: 1.5rem; }
            .stat-box .stat-val { font-size: 2.25rem; font-weight: 700; color: #153A26; font-family: 'Playfair Display', serif; }
            .stat-box .stat-label { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.2em; color: rgba(21, 58, 38, 0.7); font-weight: 900; margin-top: 8px; }
            .score-excellent { color: #059669; }
            .score-good { color: #d97706; }
            .score-poor { color: #dc2626; }

            .comment-card { background: #FFFFFF; border: 1px solid #EAE6DB; padding: 24px; margin-bottom: 16px; border-radius: 1.5rem; }
            .comment-card .guest-info { font-size: 0.7rem; color: rgba(21, 58, 38, 0.7); text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid #EAE6DB; padding-bottom: 12px; margin-bottom: 16px; font-weight: 700; }
            .comment-card .comment-text { font-size: 0.95rem; font-style: italic; color: #153A26; line-height: 1.7; }
            .print-footer { display: none; }
            .print-only-table { display: none; }

            /* Modal Styles */
            .modal-backdrop { position: fixed; inset: 0; z-index: 50; background: rgba(21, 58, 38, 0.4); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; }
            .modal-backdrop.active { display: flex; opacity: 1; pointer-events: auto; }
            .modal-container { background: #FFFFFF; border: 1px solid #EAE6DB; border-top: 4px solid #153A26; border-radius: 2rem; width: 100%; max-width: 800px; transform: translateY(-30px); opacity: 0; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 10px 40px rgba(0,0,0,0.1); display: flex; flex-direction: column; overflow: hidden; }
            .modal-backdrop.active .modal-container { transform: translateY(0); opacity: 1; }
            .modal-header { padding: 24px 32px; border-bottom: 1px solid #EAE6DB; display: flex; justify-content: space-between; align-items: center; background: rgba(245, 243, 237, 1); }
            .modal-body { padding: 32px; max-height: 70vh; overflow-y: auto; }
        }
    </style>
</head>
<body class="font-sans text-pine-900 min-h-screen relative overflow-x-hidden">
    
    <!-- Elegant Corner Decor Rings -->
    <div class="corner-decor corner-tl hidden md:block"></div>
    <div class="corner-decor corner-tr hidden md:block"></div>
    <div class="corner-decor corner-br hidden md:block"></div>
    <div class="corner-decor corner-bl hidden md:block"></div>
    

    <!-- Nav -->
    <nav class="nav-glass sticky top-0 z-50 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-antique-400 to-antique-500 flex items-center justify-center shadow-lg shadow-antique-400/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h1 class="font-serif text-xl font-bold text-pine-900 leading-tight">Super Admin Panel</h1>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-[0.2em] font-black">Strategic Reporting</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="index.php" class="text-xs font-black uppercase tracking-widest text-pine-700/70 hover:text-pine-900 transition-colors">Manage Admins</a>
                <a href="analytics.php" class="text-xs font-black uppercase tracking-widest text-pine-700/70 hover:text-pine-900 transition-colors">Analytics</a>
                <a href="reports.php" class="text-xs font-black uppercase tracking-widest text-antique-400 border-b-2 border-antique-400 pb-1">Reports</a>
                <span class="text-pine-700/20">|</span>
                <a href="logout.php" class="px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500/20 transition-all text-[0.65rem] font-bold uppercase tracking-widest">Logout</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-12 relative z-10">
        <!-- Controls -->
        <div class="glass-card-premium rounded-3xl p-8 mb-8 fade-up" id="controlsPanel">
            <div class="flex flex-wrap items-end justify-between gap-8">
                <!-- Inputs -->
                <div class="flex flex-wrap items-end gap-6">
                    <div class="flex flex-col" style="min-width: 180px;">
                        <label class="block text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em] mb-3">Temporal Preset</label>
                        <select id="presetSelect" class="lodge-input w-full cursor-pointer">
                            <option value="today">Daily (Today)</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="week" selected>Weekly (7 Days)</option>
                            <option value="month">Monthly (30 Days)</option>
                            <option value="year">Yearly Performance</option>
                            <option value="all">Historical Archive</option>
                            <option value="custom" hidden>Custom Range</option>
                        </select>
                    </div>
                    <div class="flex flex-col" style="min-width: 160px;">
                        <label class="block text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em] mb-3">Commencement</label>
                        <input type="date" id="dateFrom" class="lodge-input w-full">
                    </div>
                    <div class="flex flex-col" style="min-width: 160px;">
                        <label class="block text-[0.65rem] font-black text-antique-400/60 uppercase tracking-[0.2em] mb-3">Conclusion</label>
                        <input type="date" id="dateTo" class="lodge-input w-full">
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex flex-wrap items-end gap-4 lg:ml-auto">
                    <button id="btnGenerate" class="px-8 py-3 rounded-xl font-bold text-[0.7rem] uppercase tracking-[0.2em] transition-all duration-500 hover:shadow-[0_0_20px_rgba(201,169,110,0.3)] hover:-translate-y-1 bg-gradient-to-br from-antique-400 to-antique-500 text-white">
                        Compile Intelligence
                    </button>
                    <button id="btnPrint" class="px-8 py-3 rounded-xl font-bold text-[0.7rem] uppercase tracking-[0.2em] border border-antique-400/30 text-antique-400 hover:bg-antique-400/10 transition-all flex items-center gap-3 opacity-0 invisible" style="transition: opacity 0.5s ease, visibility 0.5s ease;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Manifest
                    </button>
                </div>
            </div>
        </div>

        <div id="loadingReport" class="no-print text-center py-24" style="display:none">
            <div class="spinner mx-auto mb-6"></div>
            <p class="text-pine-700/70 text-[0.65rem] font-black uppercase tracking-[0.3em] animate-pulse">Synchronizing Data Streams...</p>
        </div>

        <div id="emptyState" class="no-print glass-card-premium rounded-[2.5rem] p-24 text-center fade-up">
            <div class="w-20 h-20 rounded-[1.5rem] bg-paper-100/50 flex items-center justify-center mx-auto mb-8 border border-paper-200">
                <svg class="w-10 h-10 text-antique-400/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <h3 class="font-serif text-3xl font-bold text-pine-900 mb-4">Awaiting Parameters</h3>
            <p class="text-pine-700/60 text-sm max-w-md mx-auto leading-loose">Select a temporal period or custom date range to initiate report synthesis and printable manifest generation.</p>
        </div>

        <div id="reportContent" style="display:none" class="fade-up"></div>
    </div>

    <!-- Data Modal -->
    <div id="reportModal" class="modal-backdrop no-print">
        <div class="modal-container">
            <div class="modal-header">
                <div>
                    <h3 id="modalTitle" class="font-serif text-2xl font-bold text-pine-900">Details</h3>
                    <p class="text-antique-400/60 text-[0.65rem] uppercase tracking-widest font-black mt-1">Granular Metrics</p>
                </div>
                <button type="button" id="closeModalBtn" class="w-10 h-10 rounded-full bg-paper-100/50 flex items-center justify-center text-pine-700/70 hover:text-pine-900 hover:bg-paper-100/50 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modalContent" class="modal-body"></div>
        </div>
    </div>

    <script>
    (function() {
        var dateFrom = document.getElementById('dateFrom');
        var dateTo = document.getElementById('dateTo');
        var btnGenerate = document.getElementById('btnGenerate');
        var btnPrint = document.getElementById('btnPrint');
        var reportContent = document.getElementById('reportContent');
        var loadingEl = document.getElementById('loadingReport');
        var emptyState = document.getElementById('emptyState');

        function formatDate(d) {
            var year = d.getFullYear();
            var month = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function displayDate(dateStr) {
            if (!dateStr || dateStr === '1970-01-01' || dateStr === '') return 'All Time';
            var d = new Date(dateStr + 'T00:00:00');
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        }

        function scoreClass(val) {
            if (val >= 4) return 'score-excellent';
            if (val >= 3) return 'score-good';
            return 'score-poor';
        }

        function setPreset(type) {
            var today = new Date();
            var from, to;
            switch (type) {
                case 'today': from = to = formatDate(today); break;
                case 'yesterday': var yd = new Date(today); yd.setDate(yd.getDate() - 1); from = to = formatDate(yd); break;
                case 'week': to = formatDate(today); var w = new Date(today); w.setDate(w.getDate() - 6); from = formatDate(w); break;
                case 'month': to = formatDate(today); var m = new Date(today); m.setDate(m.getDate() - 29); from = formatDate(m); break;
                case 'year': to = formatDate(today); var y = new Date(today); y.setFullYear(y.getFullYear() - 1); from = formatDate(y); break;
                case 'all': from = '2020-01-01'; to = formatDate(today); break;
            }
            dateFrom.value = from;
            dateTo.value = to;
        }

        document.getElementById('presetSelect').addEventListener('change', function(e) {
            var preset = e.target.value;
            if (preset === 'custom') return;
            setPreset(preset);
            generateReport(dateFrom.value, dateTo.value);
        });

        dateFrom.addEventListener('change', function() { document.getElementById('presetSelect').value = 'custom'; });
        dateTo.addEventListener('change', function() { document.getElementById('presetSelect').value = 'custom'; });

        btnGenerate.addEventListener('click', function() {
            if (!dateFrom.value || !dateTo.value) { alert('Selection incomplete. Please specify temporal boundaries.'); return; }
            generateReport(dateFrom.value, dateTo.value);
        });

        btnPrint.addEventListener('click', function() { window.print(); });

        function generateReport(from, to) {
            emptyState.style.display = 'none';
            btnGenerate.disabled = true; btnGenerate.style.opacity = '0.5'; btnGenerate.innerText = 'Compiling...';
            btnPrint.classList.add('opacity-0', 'invisible');

            if (reportContent.innerHTML.trim() !== '') {
                reportContent.classList.add('opacity-40');
                loadingEl.style.display = 'none';
            } else {
                reportContent.style.display = 'none'; loadingEl.style.display = 'block';
            }

            fetch('../admin/api/report_data.php?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to))
                .then(res => res.json())
                .then(data => {
                    if (data.error) { alert(data.error); resetState(); return; }
                    renderReport(data);
                    
                    loadingEl.style.display = 'none';
                    reportContent.style.display = 'block'; reportContent.classList.remove('opacity-40');
                    btnPrint.classList.remove('opacity-0', 'invisible');
                    resetState();
                })
                .catch(err => {
                    console.error(err); resetState(); loadingEl.style.display = 'none';
                    if (reportContent.innerHTML.trim() === '') emptyState.style.display = 'block';
                    alert('Data synchronization failed. Please re-initiate.');
                });
        }
        
        function resetState() {
            btnGenerate.disabled = false; btnGenerate.style.opacity = '1'; btnGenerate.innerText = 'Compile Intelligence';
        }

        function buildModalSection(title, desc, tblHtml) {
            var sHtml = '<div class="report-section">';
            sHtml += '<h3>' + title + '</h3>';
            sHtml += '<div class="no-print glass-card-premium rounded-2xl p-6 flex justify-between items-center group hover:border-antique-400/40 transition-all duration-500">';
            sHtml += '<div><div class="text-sm font-bold text-pine-900">Detailed Matrix</div><div class="text-xs text-pine-700/70 mt-1">' + desc + '</div></div>';
            sHtml += '<button type="button" class="btn-open-modal px-6 py-2 rounded-xl bg-antique-400/10 hover:bg-antique-400/20 border border-antique-400/20 text-antique-400 text-[0.65rem] font-black uppercase tracking-widest transition-all" data-title="' + escapeHtml(title) + '" data-content="' + encodeURIComponent(tblHtml) + '">Analyze Data</button>';
            sHtml += '</div>';
            sHtml += '<div class="print-only-table">' + tblHtml + '</div>';
            sHtml += '</div>';
            return sHtml;
        }

        function escapeHtml(str) { return (str || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;"); }

        function renderReport(data) {
            var s = data.summary;
            var now = new Date();
            var generatedAt = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();

            var html = '';

            // Print Header
            html += '<div class="print-header">';
            html += '<img src="../img/logo.png" alt="John Hay Hotels Logo">';
            html += '<h1>Strategic Feedback Report</h1>';
            html += '<p>Generated: ' + generatedAt + '</p>';
            html += '<p>Intelligence Period: ' + displayDate(data.date_from) + ' — ' + displayDate(data.date_to) + '</p>';
            html += '</div>';

            // Summary Stats
            html += '<div class="report-section">';
            html += '<h3>Executive Summary</h3>';
            html += '<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">';
            html += '<div class="stat-box"><div class="stat-val">' + (s.total_responses || 0) + '</div><div class="stat-label">Submissions</div></div>';
            html += '<div class="stat-box"><div class="stat-val ' + scoreClass(s.avg_nps) + '">' + (s.avg_nps !== null ? s.avg_nps : '—') + '</div><div class="stat-label">Avg Rate</div></div>';
            html += '<div class="stat-box"><div class="stat-val text-red-400">' + (s.poor || 0) + '</div><div class="stat-label">Critical</div></div>';
            html += '<div class="stat-box"><div class="stat-val text-antique-400">' + (s.good || 0) + '</div><div class="stat-label">Moderate</div></div>';
            html += '<div class="stat-box"><div class="stat-val text-emerald-400">' + (s.excellent || 0) + '</div><div class="stat-label">Optimal</div></div>';
            html += '</div></div>';

            // Categories
            var catTable = '<table class="report-table"><thead><tr><th>Strategic Category</th><th style="text-align:center">Performance Index</th></tr></thead><tbody>';
            (data.category_averages || []).forEach(function(c) {
                var cls = scoreClass(c.avg);
                catTable += '<tr><td>' + c.label + '</td><td style="text-align:center" class="' + cls + ' font-bold">' + (c.avg > 0 ? c.avg.toFixed(1) + ' / 5.0' : 'N/A') + '</td></tr>';
            });
            catTable += '</tbody></table>';
            html += buildModalSection('Category Performance', 'Vertical focus performance metrics across system checkpoints.', catTable);

            // Event Breakdown
            if (data.event_breakdown && data.event_breakdown.length > 0) {
                var evtTable = '<table class="report-table"><thead><tr><th>Entity Name</th><th style="text-align:center">Response Volume</th></tr></thead><tbody>';
                data.event_breakdown.forEach(function(e) {
                    evtTable += '<tr><td>' + escapeHtml(e.event_name) + '</td><td style="text-align:center" class="font-bold text-pine-700/80">' + e.count + ' Units</td></tr>';
                });
                evtTable += '</tbody></table>';
                html += buildModalSection('Entity Distribution', 'Categorization of qualitative data points by originating entity.', evtTable);
            }

            // Qualitative Feedback
            var validComments = data.comments || [];
            if (validComments.length > 0) {
                html += '<div class="report-section">';
                html += '<h3>Qualitative Intelligence</h3>';
                
                var maxPerPage = 5;
                var totalPages = Math.ceil(validComments.length / maxPerPage);
                
                html += '<div id="comments-wrapper">';
                for (var p = 1; p <= totalPages; p++) {
                    var displayStyle = p === 1 ? 'block' : 'none';
                    html += '<div class="comment-page" id="comment-page-' + p + '" style="display:' + displayStyle + ';">';
                    
                    var startIdx = (p - 1) * maxPerPage;
                    var endIdx = Math.min(startIdx + maxPerPage, validComments.length);
                    
                    for (var i = startIdx; i < endIdx; i++) {
                        var c = validComments[i];
                        html += '<div class="comment-card">';
                        html += '<div class="guest-info">' + escapeHtml(c.guest_name || 'Anonymous') + ' • ' + escapeHtml(c.event || 'System Event') + ' • <span class="' + scoreClass(c.overall_rating) + '">' + (c.overall_rating || '—') + '/5</span></div>';
                        
                        if (c.effective) html += '<div class="comment-text mb-4"><span class="text-[0.6rem] font-black text-emerald-400 uppercase tracking-widest block mb-1">Effective Vectors</span>' + escapeHtml(c.effective).replace(/\n/g, '<br>') + '</div>';
                        if (c.improvement) html += '<div class="comment-text mb-4"><span class="text-[0.6rem] font-black text-antique-400 uppercase tracking-widest block mb-1">Development Areas</span>' + escapeHtml(c.improvement).replace(/\n/g, '<br>') + '</div>';
                        if (c.additional) html += '<div class="comment-text"><span class="text-[0.6rem] font-black text-pine-700/60 uppercase tracking-widest block mb-1">Contextual Background</span>' + escapeHtml(c.additional).replace(/\n/g, '<br>') + '</div>';
                        html += '</div>';
                    }
                    html += '</div>';
                }
                html += '</div>';
                
                if (totalPages > 1) {
                    html += '<div class="no-print mt-8 flex items-center justify-between">';
                    html += '<div class="text-pine-700/40 text-[0.6rem] font-black uppercase tracking-[0.2em]">Fragment <span id="comments-current-page" class="text-antique-400">1</span> of ' + totalPages + '</div>';
                    html += '<div class="flex gap-3">';
                    html += '<button id="btn-prev-comments" class="px-5 py-2 rounded-xl bg-paper-100/50 hover:bg-paper-100/50 text-pine-700/80 text-[0.65rem] font-black uppercase tracking-widest disabled:opacity-20 transition-all" disabled>Previous</button>';
                    html += '<button id="btn-next-comments" class="px-5 py-2 rounded-xl bg-antique-400/10 hover:bg-antique-400/20 text-antique-400 text-[0.65rem] font-black uppercase tracking-widest transition-all">Next Fragment</button>';
                    html += '</div>';
                    html += '</div>';
                }
                html += '</div>';

                setTimeout(() => {
                    var cp = 1;
                    var pgText = document.getElementById('comments-current-page');
                    var bPrev = document.getElementById('btn-prev-comments');
                    var bNext = document.getElementById('btn-next-comments');
                    
                    if(bPrev && bNext) {
                        const updateP = () => {
                            for(var p=1; p<=totalPages; p++) document.getElementById('comment-page-'+p).style.display = (p===cp)?'block':'none';
                            pgText.innerText = cp;
                            bPrev.disabled = (cp === 1);
                            bNext.disabled = (cp === totalPages);
                        };
                        bPrev.onclick = () => { if(cp>1){ cp--; updateP(); } };
                        bNext.onclick = () => { if(cp<totalPages){ cp++; updateP(); } };
                    }
                }, 100);
            }

            // Print Footer
            html += '<div class="print-footer">';
            html += 'Strategic Intelligence Manifest • Generated via Event Feedback System • ' + generatedAt;
            html += '</div>';

            reportContent.innerHTML = html;
            
            // Modal Logic
            var modal = document.getElementById('reportModal');
            var modalTitle = document.getElementById('modalTitle');
            var modalContent = document.getElementById('modalContent');
            var closeBtn = document.getElementById('closeModalBtn');
            
            closeBtn.onclick = () => { modal.classList.remove('active'); document.body.style.overflow = ''; };
            modal.onclick = (e) => { if(e.target === modal) closeBtn.onclick(); };
            
            document.querySelectorAll('.btn-open-modal').forEach(btn => {
                btn.onclick = function() {
                    modalTitle.innerHTML = this.getAttribute('data-title');
                    modalContent.innerHTML = decodeURIComponent(this.getAttribute('data-content'));
                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                };
            });
        }

        // Init preset
        setPreset('month');
        generateReport(dateFrom.value, dateTo.value);

    })();
    </script>
</body>
</html>
